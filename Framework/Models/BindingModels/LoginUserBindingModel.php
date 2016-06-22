<?php

declare(strict_types = 1);

namespace WDB\Models\BindingModels;

class LoginUserBindingModel implements IBindingModel
{
    /**
     * @Required
     * @MinLength 5
     */
    private $username;

    /**
     * @Required
     * @MinLength 5
     */
    private $password;

    /**
     * @return string $username
     */
    public function getUsername() : string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword() :string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }
}