<?php
/**
 * Created by PhpStorm.
 * User: yz
 * Date: 17/9/6
 * Time: 下午9:54
 */

namespace App\Exceptions;


class PermissionDeniedException extends BaseException
{
    protected $code = 40004;
    protected $data = "密码错误";
}