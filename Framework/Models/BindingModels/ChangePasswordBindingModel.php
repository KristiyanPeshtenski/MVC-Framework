<?php

declare(strict_types = 1);

namespace WDB\Models\BindingModels;

class ChangePasswordBindingModel
{
    /**
     * @Required
     * @MinLength 5
     * @Display(current password)
     */
    private $currentPassword;

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
     * @return string
     */
    public function getCurrentPassword() :string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setCurrentPassword(string $password)
    {
        $this->password = $password;
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
}