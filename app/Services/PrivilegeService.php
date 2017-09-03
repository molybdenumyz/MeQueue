<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/7/8
 * Time: 上午11:37
 */

namespace App\Services;


use App\Exceptions\Permission\InvalidPrivilegeException;
use App\Repository\Eloquent\PrivilegeRepository;
use App\Repository\Eloquent\UserPrivilegeRepository;
use App\Services\Contracts\PrivilegeServiceInterface;

class PrivilegeService implements PrivilegeServiceInterface
{
    private $privilegeRepo;
    private $userPrivilegeRepo;

    /**
     * PrivilegeService constructor.
     * @param $privilegeRepo
     * @param $userPrivilegeRepo
     */
    public function __construct(PrivilegeRepository $privilegeRepo,UserPrivilegeRepository $userPrivilegeRepo)
    {
        $this->privilegeRepo = $privilegeRepo;
        $this->userPrivilegeRepo = $userPrivilegeRepo;
    }

    public function getAllPrivileges()
    {
        return $this->privilegeRepo->all();
    }

    public function refreshUserPrivileges(int $userId, array $privileges): bool
    {
        if (!$this->checkPrivileges($privileges)) {
            throw new InvalidPrivilegeException();
        }

        $flag = false;

        DB::transaction(function ()use($userId,$privileges,&$flag){
            // 删除之前全部权限，重新生成
            $this->userPrivilegeRepo->deleteWhere(['user_id' => $userId]);
            $relations = [];
            foreach ($privileges as $privilege) {
                $relations[] =[
                    'user_id' => $userId,
                    'privilege' => $privilege
                ];
            }

            $this->userPrivilegeRepo->insert($relations);

            $flag = true;
        });

        return $flag;
    }

    private function checkPrivileges(array $privileges): bool
    {
        $count = $this->privilegeRepo->getIn('name', $privileges, ['name'])->count();

        if ($count != count($privileges)) {
            return false;
        }

        return true;
    }

}