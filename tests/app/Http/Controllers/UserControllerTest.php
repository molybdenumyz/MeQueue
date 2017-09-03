<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserControllerTest extends TestCase
{
   private $userName;
   private $userEmail;
   private $userMobile;

   public function __construct()
   {
       // 每次测试生成随机数据，以便支持多次测试，手机号暂时没找到合适方案

       $this->userName = 'markTest'.str_random(3);
       $this->userEmail = $this->userName.'@test.com';
       $this->userMobile = '13525919'.rand(100,999);
   }

    public function testRegister()
    {
        $this->json('POST','/user/register',[
            'name' => $this->userName,
            'email' => $this->userEmail,
            'mobile' => $this->userMobile,
            'password' => '123456'
        ])->seeJson([
            'code' => 0
        ]);

        $this->json('POST','/user/register',[
            'name' => $this->userName,
            'email' => $this->userEmail,
            'mobile' => $this->userMobile,
            'password' => '123456'
        ])->seeJson([
            'code' => 20003
        ]);
    }

    public function testLogin()
    {
        $identifiers = [
            $this->userName,$this->userEmail,$this->userMobile
        ];

        foreach ($identifiers as $identifier) {

            $this->json('POST','/user/login',[
                'identifier' => $identifier,
                'password' => '123456',
                'client' => '1'
            ])->seeJson([
                'code' => 0
            ]);
        }
    }
}
