<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/7/8
 * Time: 上午10:18
 */

namespace App\Repository\Eloquent;


class PrivilegeRepository extends AbstractRepository
{
    function model()
    {
        return "App\Repository\Models\Privilege";
    }
}