<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/7/8
 * Time: 上午9:57
 */

namespace App\Services\Contracts;


interface RoleServiceInterface
{
    function createRole(array $role,array $privileges):bool;

    function updateRole(string $roleName,array $role,array $privileges):bool;

    function deleteRole(string $roleName):bool;

    function getRolePrivileges(string $roleName);

    function giveRoleTo(int $userId,string $roleName);
}