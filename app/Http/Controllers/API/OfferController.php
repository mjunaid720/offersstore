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
use App\Repositories\Offers\OffersInterface;
use File;

class OfferController extends BaseController {

    private $offerRepo;
    private $request;

    public function __construct(Request $request, OffersInterface $offerRepository) {

        $this->offerRepo = $offerRepository;
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
                $imageName = $this->offerRepo->imagesUpload($primaryImage, $destinationPath);
                $input['primary_image'] = $imageName;
            }
        }
        $this->offerRepo->saveOffer($input);
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
                $imageName = $this->offerRepo->imagesUpload($primaryImage, $destinationPath);
                $input['primary_image'] = $imageName;
            }
        }
        if ($this->request->hasFile('secondary_images')) {
            $secondaryImages = $this->request->file('secondary_images');
            $input['secondary_images'] = $secondaryImages;
        }
        $response = $this->offerRepo->updateOffer($input);
        if ($response == true) {
            return $this->sendResponse('success', "offer updated successfully");
        } else {
            return $this->sendResponse('error', 'Some thing went wrong.');
        }
    }

    public function getOfferById($offerId) {
        $response = $this->offerRepo->offerById($offerId);
        if (!empty($response)) {
            return $this->sendResponse('success', $response);
        } else if (empty($response)) {
            return $this->sendResponse('success', 'no record found.');
        } else {
            return $this->sendResponse('error', 'Some thing went wrong.');
        }
    }

    public function getAllOffers() {
        $response = $this->offerRepo->allOffers();
        if (!empty($response)) {
            return $this->sendResponse('success', $response);
        } else if (empty($response)) {
            return $this->sendResponse('success', 'no record found.');
        } else {
            return $this->sendResponse('error', 'Some thing went wrong.');
        }
    }

    public function getTrendOffers() {
        $response = $this->offerRepo->allTrendingOffers();
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
        $response = $this->offerRepo->storeOffers($storeId);
        return $this->sendResponse('success', $response);
    }

    public function getAllCategories() {
        $categories = $this->offerRepo->getCategories();
        if (!empty($categories)) {
            return $this->sendResponse('success', $categories);
        } else {
            return $this->sendResponse('error', 'Some thing went wrong.');
        }
    }

    public function getHomeCategories() {
        $categories = $this->offerRepo->homeCategories();
        if (!empty($categories)) {
            return $this->sendResponse('success', $categories);
        } else {
            return $this->sendResponse('error', 'Some thing went wrong.');
        }
    }

    public function getChildCategories($parentId = '') {
        $categories = $this->offerRepo->childCategories($parentId);
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
        $response = $this->offerRepo->productsByCatId($catId);
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
        $response = $this->offerRepo->deleteOffers($offerId);
        if ($response == true) {
            return $this->sendResponse('success', 'offer deleted successfully.');
        } else {
            return $this->sendError('error', 'Some thing went wrong.');
        }
    }

    public function getTotalOffers($id) {
        $count = $this->offerRepo->countTotaloffers($id);
        return $count;
    }

    function getOfferDetail() {

        $validator = Validator::make($this->request->all(), [
                    'offer_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $offerId = $this->request->input('offer_id');
        $reponse = $this->offerRepo->offerDetail($offerId);
        if (!empty($reponse)) {
             return $this->sendResponse('success', $reponse);
        } else {
            return $this->sendError('error', 'Some thing went wrong.');
        }
    }

}
