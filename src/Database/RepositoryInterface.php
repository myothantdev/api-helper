<?php
/**
 * Created by PhpStorm.
 * User: myothantkyaw
 * Date: 12/8/19
 * Time: 11:00 AM
 */

namespace Tech\APIHelper\Database;

/**
 * Interface RepositoryInterface
 * @package Tech\APIHelper\Database
 */
interface RepositoryInterface
{
    /**
     * @param $params
     * @param $withResource
     * @return mixed
     */
    public function getData($params, $withResource);

    /**
     * @param $id
     * @param $withResource
     * @return mixed
     */
    public function getDataById($id, $withResource);

    /**
     * @param array $params
     * @return mixed
     */
    public function insertData(array $params);

    /**
     * @param $params
     * @param $id
     * @return mixed
     */
    public function updateData($params, $id);

    /**
     * @param $id
     * @return mixed
     */
    public function deleteData($id);

    /**
     * @return mixed
     */
    public function count();
}