<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Repositories\Store;

use App\BaseAccess;
use App;
use App\Models\Store;
use App\Models\Userrole;
use Hash;
use Request;
use DB;
use File;

/**
 * Description of Useraccess
 *
 * @author Asif
 */
class Storeaccess extends BaseAccess implements StoreInterface {

    private $store;
    private $userrole;

    public function __construct() {
        $this->store = new Store();
        $this->userrole = new Userrole();
    }

    public function saveStore($data = array()) {
        // DB::transaction(function () {
        $userData = getCurrentData();

        $userId = isset($userData['id']) ? $userData['id'] : null;
        $this->store->user_id = $userId;
        $this->store->store_name = $data['store_name'];
        $this->store->store_slug = str_slug($data['store_name'], '-');
        $this->store->store_email = $data['store_email'];
        $this->store->store_country = $data['store_country'];
        $this->store->store_state = $data['store_state'];
        $this->store->street_address = $data['street_address'];
        $this->store->store_city = $data['store_city'];
        $this->store->zip_code = $data['zip_code'];
        $this->store->contact_no = $data['contact_no'];
        $this->store->store_status = '1';
        if (isset($data['store_logo'])) {
            $this->store->store_logo = $data['store_logo'];
        }

        $this->store->save();
        $Userrolecount = $this->userrole->where('user_id', '=', 1)->where('role_id', '=', 2)->count();
        if ($Userrolecount == 0) {
            $this->userrole->user_id = 1;
//        role for seller is 2
            $this->userrole->role_id = 2;
            $this->userrole->save();
        }
        return true;
        //  });
    }

    public function getAllStores($userId = '') {
        $userData = getCurrentData();
        $userId = isset($userData['id']) ? $userData['id'] : null;
        if (!empty($userId)) {
            $query = $this->store->where('user_id', $userId)->get();
            return $query;
        } else {
            $query = $this->store->get();
            return $query;
        }
    }

    public function logoUpload($image) {
        if (!empty($image)) {

            $destinationPath = public_path() . '/uploads/storelogo';
            $fileExtension = $image->getClientOriginalExtension();
            $fileName = time() . '.' . $fileExtension;
            $image->move($destinationPath, $fileName);
            return $fileName;
        }
    }

    public function StoreDataById($storeId) {
        $storeData=array(); 
        $query = $this->store->where('store_id', $storeId)->first();
        
        if (!empty($query)) {
            $data= $query->toArray();
            $storeData['store_id']=$data['store_id'];
            $storeData['store_name']=$data['store_name'];
            $storeData['store_email']=$data['store_slug'];
            $storeData['store_logo']=$data['store_logo'];
            $storeData['store_country']= getCountryById($data['store_country']);
            $storeData['store_state']= getStatesById($data['store_state']);
            $storeData['store_city']=$data['store_city'];
            $storeData['zip_code']=$data['zip_code'];
            $storeData['contact_no']=$data['contact_no'];
            $storeData['store_status']=$data['store_status'];
            $storeData['created_at']=$data['created_at'];
            return $storeData;
        } else {
            return false;
        }
    }

    public function updateStore($postedData = array()) {
        $data = array();
        if (isset($postedData) && $postedData != "") {
            $data['store_name'] = $postedData['store_name'];
            $data['store_email'] = $postedData['store_email'];
            $data['street_address'] = $postedData['street_address'];
            $data['store_country'] = $postedData['store_country'];
            $data['store_state'] = ($postedData['store_state']);
            $data['store_city'] = $postedData['store_city'];
            $data['zip_code'] = $postedData['zip_code'];
            $data['store_name'] = $postedData['store_name'];
            $data['contact_no'] = $postedData['contact_no'];
            if (isset($postedData['store_logo']) && $postedData['store_logo'] != "") {
                $data['store_logo'] = $postedData['store_logo'];
            }
            $this->store->where('store_id', '=', $postedData['store_id'])->update($data);
            return true;
        } else {
            return false;
        }
    }

    public function delStore($storeId) {
        $userData = getCurrentData();
        $userId = $userData['id'];
        if (isset($userId) && $userId != '') {
            $storeCount = $this->store->where('user_id', $userId)->where('store_id', $storeId)->count();
            if ($storeCount > 0) {
                $this->store->where('store_id', $storeId)->delete();
                return true;
            } else {
                return false;
            }
        }
    }

}
