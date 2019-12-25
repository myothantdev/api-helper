<?php
/**
 * Created by PhpStorm.
 * User: myothantkyaw
 * Date: 12/8/19
 * Time: 10:05 AM
 */

namespace Tech\APIHelper\Database\Eloquent;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Tech\APIHelper\Database\RepositoryInterface;
use Tech\APIHelper\Exceptions\DataBaseException;

/**
 * Class BaseRepository
 * @package Tech\APIHelper\Database\Eloquent
 */
class BaseRepository implements RepositoryInterface
{
    /**
     * @var Model
     */
    public $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @return mixed
     */
    public function count()
    {
        return $this->model->count();
    }

    /**
     * @param $params
     * @param $withResource
     * @param $attributes
     * @return mixed
     */
    public function getData($params, $withResource, $attributes)
    {
        $query = $this->getQuery($params, $attributes);
        if (!empty($withResource)) :
            $query = $query->with($withResource);
        endif;

        return $query->get()->toArray();
    }

    /**
     * @param $id
     * @param $withResource
     * @return mixed
     */
    public function getDataById($id, $withResource)
    {
        $query = $this->getQuery(["id" => $id]);
        if (!empty($withResource)) :
            $query = $query->with($withResource);
        endif;
        return $query->first();
    }

    /**
     * @param array $params
     * @return mixed
     * @throws DataBaseException
     */
    public function insertData(array $params)
    {
        try {
            DB::beginTransaction();
            $data = $this->model->create($params);
            DB::commit();
            return $data->toArray();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new DataBaseException($exception->getMessage());
        }
    }

    /**
     * @param $params
     * @param $id
     * @return mixed
     * @throws DataBaseException
     */
    public function updateData($params, $id)
    {
        try {
            DB::beginTransaction();
            $result = $this->model->where("id", "=", $id)->update($params);
            DB::commit();
            if (!empty($result)) :
                return $this->getDataById($id, null)->toArray();
            endif;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new DataBaseException($exception->getMessage());
        }
    }

    /**
     * @param $id
     * @throws DataBaseException
     */
    public function deleteData($id)
    {
        try {
            DB::beginTransaction();
            $this->model->where("id", "=", $id)->delete();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new DataBaseException($exception->getMessage());
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function isExist($id, $trash = null)
    {
        $query = $this->model->where("id", "=", $id);
        if (!empty($trash)) :
            $query = $query->withTrashed();
        endif;
        return $query->exists();
    }

    public function restore($id)
    {
        $result = $this->model->where("id", "=", $id)->restore($id);
        if (!empty($result)) :
            return $this->getDataById($id, null)->toArray();
        endif;
        return $result;
    }

    /**
     * @param $params
     * @return Model
     * @param array $attributes
     */
    public function getQuery($params, $attributes = [])
    {
        $query = $this->model;

        if (!empty($params["limit"])) :
            $query = $query->limit($params["limit"]);
        endif;

        if (!empty($params["offset"]) && !empty($params["limit"])) :
            $query = $query->offset($params["offset"]);
        endif;

        if (!empty($params["from"])) :
            $query = $query->where("created_at", ">=", $params["from"]);
        endif;

        if (!empty($params["to"])) :
            $query = $query->where("created_at", "<=", $params["to"]);
        endif;

        if (!empty($params["id"])) :
            $query = $query->where("id", "=", $params["id"]);
        endif;

        if (!empty($params["deleted"])) :
            $query = $query->whereNotNull("deleted_at")->withTrashed();
        endif;

        if (!empty($attributes)) :
            $query = $this->getCustomQuery($query, $attributes);
        endif;

        return $query;
    }

    /**
     * @param $query
     * @return mixed
     * @param $attributes
     */
    protected function getCustomQuery($query , $attributes)
    {
        $operator = "=";
        foreach ($attributes as $key => $attribute) :
            if (!empty($attribute)) :
                $raw = explode(":", $key);
                if (count($raw) > 1) :
                    $operator = end($raw);
                endif;
                $query = $query->where(array_first($raw), $operator, $attribute);
            endif;
        endforeach;
        return $query;
    }
}