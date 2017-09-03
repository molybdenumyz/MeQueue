<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/6/28
 * Time: 下午10:02
 */

namespace App\Exceptions\Permission;


use App\Exceptions\BaseException;

class RoleExistedException extends BaseException
{
    protected $code = 30003;
    protected $data = "Role Existed";
}