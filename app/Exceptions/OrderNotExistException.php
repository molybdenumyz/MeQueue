<?php
/**
 * Created by PhpStorm.
 * User: yz
 * Date: 17/9/5
 * Time: 上午10:39
 */

namespace App\Exceptions;


class OrderNotExistException extends BaseException
{
    protected $code = 40002;
    protected $data = "订单不存在";
}