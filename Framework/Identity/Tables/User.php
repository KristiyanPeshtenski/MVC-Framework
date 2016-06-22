<?php

declare(strict_types = 1);

namespace WDB\Identity\Tables;

use WDB\Identity\ApplicationUser;

/**
 * Class User
 * @package WDB\Identity\Tables
 * @Table users
 */
class User extends ApplicationUser
{
    /**
     * @Column full_name
     * @Type NVARCHAR
     * @Length 255
     * @Null
     */
    private $fullName;

    public function __construct(string $username, string $password, string $email, int $id, string $fullName = null)
    {
        parent::__construct($username, $password, $email, $id);
        $this->setFullName($fullName);
    }

    /**
     * @return mixed
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @param mixed $fullName
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }
}