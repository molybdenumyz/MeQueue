<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/7/8
 * Time: 上午9:42
 */

namespace App\Exceptions\Auth;


use App\Exceptions\BaseException;

class UserNotActivatedException extends BaseException
{
    protected $code = 20006;
    protected $data = "User Not Activated";
}