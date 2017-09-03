<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/6/28
 * Time: 下午10:04
 */

namespace App\Exceptions\Permission;


use App\Exceptions\BaseException;

class PermissionDeniedException extends BaseException
{
    protected $code = 30004;
    protected $data = "Permission Denied";
}