<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use Crypt;
use App\Repositories\Store\StoreInterface;
use File;
use Log;

class StoreController extends BaseController {

    private $storeRepo;
    private $request;

    public function __construct(Request $request, StoreInterface $storeRepository) {
        $this->storeRepo = $storeRepository;
        $this->request = $request;
    }

    public function createStore() {
        Log::emergency($this->request->file('store_logo'));
        // print_rr($this->request->file('store_logo')); exit;
        $validator = Validator::make($this->request->all(), [
                    'store_name' => 'required',
                    'store_email' => 'required|email',
                    'street_address' => 'required',
                    'store_country' => 'required',
                    'store_state' => 'required',
                    'store_city' => 'required',
                    'zip_code' => 'required',
                    'contact_no' => 'required',
                        //'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $input = $this->request->all();
        if ($this->request->hasFile('store_logo')) {
            $imageObj = $this->request->file('store_logo');
            if (!empty($imageObj)) {
                $imageName = $this->storeRepo->logoUpload($imageObj);
            }
            $input['store_logo'] = $imageName;
        }

        $response = $this->storeRepo->saveStore($input);
        if ($response == true) {
            return $this->sendResponse('success', 'Store created successfully.');
        } else {
            return $this->sendResponse('error', 'Some thing went wrong.');
        }
    }

    public function getStores() {
        $response = $this->storeRepo->getAllStores();
        if (!empty($response)) {
            return $this->sendResponse('success', $response);
        } else {
            return $this->sendResponse('error', 'Some thing went wrong.');
        }
    }

    public function getStoreDataById() {
        $validator = Validator::make($this->request->all(), [
                    'store_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $storeId = $this->request->input('store_id');
        $response = $this->storeRepo->StoreDataById($storeId);
        if (!empty($response)) {
            return $this->sendResponse('success', $response);
        } else {
            return $this->sendResponse('error', 'no record found.');
        }
    }

    public function updateStore() {
        $validator = Validator::make($this->request->all(), [
                    'store_name' => 'required',
                    'store_email' => 'required|email',
                    'street_address' => 'required',
                    'store_country' => 'required',
                    'store_state' => 'required',
                    'store_city' => 'required',
                    'zip_code' => 'required',
                    'contact_no' => 'required',
                    'store_id' => 'required',
                        //  'store_logo' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $input = $this->request->all();
        if ($this->request->hasFile('store_logo') && $this->request->file('store_logo') != '') {
            $imageObj = $this->request->file('store_logo');
            if (isset($imageObj) && $imageObj != "") {
                $imageName = $this->storeRepo->logoUpload($imageObj);
            }
            $input['store_logo'] = $imageName;
        }
        $response = $this->storeRepo->updateStore($input);
        if (!empty($response)) {
            return $this->sendResponse('success', "store updated successfully");
        } else {
            return $this->sendResponse('error', 'Some thing went wrong.');
        }
    }

    public function getStoreCountries() {
        $countries = getAllCountries();
        if (!empty($countries)) {
            return $this->sendResponse('success', $countries);
        } else {
            return $this->sendResponse('error', 'Some thing went wrong.');
        }
    }

    public function getStatesByCountryId($countyId) {
        if (!empty($countyId)) {
            $states = getStatesByCountryId($countyId);
            if (!empty($states)) {
                return $this->sendResponse('success', $states);
            } else {
                return $this->sendResponse('error', 'Some thing went wrong.');
            }
        }
    }

    public function storeDelete() {
        $validator = Validator::make($this->request->all(), [
                    'store_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $input = $this->request->input();
        $response = $this->storeRepo->delStore($input);
        if ($response == true) {
            return $this->sendResponse('success', 'store deleted successfully');
        } else {
            return $this->sendError('error', 'Some thing went wrong.');
        }
    }

}
