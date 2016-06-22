<?php

declare(strict_types = 1);

namespace WDB\Identity\Managers;

use WDB\Identity\IApplicationUser;
use WDB\Models\BindingModels\ChangePasswordBindingModel;
use WDB\Models\BindingModels\LoginUserBindingModel;
use WDB\Models\BindingModels\RegisterUserBindingModel;

interface IUserManager
{
    static function getInstance() : IUserManager;
    function register(RegisterUserBindingModel $model) : int;
    function login(LoginUserBindingModel $model) : int;

    function edit(EditUserBindingModel $model) :bool;
    function changePassword(ChangePasswordBindingModel $model) : bool;

    function isExistingUsername(string $username) :bool;
    function isExistingEmail(string $email) :bool;

    function getInfo(string $id) : array;

    function isInRoleByUsername(string $username, string $roleName) : bool;
    function isInRoleById(string $id, string $roleName) : bool;

    function addToRole(int $userId, int $roleId);

}