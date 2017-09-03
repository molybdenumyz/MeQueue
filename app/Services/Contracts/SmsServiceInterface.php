<?php

namespace App\Services\Contracts;

interface SmsServiceInterface
{
    function sendSms(string $mobile, string $msg): string;

    function execResult(string $result): string;
}

?>