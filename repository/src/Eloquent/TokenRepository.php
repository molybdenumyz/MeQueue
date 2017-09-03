<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/6/29
 * Time: 下午11:45
 */

namespace App\Repository\Eloquent;


class TokenRepository extends AbstractRepository
{
    function model()
    {
        return 'App\Repository\Models\Token';
    }
}