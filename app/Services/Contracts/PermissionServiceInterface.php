<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/7/8
 * Time: 上午10:04
 */

namespace App\Services\Contracts;


interface PermissionServiceInterface
{
    function getUserPrivileges(int $userId);

    function checkPermission(int $userId,array $privileges);
}