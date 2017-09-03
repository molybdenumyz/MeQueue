<?php
/**
 * Created by PhpStorm.
 * User: yz
 * Date: 17/8/29
 * Time: 下午3:12
 */

namespace App\Repository\Eloquent;


use App\Repository\Traits\InsertWithIdTrait;
use Illuminate\Support\Facades\DB;

class QueueRepository extends AbstractRepository
{
    use InsertWithIdTrait;





    function model()
    {
        return "App\Repository\Models\Queue";
    }

    function findFreeByTime($startTime,$endTime){
        return DB::select('select * from queue where start_time <= '.$startTime.' and end_time >= '.$endTime);
    }

    function findExpiresId($now){
        return DB::select('select id from queue where status = 0 and expires_at < '.$now);

    }

}