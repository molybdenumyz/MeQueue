<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/7/8
 * Time: 上午11:50
 */

namespace App\Services;


use App\Repository\Eloquent\UserPrivilegeRepository;
use App\Services\Contracts\PermissionServiceInterface;

class PermissionService implements PermissionServiceInterface
{
    private $userPrivilegeRepo;


    public function __construct(UserPrivilegeRepository $userPrivilegeRepo)
    {
        $this->userPrivilegeRepo = $userPrivilegeRepo;
    }

    public function getUserPrivileges(int $userId)
    {
        $rows = $this->userPrivilegeRepo->getBy('user_id',$userId,['privilege']);

        $privileges = [];

        foreach ($rows as $row) {
            $privileges[] =$row->privilege;
        }

        return $privileges;
    }

    public function checkPermission(int $userId, array $privileges)
    {
        return $this->userPrivilegeRepo->checkUserPrivileges($userId,$privileges);
    }

}