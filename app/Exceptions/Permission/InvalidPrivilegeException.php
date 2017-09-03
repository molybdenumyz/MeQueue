<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/7/8
 * Time: 上午11:09
 */

namespace App\Exceptions\Permission;


use App\Exceptions\BaseException;

class InvalidPrivilegeException extends BaseException
{
    protected $code = 30006;
    protected $data = "The input privileges is invalid";
}