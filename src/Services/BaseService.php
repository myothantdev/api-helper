<?php

namespace Tech\APIHelper\Services;

use Tech\APIHelper\Resources\Formatter;
use Illuminate\Database\Eloquent\Model;
use Tech\APIHelper\Exceptions\NotFoundException;
use Tech\APIHelper\Exceptions\FatalErrorException;
use Tech\APIHelper\Database\Eloquent\BaseRepository;
use Tech\APIHelper\Exceptions\DeleteResourceNotFound;
use Tech\APIHelper\Exceptions\UpdateResourceNotFound;

/**
 * Class BaseService
 * @package Tech\APIHelper\Service
 */
abstract class BaseService extends CommonService
{
    /**
     * @var Formatter
     */
    protected $response;

    /**
     * @var int
     */
    protected $limit = 30;

    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @var BaseRepository
     */
    protected $repository;

    protected $allowable = [
        "limit", "offset", "from", "to", "deleted"
    ];

    protected $repositoryInterface;

    /**
     * BaseService constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->repository = new BaseRepository($model);
        $this->response = Formatter::factory();
    }

    /**
     * @param int $count
     * @param int $total
     * @param $attributes
     * @return Formatter
     */
    public function setMetaResponse(int $count, int $total, $attributes)
    {
        $limit = empty($attributes["limit"]) ? $this->limit : $attributes["limit"];
        $offset = empty($attributes["offset"]) ? $this->offset : $attributes["offset"];

        return $this->response->setCount($count)->setTotal($total)
            ->setMetaData((int) $offset, (int) $limit);
    }

    /**
     * @param $params
     * @param null $withResource
     * @return array
     * @throws FatalErrorException
     */
    public function getAll($params, $withResource = null)
    {
        try {
            $input = $params->only($this->allowable);
            $count = $this->repository->count();
            $data = $this->repository->getData($input, $withResource);
            $this->setMetaResponse(count($data), $count, $params);
            return $this->response->make($data);
        } catch (\Exception $exception) {
            throw new FatalErrorException($exception->getMessage());
        }
    }

    /**
     * @param $id
     * @param null $withResource
     * @return array
     * @throws NotFoundException
     */
    public function getDataById($id, $withResource = null)
    {
        $this->id($id);
        $data = $this->repository->getDataById($id, $withResource);
        if (empty($data)) :
            throw new NotFoundException();
        endif;
        return $this->response->make($data->toArray());
    }

    /**
     * @param $params
     * @return array
     * @throws FatalErrorException
     */
    public function insert($params)
    {
        try {
            $data = $this->repository->insertData($params);
            return $this->response->make($this->arrayStringSort($data));
        } catch (\Exception $exception) {
            throw new FatalErrorException($exception->getMessage());
        }
    }

    /**
     * @param $params
     * @param $id
     * @return array
     * @throws FatalErrorException
     * @throws UpdateResourceNotFound
     */
    public function update($params, $id)
    {
        if (!$this->repository->isExist($id)) :
            throw new UpdateResourceNotFound();
        endif;

        try {
            $data = $this->repository->updateData($params, $id);
            return $this->response->make($data);
        } catch (\Exception $exception) {
            throw new FatalErrorException($exception->getMessage());
        }
    }

    /**
     * @param $id
     * @return array
     * @throws DeleteResourceNotFound
     * @throws FatalErrorException
     */
    public function delete($id)
    {
        $this->id($id);

        if (!$this->$this->repository->isExist($id)) :
            throw new DeleteResourceNotFound();
        endif;

        try {
            $this->repository->deleteData($id);
            return $this->response->make([
                "message" => "The $id was successfully deleted."
            ]);
        } catch (\Exception $exception) {
            throw new FatalErrorException($exception->getMessage());
        }
    }

    public function __clone(){}
}