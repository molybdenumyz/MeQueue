<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/6/28
 * Time: 下午9:54
 */

namespace App\Exceptions\Auth;


use App\Exceptions\BaseException;

class UserExistedException extends BaseException
{
    protected $code = 20003;
    protected $data = "User Existed";

    /**
     * UserExistedException constructor.
     * @param string $column 出现重复的字段名称，用于辅助前端构建错误信息
     */

    public function __construct(string $column)
    {
        parent::__construct();
        $this->data = [
            'message' => 'a user with same '.$column.' is existed',
            'column' => $column
        ];
    }
}