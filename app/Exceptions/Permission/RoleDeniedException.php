<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/6/28
 * Time: 下午10:06
 */

namespace App\Exceptions\Permission;


use App\Exceptions\BaseException;

class RoleDeniedException extends BaseException
{
    protected $code = 30005;
    protected $data = "Role Denied";
}