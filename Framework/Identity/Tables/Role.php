<?php

declare(strict_types = 1);

namespace WDB\Identity\Tables;

/**
 * Class Role
 * @package WDB\Identity
 * @Table roles
 */
class Role
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
     * @Column name
     * @Type NVARCHAR
     * @Length 255
     * @Unique
     */
    protected $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param string
     * @return Role
     */
    public function setName(string $name) : Role
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }
}