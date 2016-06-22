<?php

declare(strict_types = 1);

namespace WDB\Identity\Managers;

use WDB\Db\Database;
use WDB\Exceptions\ApplicationException;
use WDB\Models\BindingModels\ChangePasswordBindingModel;
use WDB\Models\BindingModels\LoginUserBindingModel;
use WDB\Models\BindingModels\RegisterUserBindingModel;

class UserManager implements IUserManager
{
    /**
     * @var IUserManager
     */
    private static $_inst = null;

    /**
     * @var Database
     */
    private $_db;

    private function __construct()
    {
        $this->_db = Database::getInstance('default');
    }

    static function getInstance() : IUserManager
    {
        if(self::$_inst == null)
        {
            self::$_inst = new UserManager();
        }

        return self::$_inst;
    }

    /**
     * @param RegisterUserBindingModel $model
     * @return int
     * @throws \Exception
     */
    public function register(RegisterUserBindingModel $model) : int
    {
        if(self::isExistingUsername($model->getUsername()))
        {
            $_SESSION['binding-model-errors'][] = 'username already exists';
            throw new ApplicationException("");
        }
        if($model->getPassword() !== $model->getConfirmPassword())
        {
            $_SESSION['binding-model-errors'][] = 'Passwords do not match';
            throw new ApplicationException("");
        }

        $response = $this->_db->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)")
            ->execute(
                [
                    $model->getUsername(),
                    password_hash($model->getPassword(), PASSWORD_DEFAULT),
                    $model->getEmail()
                ]
            );


        if ($response->rowCount() < 1){
            throw new ApplicationException ('Cannot register user');
        }

        return intval($this->_db->rowCount());
    }

    /**
     * @param LoginUserBindingModel $model
     * @return int
     * @throws \Exception
     */
    public function login(LoginUserBindingModel $model) : int
    {
        $response = $this->_db->prepare("SELECT id, username, password FROM users WHERE username = ?")
            ->execute([$model->getUsername()]);
        if($response->rowCount() > 0)
        {
            $user = $response->fetchRowAssoc();

            if(password_verify($model->getPassword(), $user['password']))
            {
                return intval($user['id']);
            }

            $_SESSION['binding-model-errors'][] = 'Wrong username or password';
            throw new ApplicationException('Wrong username or password');
        }
    }

    /**
     * @param EditUserBindingModel $model
     * @return bool
     */
    public function edit(EditUserBindingModel $model) :bool
    {
        // TODO: Implement edit() method.
    }

    /**
     * @param ChangePasswordBindingModel $model
     * @return bool
     * @throws \Exception
     */
    public  function changePassword(ChangePasswordBindingModel $model) : bool
    {
        if ($model->getPassword() != $model->getConfirmPassword()){
            $_SESSION['binding-model-errors'][] = "Passwords does not match!";
            throw new ApplicationException("Passwords does not match!");
        }

        $response = $this->_db->prepare("SELECT password FROM users WHERE username = ?")
            ->execute([ $_SESSION['user_id' ]]);

        $password = $response->fetch('password');
        if(!password_verify($model->getCurrentPassword(), $password))
        {
            $_SESSION['binding-model-errors'][] = "Wrong Password";
            throw new ApplicationException('');
        }

        $response = $this->_db->prepare("UPDATE users SET password = ? WHERE id = ?")
            ->execute(
                [
                    password_hash($model->getPassword(), PASSWORD_DEFAULT),
                    $_SESSION['user_id']
                ]
            );

        return $response->rowCount() > 0;
    }

    /**
     * @param string $username
     * @return bool
     */
    public function isExistingUsername(string $username) :bool
    {
        $response = $this->_db->prepare("SELECT id FROM users WHERE username = ?")
            ->execute([ $username ]);

        return $response->rowCount() > 0;
    }

    /**
     * @param string $email
     * @return bool
     */
    public function isExistingEmail(string $email) :bool
    {
        $response = $this->_db->prepare("SELECT id FROM users WHERE email = ?")
            ->execute([ $email ]);

        return $response->rowCount() > 0;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function getInfo(string $id) : array
    {
        $response = $this->_db->prepare("SELECT id, username, email FROM users WHERE id = ?")
            ->execute([ $id ])
            ->fetchAllAssoc();

        return $response;
    }

    /**
     * @param int $userId
     * @param int $roleId
     * @return bool
     */
    public function addToRole(int $userId, int $roleId) :bool
    {
        $response = $this->_db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)")
            ->execute([$userId, $roleId]);

        return $response->rowCount() > 0;
    }

    /**
     * @param string $username
     * @param string $roleName
     * @return bool
     */
    public function isInRoleByUsername(string $username, string $roleName) : bool
    {
        $sql = "SELECT u.id FROM user_roles as ur
             INNER JOIN users AS u ON ur.user_id = u.id
             INNER JOIN roles AS r ON ur.role_id = r.id
             WHERE u.username = ? and r.name = ?";

        $response = $this->_db->prepare($sql)
            ->execute([ $username, $roleName ]);

        return $response->rowCount() > 0;
    }

    /**
     * @param int $id
     * @param string $roleName
     * @return bool
     */
    public function isInRoleById(string $id, string $roleName) : bool
    {
        $sql = "SELECT u.id FROM user_roles as ur
             INNER JOIN users AS u ON ur.user_id = u.id
             INNER JOIN roles AS r ON ur.role_id = r.id
             WHERE u.username = ? and r.name = ?";

        $response = $this->_db->prepare($sql)
            ->execute([ $id, $roleName ]);

        return $response->rowCount() > 0;
    }
}