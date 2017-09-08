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
use App\Exceptions\OrderNotExistException;
use App\Exceptions\PermissionDeniedException;
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

    public function addOrder(Request $request)
    {
        $rules = [
            'name' => 'required|max:100',
            'mobile' => 'required|max:125',
            'position' => 'required|max:100',
            'startTime' => 'required|integer',
            'endTime' => 'required|integer',
            'expiresAt' => 'required|integer'
        ];

        ValidationHelper::validateCheck($request->all(), $rules);

        $info = ValidationHelper::getInputData($request, $rules);
        $info = Utils::unCamelize($info);


        return response()->json(
            [
                'code' => 0,
                'data' => [
                    'orderId' => $this->queueService->applyOrder($info)
                ]
            ]
        );
    }

    public function deleteOrder(Request $request, int $orderId)
    {

        $rules = [
          'token' => 'required'
        ];

        ValidationHelper::validateCheck($request->all(),$rules);

        $info = ValidationHelper::getInputData($request,$rules);

        $token = $info['token'];

        if (strcmp("a29f44c76423a2f5787adf0eefdc07ac",$token))
            throw new PermissionDeniedException();

        if (!$this->queueService->deleteOrder($orderId)) {
            throw new OrderNotExistException();
        }

        return response()->json(
            [
                'code' => 0
            ]
        );
    }

    public function updateOrderStatus(Request $request)
    {
        $rules = [
            'token' => 'required',
            'orderId' => 'required|integer',
            'status' => 'required|integer'
        ];

        ValidationHelper::validateCheck($request->all(),$rules);

        $info = ValidationHelper::getInputData($request,$rules);

        $token = $info['token'];

        if (strcmp("a29f44c76423a2f5787adf0eefdc07ac",$token))
            throw new PermissionDeniedException();


        if (! $this->queueService->updateOrderStatus($info['orderId'], $info['status']))
            throw new UnknownException("更新订单状态失败");


        return response()->json(
            [
                'code' => 0
            ]
        );
    }

    public function getOrder(Request $request)
    {
        $rules = [
            'startTime' => 'required|integer',
            'endTime' => 'required|integer'
        ];

        ValidationHelper::validateCheck($request->all(),$rules);

        $info = ValidationHelper::getInputData($request,$rules);


        return response()->json(
            [
                'code' => 0,
                'data' => $this->queueService->getOrders($info['startTime'], $info['endTime'])
            ]
        );

    }

    public function closeBlock(Request $request)
    {
        $rules = [
            'token' => 'required',
            'block' => 'required|array'
        ];

        ValidationHelper::validateCheck($request->all(), $rules);

        $times = ValidationHelper::getInputData($request, $rules);

        $this->queueService->closeBlock($times['block']);


        $token = $times['token'];
        if (strcmp("a29f44c76423a2f5787adf0eefdc07ac",$token))
            throw new PermissionDeniedException();

        return response()->json(
            [
                'code' => 0,
                'data' => $this->queueService->getClosedBlock()
            ]
        );

    }

    public function getBlocks(Request $request)
    {
        return response()->json(
            [
                'code' => 0,
                'data' => $this->queueService->getClosedBlock()
            ]
        );
    }

    public function releaseBlock(Request $request)
    {
        $rules = [
            'token'=>'required',
            'blockIds'=>'required|array'
        ];

        ValidationHelper::validateCheck($request->all(), $rules);

        $blockIds = ValidationHelper::getInputData($request, $rules);

        $this->queueService->releaseBlock($blockIds['blockIds']);


        $token = $blockIds['token'];

        if (strcmp("a29f44c76423a2f5787adf0eefdc07ac",$token))
            throw new PermissionDeniedException();

        return response()->json(
            [
                'code'=>0,
                'data'=>$this->queueService->getClosedBlock()
            ]
        );
    }


    public function dump(Request $request){
        $rules = [
          'token'=>'required',
          'startTime'=>'required|integer',
          'endTime'=>'required|integer',
          'sheetName'=>'required'
        ];

        ValidationHelper::validateCheck($request->all(),$rules);

        $info = ValidationHelper::getInputData($request,$rules);

        $token = $info['token'];

        if (strcmp("a29f44c76423a2f5787adf0eefdc07ac",$token))
            throw new PermissionDeniedException();


        $this->queueService->dump($info['sheetName'],$info['startTime'],$info['endTime']);
    }
}