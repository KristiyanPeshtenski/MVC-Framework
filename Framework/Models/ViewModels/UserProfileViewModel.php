<?php
declare(strict_types=1);

namespace Framework\Models\ViewModels;

class UserProfileViewModel
{
    private $id;
    private $username;
    private $fullName;

    /**
     * @return mixed
     */
    public function isLogged() : bool
    {
        return $this->username !== null;
    }

    /**
     * UserProfileViewModel constructor.
     * @param string $username
     * @param string $id
     * @param string $fullName
     */
    public function __construct(string $username = "", string $id = "", string $fullName = "")
    {
        $this->setId($id)
            ->setUsername($username)
            ->setFullName($fullName);
    }

    /**
     * @return string
     */
    public function getId() : string
    {
        if ($this->id !== null) {
            return $this->id;
        }

        return '';
    }

    /**
     * @param string $id
     * @return UserProfileViewModel
     */
    public function setId(string $id) : UserProfileViewModel
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername() : string
    {
        if ($this->username !== null) {
            return $this->username;
        }

        return '';
    }

    /**
     * @param string $username
     * @return UserProfileViewModel
     */
    public function setUsername(string $username) : UserProfileViewModel
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getFullName() : string {
        return $this->fullName;
    }

    /**
     * @param string $fullname
     * @return UserProfileViewModel
     */
    public function setFullName(string $fullName) {
        $this->fullName = $fullName;
        return $this;
    }
}