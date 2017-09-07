<?php
/**
 * Created by PhpStorm.
 * Auth: mark
 * Date: 17/6/28
 * Time: 下午9:48
 */

namespace App\Exceptions\Common;


use App\Exceptions\BaseException;

class UnknownException extends BaseException
{
    protected $code = 5000;

    public function __construct(string $data)
    {
        parent::__construct();
        $this->data = $data;
    }
}