<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/6/28
 * Time: 下午9:51
 */

namespace App\Exceptions\Auth;


use App\Exceptions\BaseException;

class UserNotExistException extends BaseException
{
    protected $code = 20001;
    protected $data = "User Not Exist";
}