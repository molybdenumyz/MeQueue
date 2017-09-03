<?php

/**
 * Created by PhpStorm.
 * User: lumin
 * Date: 17/7/17
 * Time: 上午10:20
 */
class UtilsTest extends TestCase
{
    public function testCamelize()
    {
        $arr = [
            'school_id' => 1,
            'school_name' => 'NUEQ'
        ];

        var_dump(\App\Common\Utils::camelize($arr));


    }
}