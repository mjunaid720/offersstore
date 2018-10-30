<?php
/**
 * Created by PhpStorm.
 * User: junai
 * Date: 30/10/2018
 * Time: 01:17
 */

namespace App\Repositories\Store;


interface StoreInterface
{
    public function saveStore($data = array());
    public function getAllStores($userId = '');
    public function logoUpload($image);
    public function StoreDataById($storeId);
    public function updateStore($postedData = array());
    public function delStore($storeId);


}