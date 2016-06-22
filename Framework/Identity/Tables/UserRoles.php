<?php

declare(strict_types = 1);

namespace WDB\Identity\Tables;

/**
 * Class UserRoles
 * @package WDB\Identity
 * @Table user_roles
 * @Primary user_id
 * @Primary role_id
 * @Foreign (user_id) References user(id)
 * @Foreign (role_id) References role(id)
 */
class UserRoles
{
    /**
     * @Column user_id
     * @Type INT
     * @Length 11
     * @NotNull
     */
    private $userId;

    /**
     * @Column role_id
     * @Type INT
     * @Length 11
     * @NotNull
     */
    private $roleId;
}