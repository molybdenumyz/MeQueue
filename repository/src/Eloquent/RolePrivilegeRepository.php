<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/7/8
 * Time: 上午10:19
 */

namespace App\Repository\Eloquent;


class RolePrivilegeRepository extends AbstractRepository
{
    function model()
    {
        return "App\Repository\Models\RolePrivilege";
    }
}