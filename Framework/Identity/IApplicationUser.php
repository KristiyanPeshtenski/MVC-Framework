<?php

declare(strict_types = 1);

namespace WDB\Identity;

interface IApplicationUser
{
    function getId() :string;
    function setId(string $id) : ApplicationUser;

    function getUsername() : string;
    function setUsername(string $username) : ApplicationUser;

    function getEmail() : string;
    function setEmail(string $email) : ApplicationUser;

    function getPassword() : string;
    function setPassword(string $password) : ApplicationUser;

    function isLogged() : bool;
}