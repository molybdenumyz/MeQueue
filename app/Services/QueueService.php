<?php
/**
 * Created by PhpStorm.
 * User: yz
 * Date: 17/8/29
 * Time: 下午3:06
 */

namespace App\Services;


use App\Common\Utils;
use App\Exceptions\ApplyLateException;
use App\Exceptions\TImeHaveBeenBlock;
use App\Repository\Eloquent\QueueRepository;
use App\Services\Contracts\QueueServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class QueueService implements QueueServiceInterface
{

    private $queueRepo;

    /**
     * QueueService constructor.
     * @param $queueRepo
     */
    public function __construct(QueueRepository $queueRepo)
    {
        $this->queueRepo = $queueRepo;
    }

    function applyOrder(array $orderInfo)
    {
        $id = 0;
        DB::transaction(function () use ($orderInfo, &$id) {

            $this->findOccupation($orderInfo['start_time'],$orderInfo['end_time']);


            $id = $this->queueRepo->insertWithId($orderInfo);

        });
        return $id;
    }

    function getOrders($startTime, $endTime, $status)
    {
        $now = Utils::createTimeStamp();

//        $appliers = $this->queueRepo->findExpiresId($now);
//
//        DB::transaction(function () use ($appliers) {
//
//            foreach ($appliers as $applier) {
//                $this->queueRepo->delete($applier->id);
//            }
//        });
        if ($status == 1) {
            $data = $this->queueRepo->findUnExpires($now, $startTime, $endTime);

        } else {
            $data = [
                'unExpires' => $this->queueRepo->findUnExpires($now, $startTime, $endTime),
                'expires' => $this->queueRepo->findExpires($now)
            ];
        }


        return $data;
    }

    function updateOrderStatus($orderId, $status)
    {
        return $this->queueRepo->update(['status' => $status], $orderId) == 1;
    }

    function deleteOrder($orderId)
    {
        return $this->queueRepo->deleteWhere(['id' => $orderId]) == 1;
    }

    function closeBlock($times)
    {
        //查出时间段中存在的项
        //$data = $this->queueRepo->findUnExpires(Utils::createTimeStamp(),$startTime,$endTime);

        $flag = false;

        DB::transaction(function () use (&$times, &$flag) {
            foreach ($times as &$time) {
                $count = $this->queueRepo->getWhereCount(['start_time' => $time['startTime'], 'end_time' => $time['endTime']]);
                if ($count > 1)
                    continue;
                else {
                    $time['status'] = 2;
                    $time = Utils::unCamelize($time);
                    $this->queueRepo->insert($time);
                }
            }
            $flag = true;
        });
        return $flag;
    }


    function getClosedBlock()
    {

        $data = $this->queueRepo->getBy('status', 2, ['id', 'status', 'start_time', 'end_time'])->toArray();

        foreach ($data as &$item) {
            $item = Utils::camelize($item);
        }
        return $data;
    }


    function releaseBlock($blockIds)
    {
        DB::transaction(function () use ($blockIds) {
            $this->queueRepo->deleteWhereIn('id', $blockIds);
        });
        return true;
    }

    function dump($sheetName, $startTime, $endTime)
    {
        $rows[] = ['当前预约状态', '患者姓名', '患者手机号', '检查部位', '开始时间', '结束时间'];

        $data = $this->queueRepo->getByMult([
            [
                'start_time', '>=', $startTime
            ],
            [
                'end_time', '<=', $endTime
            ],
            [
                'status', '!=', 2
            ]
        ], ['status', 'name', 'mobile', 'position', 'start_time', 'end_time', 'expires_at'])->toArray();


        foreach ($data as &$datum) {

            $datum = array_values($datum);
            if ($datum[0] == 0) {
                if ($datum[6] > Utils::createTimeStamp())
                    $datum[0] = '待确认';
                else
                    $datum[0] = '已过期';
            } elseif ($datum[0] == 1) {
                $datum[0] = '预约确认';
            }

            $datum[4] = date('Y-m-d H:i', $datum[4] / 1000);
            $datum[5] = date('Y-m-d H:i', $datum[5] / 1000);

            unset($datum[6]);

            $rows[] = $datum;
        }

        $this->export($sheetName, $rows);
    }

    public function export(string $name, array $rows)
    {
        Excel::create($name, function ($excel) use ($rows) {
            $excel->sheet('sheet1', function ($sheet) use ($rows) {

                $sheet->setWidth(array(
                    'A' => 12,
                    'B' => 10,
                    'C' => 12,
                    'D' => 12,
                    'E' => 20,
                    'F' => 20
                ));
                $sheet->rows($rows);
            });

        })->download('xlsx', [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Headers' => 'Origin, Content-Type, Cookie, Accept,token,Accept,X-Requested-With',
            'Access-Control-Allow-Methods' => 'GET, POST, DELETE, PATCH, PUT, OPTIONS',
            'Access-Control-Allow-Credentials' => 'true'
        ]);
    }

    function findOccupation($startTime,$endTime){

        $rowLeft = count($this->queueRepo->getBy('start_time',$startTime,['id']));

        $rowRight = count($this->queueRepo->getBy('end_time',$endTime,['id']));


        $count = $this->queueRepo->findWeatherOccupation(Utils::createTimeStamp(),$startTime,$endTime)[0]->row ;

        if (($count - $rowLeft -$rowRight) > 0)
            throw new ApplyLateException();

        return true;
    }
}