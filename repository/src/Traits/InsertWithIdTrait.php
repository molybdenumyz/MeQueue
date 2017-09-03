<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/6/29
 * Time: 下午11:16
 */

namespace App\Repository\Traits;

use Carbon\Carbon;

trait InsertWithIdTrait
{
    function insertWithId(array $data)
    {
        if($this->model->timestamps){
            $current = new Carbon();

            if(! is_array(reset($data))){
                $data = array_merge($data,
                    [
                        'created_at' => $current,
                        'updated_at' => $current,
                    ]);
            }else{
                foreach ($data as  $key => $value) {
                    $data[$key] = array_merge($value,
                        [
                            'created_at' => $current,
                            'updated_at' => $current,
                        ]);
                }
            }

        }

        return $this->model->insertGetId($data);
    }

}