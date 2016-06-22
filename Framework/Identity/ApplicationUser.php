<?php

declare(strict_types = 1);

namespace WDB\Identity;

/**
 * Class ApplicationUser
 * @package WDB\Identity
 * @Table users
 */
abstract class ApplicationUser implements IApplicationUser
{
    /**
     * @Column id
     * @Type INT
     * @Length 11
     * @Primary
     * @Increment
     */
    protected $id;

    /**
     * @Column username
     * @Type Nvarchar
     * @Length 255
     * @Unique
     */
    protected $username;

    /**
     * @Column email
     * @Type NVARCHAR
     * @Length 255
     * @Unique
     */
    protected $email;

    /**
     * @Column password
     * @Type NVARCHAR
     * @Length 255
     */
    protected $password;

    protected function __construct(string $username, string $password, string $email, string $id)
    {
        $this->setId($id)
            ->setUsername($username)
            ->setPassword($password)
            ->setEmail($email);
    }

    /**
     * @return int
     */
    function getId() :string
    {
        if($this->id !== null)
        {
            return $this->id;
        }

        return '';
    }

    /**
     * @param string $id
     * @return ApplicationUser
     */
    function setId(string $id) : ApplicationUser
    {
        $this->id = $id;
        return $this;
    }

    function getUsername() : string
    {
        if($this->username !== null)
        {
            return $this->username;
        }

        return '';
    }

    /**
     * @param string $username
     * @return ApplicationUser
     */
    function setUsername(string $username) : ApplicationUser
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    function getEmail() : string
    {
        if($this->username !== null)
        {
            return $this->email;
        }

        return '';
    }

    /**
     * @param string $email
     * @return ApplicationUser
     */
    function setEmail(string $email) : ApplicationUser
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    function getPassword() : string
    {
        if($this->password !== null)
        {
            return $this->password;
        }

        return '';
    }

    /**]
     * @param string $password
     * @return ApplicationUser
     */
    function setPassword(string $password) : ApplicationUser
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return bool
     */
    function isLogged() : bool
    {
       return $this->username !== null;
    }
}