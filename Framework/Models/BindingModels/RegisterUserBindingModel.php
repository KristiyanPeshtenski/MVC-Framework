<?php

declare(strict_types = 1);

namespace WDB\Models\BindingModels;

class RegisterUserBindingModel implements IBindingModel
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
     * @Required
     * @MinLength 5
     * @Display(Confirm Password)
     */
    private $confirmPassword;

    /**
     * @Required
     */
    private $email;

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

    /**
     * @return string
     */
    public function getConfirmPassword() :string
    {
        return $this->confirmPassword;
    }

    /**
     * @param string $confirmPassword
     */
    public function setConfirmPassword(string $confirmPassword)
    {
        $this->confirmPassword = $confirmPassword;
    }

    /**
     * @return string
     */
    public function getEmail() :string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }
}