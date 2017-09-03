<?php
/**
 * Created by PhpStorm.
 * Auth: mark
 * Date: 17/6/28
 * Time: 下午9:28
 */

namespace App\Common;

use Illuminate\Support\Facades\Validator;

class Utils
{
    /**
     * 毫秒级时间戳生成工具
     * 返回当前时间的13位毫秒级时间戳
     * @return float
     */
    public static function createTimeStamp(): float
    {
        list($micro, $se) = explode(' ', microtime());
        return $se * 1000 + round($micro * 1000, 0);
    }

    /**
     * 邮箱正则判断
     * @param string $email
     * @return bool
     */
    public static function isEmail(string $email): bool
    {
        $patternEmail = '/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/';
        return preg_match($patternEmail, $email) == 1;
    }

    /**
     * 手机号正则判断
     * @param string $mobile
     * @return bool
     */
    public static function isMobile(string $mobile):bool
    {
        $patternMobile = '/(13\d|14[57]|15[^4,\D]|17[678]|18\d)\d{8}|170[059]\d{7}/';
        return preg_match($patternMobile, $mobile) == 1;
    }

    /**
     * 判断数组是否是关联数组
     * @param $array
     * @return bool
     */
    public static function isAssoc($array)
    {
        if(is_array($array)) {
            $keys = array_keys($array);
            return $keys !== array_keys($keys);
        }
        return false;
    }

    /**
     * 对一个变量进行下划线转驼峰化，可以是关联数组或者stdClass
     * @param $var
     * @param string $separator
     * @return mixed
     */
    public static function camelize(&$var,$separator='_')
    {
        if ($var instanceof \stdClass) {
            // 对对象的转换
            return self::camelize(get_object_vars($var),$separator);
        }

        if (is_array($var)){
            if (self::isAssoc($var)) {
                $newKeys = [];
                foreach ($var as $key => &$value){
                    if (isset($newKeys[$key])) {
                        continue;
                    }
                    $newKey = self::camelizeString($key,$separator);
                    $newKeys[$newKey] = true;
                    $var[$newKey] =  self::camelize($value,$separator);
                    unset($var[$key]);
                }
            }
        }
        return $var;
    }

    public static function camelizeString($uncamelized_words,$separator='_')
    {
        $uncamelized_words = $separator. str_replace($separator, " ", strtolower($uncamelized_words));
        return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator );
    }

    public static function unCamelize(&$var,$separator='_'){

        if ($var instanceof \stdClass) {
        // 对对象的转换
        return self::unCamelize(get_object_vars($var), $separator);
    }

        if (is_array($var)) {
            if (self::isAssoc($var)) {
                $newKeys = [];
                foreach ($var as $key => &$value) {
                    if (isset($newKeys[$key])) {
                        continue;
                    }
                    $newKey = self::unCamelizeString($key, $separator);
                    $newKeys[$newKey] = true;
                    $var[$newKey] = self::unCamelize($value, $separator);
                    if ($newKey != $key) {
                        // 防止不变的键被删除
                        unset($var[$key]);
                    }
                }
            }
        }
        return $var;
    }

    public static function unCamelizeString($camelCaps,$separator='_') {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }
}