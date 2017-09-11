<?php
/**
 * Created by PhpStorm.
 * User: yz
 * Date: 17/8/29
 * Time: 下午3:01
 */

namespace App\Services\Contracts;


interface QueueServiceInterface
{
    function applyOrder(array $orderInfo);

    function getOrders($startTime,$endTime,$token);

    function updateOrderStatus($orderId,$status);

    function deleteOrder($orderId);

    function closeBlock($times);

    function getClosedBlock();

    function releaseBlock($info);

    function dump($sheetName,$startTime,$endTime);


}