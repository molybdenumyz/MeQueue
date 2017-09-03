<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/6/30
 * Time: 下午3:37
 */

namespace App\Common;
use Illuminate\Support\Facades\Hash;

/**
 * 用于定义加密规则的类，默认使用laravel的Hash实现
 * Class Encrypt
 * @package App\Common
 */
class Encrypt
{
    public static function encrypt(string $secret)
    {
        return Hash::make($secret . config('encrypt.salt'));
    }

    public static function check(string $plain, string $hashed): bool
    {
        return Hash::check($plain . config('encrypt.salt'),$hashed);
    }
}