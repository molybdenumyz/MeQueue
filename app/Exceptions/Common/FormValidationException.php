<?php
/**
 * Created by PhpStorm.
 * Auth: mark
 * Date: 17/6/28
 * Time: 下午9:45
 */

namespace App\Exceptions\Common;


use App\Exceptions\BaseException;
use Throwable;

class FormValidationException extends BaseException
{
    protected $code = 10001;

    public function __construct(array $data = [''])
    {
        parent::__construct();
        $this->data = $data;
    }
}