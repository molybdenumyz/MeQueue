<?php
/**
 * Created by PhpStorm.
 * User: yz
 * Date: 17/8/29
 * Time: 下午3:11
 */

namespace App\Repository\Models;


use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    protected $table='queue';
    public $timestamps = false;
}