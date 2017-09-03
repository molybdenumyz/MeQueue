<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/6/29
 * Time: 下午11:51
 */

namespace App\Repository\Eloquent;

use App\Repository\Traits\InsertWithIdTrait;

class UserRepository extends AbstractRepository
{
    function model()
    {
        return 'App\Repository\Models\User';
    }

    use InsertWithIdTrait;
}