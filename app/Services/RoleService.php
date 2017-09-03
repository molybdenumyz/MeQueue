<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/7/8
 * Time: 上午10:15
 */

namespace App\Services;


use App\Exceptions\Permission\InvalidPrivilegeException;
use App\Exceptions\Permission\RoleExistedException;
use App\Exceptions\Permission\RoleNotExistException;
use App\Repository\Eloquent\PrivilegeRepository;
use App\Repository\Eloquent\RolePrivilegeRepository;
use App\Repository\Eloquent\RoleRepository;
use App\Repository\Eloquent\UserPrivilegeRepository;
use App\Services\Contracts\RoleServiceInterface;
use Illuminate\Support\Facades\DB;

class RoleService implements RoleServiceInterface
{
    private $roleRepo;
    private $privilegeRepo;
    private $rolePriRepo;
    private $userPriRepo;

    public function __construct(
        RoleRepository $roleRepository, PrivilegeRepository $privilegeRepository,
        RolePrivilegeRepository $rolePrivilegeRepository,
        UserPrivilegeRepository $userPrivilegeRepository
    )
    {
        $this->roleRepo = $roleRepository;
        $this->privilegeRepo = $privilegeRepository;
        $this->rolePriRepo = $rolePrivilegeRepository;
        $this->userPriRepo = $userPrivilegeRepository;
    }

    public function createRole(array $role, array $privileges): bool
    {
        $roleCount = $this->roleRepo->getWhereCount([
            'name' => $role['name']
        ]);

        if ($roleCount > 0) {
            throw new RoleExistedException();
        }

        if (!$this->checkPrivileges($privileges)) {
            // 检测给的权限是否正确，防止用户注入不存在的权限造成危害
            throw new InvalidPrivilegeException();
        }

        $flag = false;

        DB::transaction(function () use ($role, $privileges, &$flag) {
            $this->roleRepo->insert($role);
            $relations = [];
            foreach ($privileges as $privilege) {
                $relations[] = [
                    'role_name' => $role['name'],
                    'privilege_name' => $privilege
                ];
            }
            $this->rolePriRepo->insert($relations);
            $flag = true;
        });

        return $flag;
    }

    public function updateRole(string $roleName, array $role, array $privileges): bool
    {
        // 角色创建后拒绝修改role_name，只能修改display_name
        if (isset($role['name'])) {
            unset($role['name']);
        }

        $roleCount = $this->roleRepo->getWhereCount([
            'name' => $roleName
        ]);

        if ($roleCount == 0) {
            throw new RoleNotExistException();
        }

        if (!$this->checkPrivileges($privileges)) {
            // 检测给的权限是否正确，防止用户注入不存在的权限造成危害
            throw new InvalidPrivilegeException();
        }

        $flag = false;

        DB::transaction(function () use ($roleName, $role, $privileges, &$flag) {
            $this->roleRepo->updateWhere(['name' => $roleName], $role);
            $this->rolePriRepo->deleteWhere(['role_name' => $roleName]);
            $relations = [];
            foreach ($privileges as $privilege) {
                $relations[] = [
                    'role_name' => $roleName,
                    'privilege_name' => $privilege
                ];
            }
            $this->rolePriRepo->insert($relations);
            $flag = true;
        });

        return $flag;
    }

    public function deleteRole(string $roleName): bool
    {
        $flag = false;

        DB::transaction(function () use ($roleName, &$flag) {
            $this->rolePriRepo->deleteWhere(['role_name' => $roleName]);
            $this->roleRepo->deleteWhere(['name' => $roleName]);
            $flag = true;
        });

        return $flag;
    }

    public function getRolePrivileges(string $roleName)
    {
        $rows = $this->rolePriRepo->getBy('role_name',$roleName,['privilege_name']);

        $privileges = [];

        foreach ($rows as $row) {
            $privileges[] =$row->privilege_name;
        }

        return $privileges;
    }

    public function giveRoleTo(int $userId, string $roleName):bool
    {
        $privileges = $this->getRolePrivileges($roleName);

        $flag = false;

        DB::transaction(function ()use($userId,$privileges,&$flag){
            // 删除之前全部权限，重新生成
            $this->userPriRepo->deleteWhere(['user_id' => $userId]);
            $relations = [];
            foreach ($privileges as $privilege) {
                $relations[] =[
                    'user_id' => $userId,
                    'privilege' => $privilege
                ];
            }

            $this->userPriRepo->insert($relations);

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