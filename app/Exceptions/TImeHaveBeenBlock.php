<?php
/**
 * Created by PhpStorm.
 * User: yz
 * Date: 17/9/6
 * Time: 下午4:52
 */

namespace App\Exceptions;


class TImeHaveBeenBlock extends BaseException
{
    protected $code = 40003;
    protected $data = "该时间段不空闲";
}