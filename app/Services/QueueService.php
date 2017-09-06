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
use Illuminate\Support\Facades\DB;

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
                }elseif ($record->status == 1){
                    throw new ApplyLateException();
                }elseif ($record->status == 2){
                    throw new TImeHaveBeenBlock();
                }
            }
        }

        return $this->queueRepo->insertWithId($orderInfo);
    }

    function getOrders($startTime, $endTime)
    {
        $now = Utils::createTimeStamp();

        $appliers = $this->queueRepo->findExpiresId($now);

        DB::transaction(function () use ($appliers) {

            foreach ($appliers as $applier) {
                $this->queueRepo->delete($applier->id);
            }
        });

        $data =  $this->queueRepo->getByMult([
            [
                'start_time','>=',$startTime
            ],
            [
                'end_time','<=',$endTime
            ]
        ])->toArray();

        foreach ($data as &$item){
            $item = Utils::camelize($item);
        }

        return $data;
    }

    function updateOrderStatus($orderId, $status)
    {
        return $this->queueRepo->update(['status' => $status], $orderId) == 1;
    }

    function deleteOrder($orderId)
    {
        return $this->queueRepo->deleteWhere(['id'=>$orderId]) ==1;
    }

    function closeBlock($times)
    {

       $flag = false;
       DB::transaction(function ()use($times,&$flag){
           foreach ($times as &$time) {
               $time['status'] = 2;
               $time = Utils::unCamelize($time);
               $this->queueRepo->insert($time);
          }
          $flag = true;
       });
       return $flag;
    }


    function getClosedBlock(){

        $data = $this->queueRepo->getBy('status',2,['id','status','start_time','end_time'])->toArray();

        foreach ($data as &$item){
            $item = Utils::camelize($item);
        }
        return $data;
    }


    function releaseBlock($blockIds)
    {
        return $this->queueRepo->deleteWhereIn('id',$blockIds);
    }
}