<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/7/8
 * Time: 上午10:17
 */

namespace App\Repository\Eloquent;


class RoleRepository extends AbstractRepository
{
    function model()
    {
        return "App\Repository\Models\Role";
    }
}