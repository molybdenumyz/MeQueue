<?php
/**
 * Created by PhpStorm.
 * User: yz
 * Date: 17/8/29
 * Time: 下午4:50
 */

namespace App\Exceptions;


class ApplyLateException extends BaseException
{
    protected $code = 40001;
    protected $data = "已经有人在这个时间段预定了";
}