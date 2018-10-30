<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Validator;
use Crypt;
//use App\Repositories\Offer\Offeraccess;
use App\Repositories\Offers\Offersaccess;
use File;

class OfferController extends BaseController {

    private $offerobj;
    private $request;

    public function __construct(Request $request) {

        $this->offerobj = new Offersaccess();
        $this->request = $request;
    }

    public function createOffer() {
        $validator = Validator::make($this->request->all(), [
                    'store_id' => 'required',
                    'offer_title' => 'required',
                    'offer_price' => 'required',
                    'offer_quantity' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $input = $this->request->all();
        if ($this->request->hasFile('primary_image')) {
            $primaryImage = $this->request->file('primary_image');
            if (!empty($primaryImage)) {
                $destinationPath = public_path() . '/uploads/offerprimaryimage';
                $imageName = $this->offerobj->imagesUpload($primaryImage, $destinationPath);
                $input['primary_image'] = $imageName;
            }
        }
        $this->offerobj->saveOffer($input);
        if ($this->request->hasFile('secondary_images')) {
            $secondaryImages = $this->request->file('secondary_images');
            $input['secondary_images'] = $secondaryImages;
        }
        $response = $this->sendResponse('success', 'Offer created successfully.');
        if ($response == true) {
            return $this->sendResponse('success', 'Offer created successfully.');
        } else {
            return $this->sendResponse('error', 'Some thing went wrong.');
        }
    }

    public function getAllCategoriesByNoOffer() {
        $response = $this->offerobj->getCategoriesbyOfferCount();
    }

    public function updateOffer() {
        $validator = Validator::make($this->request->all(), [
                    'offer_id' => 'required',
                    'offer_title' => 'required',
                    'offer_price' => 'required',
                    'offer_quantity' => 'required',
                    'offer_category' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $input = $this->request->all();
        if ($this->request->hasFile('primary_image')) {
            $primaryImage = $this->request->file('primary_image');
            if (!empty($primaryImage)) {
                $destinationPath = public_path() . '/uploads/offerprimaryimage';
                $imageName = $this->offerobj->imagesUpload($primaryImage, $destinationPath);
                $input['primary_image'] = $imageName;
            }
        }
        if ($this->request->hasFile('secondary_images')) {
            $secondaryImages = $this->request->file('secondary_images');
            $input['secondary_images'] = $secondaryImages;
        }
        $response = $this->offerobj->updateOffer($input);
        if ($response == true) {
            return $this->sendResponse('success', "offer updated successfully");
        } else {
            return $this->sendResponse('error', 'Some thing went wrong.');
        }
    }

    public function getOfferById($offerId) {
        $response = $this->offerobj->offerById($offerId);
        if (!empty($response)) {
            return $this->sendResponse('success', $response);
        } else if (empty($response)) {
            return $this->sendResponse('success', 'no record found.');
        } else {
            return $this->sendResponse('error', 'Some thing went wrong.');
        }
    }

    public function getAllOffers() {
        $response = $this->offerobj->allOffers();
        if (!empty($response)) {
            return $this->sendResponse('success', $response);
        } else if (empty($response)) {
            return $this->sendResponse('success', 'no record found.');
        } else {
            return $this->sendResponse('error', 'Some thing went wrong.');
        }
    }

    public function getTrendOffers() {
        $response = $this->offerobj->allTrendingOffers();
        if (!empty($response)) {
            return $this->sendResponse('success', $response);
        } else if (empty($response)) {
            return $this->sendResponse('success', 'no record found.');
        } else {
            return $this->sendResponse('error', 'Some thing went wrong.');
        }
    }

    public function getstoreOffers() {

        $validator = Validator::make($this->request->all(), [
                    'store_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $storeId = $this->request->input('store_id');
        $response = $this->offerobj->storeOffers($storeId);
        return $this->sendResponse('success', $response);
    }

    public function getAllCategories() {
        $categories = $this->offerobj->getCategories();
        if (!empty($categories)) {
            return $this->sendResponse('success', $categories);
        } else {
            return $this->sendResponse('error', 'Some thing went wrong.');
        }
    }

    public function getHomeCategories() {
        $categories = $this->offerobj->homeCategories();
        if (!empty($categories)) {
            return $this->sendResponse('success', $categories);
        } else {
            return $this->sendResponse('error', 'Some thing went wrong.');
        }
    }

    public function getChildCategories($parentId = '') {
        $categories = $this->offerobj->childCategories($parentId);
        if (!empty($categories)) {
            return $this->sendResponse('success', $categories);
        } else {
            return $this->sendResponse('success', []);
        }
    }

    public function getproductsByCatId() {
        $validator = Validator::make($this->request->all(), [
                    'cat_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $catId = $this->request->input('cat_id');
        $response = $this->offerobj->productsByCatId($catId);
        return $this->sendResponse('success', $response);
    }

    public function deleteOffers() {
        $validator = Validator::make($this->request->all(), [
                    'offer_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $offerId = $this->request->input('offer_id');
        $response = $this->offerobj->deleteOffers($offerId);
        if ($response == true) {
            return $this->sendResponse('success', 'offer deleted successfully.');
        } else {
            return $this->sendError('error', 'Some thing went wrong.');
        }
    }

    public function getTotaloffers($id) {
        $parntid = $this->offerobj->countTotaloffers($id);
        print_r($parntid);
    }

    function getOfferDetail() {

        $validator = Validator::make($this->request->all(), [
                    'offer_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $offerId = $this->request->input('offer_id');
        $reponse = $this->offerobj->offerDetail($offerId);
        if (!empty($reponse)) {
             return $this->sendResponse('success', $reponse);
        } else {
            return $this->sendError('error', 'Some thing went wrong.');
        }
    }

}
