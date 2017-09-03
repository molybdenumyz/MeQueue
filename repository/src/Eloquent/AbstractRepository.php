<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/6/28
 * Time: 下午11:22
 */

namespace App\Repository\Eloquent;


use Carbon\Carbon;
use Closure;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use App\Repository\Contracts\RepositoryInterface;
use Illuminate\Support\Facades\DB;

abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * 传入要从容器中解析出的实例的完整类路径
     * @var Model $model
     * */
    protected $model;

    public function __construct(Container $app)
    {
        $this->model = $app->make($this->model());
    }

    abstract function model();

    public function all(array $columns = ['*'])
    {
        return $this->model->get($columns);
    }

    public function get(string $id, array $columns = ['*'], string $primary = 'id')
    {
        return $this->model
            ->where($primary, $id)
            ->get($columns)
            ->first();
    }

    public function getBy(string $param, string $value, array $columns = ['*'])
    {
        return $this->model
            ->where($param, $value)
            ->get($columns);
    }

    public function getByMult(array $params, array $columns = ['*'])
    {
        return $this->model
            ->where($params)
            ->get($columns);
    }

    //在多个候选列表中的匹配
    public function getIn($param, array $data, array $columns = ['*'])
    {
        return $this->model
            ->whereIn($param, $data)
            ->get($columns);
    }

    public function insert(array $data)
    {
        if ($this->model->timestamps) {
            $current = new Carbon();

            if (!is_array(reset($data))) {
                $data = array_merge($data,
                    [
                        'created_at' => $current,
                        'updated_at' => $current,
                    ]);
            } else {
                foreach ($data as $key => $value) {
                    $data[$key] = array_merge($value,
                        [
                            'created_at' => $current,
                            'updated_at' => $current,
                        ]);
                }
            }

        }
        return $this->model
            ->insert($data);
    }

    public function update(array $data, int $id, string $attribute = "id")
    {
        if ($this->model->timestamps) {
            $current = new Carbon();

            if (!is_array(reset($data))) {
                $data = array_merge($data,
                    [
                        'updated_at' => $current,
                    ]);
            } else {
                foreach ($data as $key => $value) {
                    $data[$key] = array_merge($value,
                        [
                            'updated_at' => $current,
                        ]);
                }
            }

        }
        return $this->model
            ->where($attribute, '=', $id)
            ->update($data);
    }


    /**
     * 多条件限定查找
     */
    public function updateWhere(array $condition, array $data)
    {
        if ($this->model->timestamps) {
            $current = new Carbon();

            if (!is_array(reset($data))) {
                $data = array_merge($data,
                    [
                        'updated_at' => $current,
                    ]);
            } else {
                foreach ($data as $key => $value) {
                    $data[$key] = array_merge($value,
                        [
                            'updated_at' => $current,
                        ]);
                }
            }

        }
        return $this->model
            ->where($condition)
            ->update($data);
    }


    public function delete(string $id, string $primary = 'id'): bool
    {
        //WARNING:DO NOT USE THIS FUNCTION
        return $this->model
            ->where($primary, $id)->delete() == 1;
    }

    public function deleteWhere(array $param = [])
    {
        return $this->model
            ->where($param)->delete();
    }

    // 分页方法

    public function paginate(int $page = 1, int $size = 20, array $param = [], array $columns = ['*'])
    {
        if (!empty($param))
            return $this->model
                ->where($param)
                ->skip($size * --$page)
                ->take($size)
                ->get($columns);
        else
            return $this->model
                ->skip($size * --$page)
                ->take($size)
                ->get($columns);
    }

    public function getWhereCount(array $param = []): int
    {
        if (!empty($param)) {
            return $this->model->where($param)->count();
        } else
            return $this->model->count();
    }

    public function whereExist(Closure $func)
    {
        return $this->model->whereExists($func)->get();
    }

    // 原始方法，比较危险，需要注意

    public function selectRaw(string $sql, array $condition = [])
    {
        if (!empty($condition)) {
            return $this->model->select(DB::raw($sql))
                ->where($condition)
                ->get();
        }
    }

    private function freshTimestamp(): Carbon
    {
        return new Carbon;
    }
}