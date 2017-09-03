<?php
/**
 * Created by PhpStorm.
 * User: yz
 * Date: 17/8/29
 * Time: 下午4:01
 */

namespace App\Http\Controllers;


use App\Common\Utils;
use App\Common\ValidationHelper;
use App\Exceptions\Common\UnknownException;
use App\Services\QueueService;
use Illuminate\Http\Request;

class QueueController extends Controller
{

    private $queueService;

    /**
     * QueueController constructor.
     * @param $queueService
     */
    public function __construct(QueueService $queueService)
    {
        $this->queueService = $queueService;
    }

    public function addOrder(Request $request){
        $rules = [
            'name'=>'required|max:100',
            'mobile'=>'required|max:125',
            'position'=>'required|max:100',
            'doctor'=>'required|max:100',
            'startTime'=>'required|integer',
            'endTime'=>'required|integer',
            'expiresAt'=>'required|integer'
        ];

        ValidationHelper::validateCheck($request->all(),$rules);

        $info = ValidationHelper::getInputData($request,$rules);
        $info = Utils::unCamelize($info);


        return response()->json(
            [
                'code'=>0,
                'data'=>[
                    'orderId'=>$this->queueService->applyOrder($info)
                ]
            ]
        );
    }

    public function deleteOrder(Request $request,int $orderId){

        $this->queueService->deleteOrder($orderId);
        return response()->json(
            [
                'code'=>0
            ]
        );
    }

    public function updateOrderStatus(Request $request){
        $rules = [
            'orderId'=>'required|integer',
            'status'=>'required|integer'
        ];

        ValidationHelper::validateCheck($request->all(),$rules);

        $info = ValidationHelper::getInputData($request,$rules);

        if ($this->queueService->updateOrderStatus($info['orderId'],$info['status']))
            throw new UnknownException("更新订单状态失败");
        return response()->json(
            [
                'code'=>0,
                'data'=>'😯'
            ]
        );
    }

    public function getOrder(Request $request){
        $rules = [
            'startTime'=>'required|integer',
            'endTime'=>'required|integer'
        ];

        ValidationHelper::validateCheck($request->all(),$rules);

        $info = ValidationHelper::getInputData($request,$rules);
        return response()->json(
            [
                'code'=>0,
                'data'=>$this->queueService->getOrders($info['startTime'],$info['endTime'])
            ]
        );
    }
}