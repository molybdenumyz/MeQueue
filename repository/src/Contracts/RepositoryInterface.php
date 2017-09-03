<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/6/28
 * Time: 下午11:20
 */

namespace App\Repository\Contracts;


interface RepositoryInterface
{
    function all(array $columns = ['*']);

    function get(string $id, array $columns = ['*'], string $primary = 'id');

    function getBy(string $param, string $value, array $columns = ['*']);

    function getByMult(array $params, array $columns = ['*']);

    function getIn($param, array $data, array $columns = ['*']);

    function insert(array $data);

    function update(array $data, int $id, string $attribute = "id");

    function updateWhere(array $condition,array $data);

    function delete(string $id,string $primary);

    function deleteWhere(array $param = []);

    function paginate(int $page = 1, int $size = 15, array $params = [], array $columns = ['*']);

    function getWhereCount(array $param = []):int;

    function whereExist(\Closure $func);

    function selectRaw(string $sql,array $condition = []);
}