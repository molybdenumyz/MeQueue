<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/7/8
 * Time: 上午10:07
 */

namespace App\Services\Contracts;


interface PrivilegeServiceInterface
{
    function getAllPrivileges();

    function refreshUserPrivileges(int $userId,array $privileges):bool;
}