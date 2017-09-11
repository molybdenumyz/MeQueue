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
        DB::transaction(function ()use ($orderInfo,&$id){
            $records = $this->queueRepo->findFreeByTime($orderInfo['start_time'], $orderInfo['end_time']);
            if ($records != null) {
                $now = Utils::createTimeStamp();
                foreach ($records as $record) {

                    if ($record->status == 0) {
                        if ($now > $record->expires_at) {
                            continue;
                        } else {
                            throw new ApplyLateException();
                        }
                    } elseif ($record->status == 1) {
                        throw new ApplyLateException();
                    } elseif ($record->status == 2) {
                        throw new TImeHaveBeenBlock();
                    }
                }
            }
            $id = $this->queueRepo->insertWithId($orderInfo);
        });
        return $id;
    }

    function getOrders($startTime, $endTime)
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

        $data = $this->queueRepo->findUnExpires($now,$startTime,$endTime);


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
                $count = $this->queueRepo->getWhereCount(['start_time'=>$time['startTime'],'end_time'=>$time['endTime']]);
                if ($count >1)
                    continue;
                else{
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
        DB::transaction(function () use($blockIds){
            $this->queueRepo->deleteWhereIn('id', $blockIds);
        });
        return true;
    }

    function dump($sheetName,$startTime, $endTime)
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
            ],
            [
                'expires_at', '>', Utils::createTimeStamp()
            ]
        ], ['status', 'name', 'mobile', 'position', 'start_time', 'end_time'])->toArray();

        foreach ($data as &$datum) {

            $datum = array_values($datum);
            if ($datum[0] == 0) {
                $datum[0] = '已预约';
            } elseif ($datum[0] == 1) {
                $datum[0] = '预约确认';
            }

            $datum[4] = date('Y-m-d H:i:s', $datum[4] / 1000);
            $datum[5] = date('Y-m-d H:i:s', $datum[5] / 1000);
            $rows[] = $datum;
        }

        $this->export($sheetName, $rows);
    }

    public function export(string $name, array $rows)
    {
        Excel::create($name, function ($excel) use ($rows) {
            $excel->sheet('sheet1', function ($sheet) use ($rows) {

                $sheet->setWidth(array(
                    'A' => 15,
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
}