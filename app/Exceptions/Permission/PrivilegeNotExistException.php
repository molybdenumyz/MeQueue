<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/6/28
 * Time: 下午10:00
 */

namespace App\Exceptions\Permission;


use App\Exceptions\BaseException;

class PrivilegeNotExistException extends BaseException
{
    protected $code = 30001;
    protected $data = "Privilege Not Exist";
}