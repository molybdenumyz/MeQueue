<?php
/**
 * Created by PhpStorm.
 * Auth: mark
 * Date: 17/6/28
 * Time: 下午9:41
 */

namespace App\Exceptions;


class BaseException extends \Exception
{
    // 错误回显数据
    protected $data;

    public function getData()
    {
        // 外界只读
        return $this->data;
    }
}