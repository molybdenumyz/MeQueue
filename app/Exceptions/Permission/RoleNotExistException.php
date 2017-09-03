<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/6/28
 * Time: 下午10:01
 */

namespace App\Exceptions\Permission;


use App\Exceptions\BaseException;

class RoleNotExistException extends BaseException
{
    protected $code = 30002;
    protected $data = "Role Not Exist";
}