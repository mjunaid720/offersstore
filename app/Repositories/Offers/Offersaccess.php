<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Offersaccess
 *
 * @author Asif javaid
 */

namespace App\Repositories\Offers;

use App\BaseAccess;
use App;
use App\Models\Offer;
use App\Models\OfferImages;
use App\Models\Categories;
use App\Models\OfferCategory;
use App\Models\Store;
use Hash;
use Request;
use DB;
use File;
use Carbon\Carbon;

class Offersaccess extends BaseAccess implements OffersInterface{

    private $offer;
    private $offerImages;
    private $categories;
    private $store;

    //put your code here
    public function __construct() {
        $this->offer = new Offer();
        $this->offerImages = new OfferImages();
        $this->categories = new Categories();
        $this->store = new Store();
    }

    public function saveOffer($data = array()) {
        $userData = getCurrentData();

        $userId = isset($userData['id']) ? $userData['id'] : null;
        $this->offer->user_id = $userId;
        // DB::transaction(function () {
        $this->offer->store_id = $data['store_id'];
        $this->offer->offer_title = $data['offer_title'];
        $this->offer->offer_slug = str_slug($data['offer_title'], '-');
        $this->offer->offer_price = $data['offer_price'];
        $this->offer->offer_quantity = $data['offer_quantity'];
        if(isset($data['flyer_id']) && $data['flyer_id']!=''){
             $this->offer->flyer_id = $data['flyer_id'];
        }
        $this->offer->offer_start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . " +25 day"));
        $this->offer->offer_end_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . " +25 day"));
        $this->offer->offer_status = "1";

        $this->offer->save();
        $offerId = $this->offer->offer_id;
        $destinationPath = public_path() . '/uploads/offerimages';
        if (isset($data['primary_image']) && $data['primary_image'] != "") {
            $this->offer->where('offer_id', $offerId)->update(['primary_image' => $data['primary_image']]);
            $offerImages = new OfferImages();
            $offerImages->offer_id = $offerId;
            $offerImages->image_name = $data['primary_image'];
            $offerImages->save();
        }

        if (isset($data['secondary_images']) && $data['secondary_images'] != "") {
            $imageNames = $this->multiPleImagesUpload($data['secondary_images'], $destinationPath);

            foreach ($imageNames as $in) {
                $offerImages = new OfferImages();
                $offerImages->offer_id = $offerId;
                $offerImages->image_name = $in;
                $offerImages->save();
            }
        }
        //categories save

        $categories = isset($data['offer_category']) ? $data['offer_category'] : NULL;
        if (!empty($offerId)) {
            if ((count($categories) > 0) && is_array($categories)) {
                foreach ($categories as $catIds) {
                    if ($catIds) {
                        $offerCategory = new OfferCategory();
                        $offerCategory->category_id = $catIds;
                        $offerCategory->offer_id = $offerId;
                        $offerCategory->save();
                    }
                }
            }
        }
        return true;
    }

    public function offerById($offerId) {
        $arr = array();
        $query = $this->offer->where('offer_id', $offerId)->first();
        //$query= DB::table('offers')->join('offer_images','offers.offer_id', '=', 'offer_images.offer_id')->where('offers.offer_id',$offerId)->first();

        if (!empty($query)) {
            $offerdata = $query->toArray();
            $query = DB::table('offer_images')->select('offer_id', 'image_name')->where('offer_id', $offerdata['offer_id'])->get();
            $offerCategory = new OfferCategory();
            $catQuery = $offerCategory->select('offer_id', 'category_id')->where('offer_id', $offerId)->get();
            if (!empty($catQuery)) {
                $res['categories'] = objToArray($catQuery);
                $arr = array_merge($offerdata, $res['categories']);
            }
            $res['images'] = objToArray($query);
            if (isset($res['images']) && $res['images'] != "") {
                $arr = array_merge($offerdata, $res);
                return $arr;
            } else {
                return $offerdata;
            }
        } else {
            return false;
        }
    }

    public function getCategoriesbyOfferCount() {
        $query = $this->categories->select('category_id', 'ctegory_title', 'parent_id')->where('status', '1')->get();
    }

    public function updateOffer($postedData = array()) {
        $data = array();
        $data['offer_title'] = $postedData['offer_title'];
        $data['offer_price'] = $postedData['offer_price'];
        $data['offer_quantity'] = $postedData['offer_quantity'];
        if (isset($postedData['primary_image']) && $postedData['primary_image'] != '') {
            $data['primary_image'] = $postedData['primary_image'];
        }
        $data['offer_start_date'] = $postedData['offer_start_date'];
        $data['offer_end_date'] = $postedData['offer_end_date'];
        $this->offer->where('offer_id', $postedData['offer_id'])->update($data);

        //multiple images updates
        $destinationPath = public_path() . '/uploads/offerimages';
        if (isset($postedData['secondary_images']) && $postedData['secondary_images'] != "") {
            DB::table('offer_images')->where('offer_id', $postedData['offer_id'])->delete();

            $imageNames = $this->multiPleImagesUpload($postedData['secondary_images'], $destinationPath);

            foreach ($imageNames as $in) {
                $offerImages = new OfferImages();
                $offerImages->offer_id = $postedData['offer_id'];
                $offerImages->image_name = $in;
                $offerImages->save();
            }
        }
        // categories update
        $categories = isset($postedData['offer_category']) ? $postedData['offer_category'] : NULL;
        if ((count($categories) > 0) && is_array($categories)) {
            DB::table('offer_selected_category')->where('offer_id', $postedData['offer_id'])->delete();
            foreach ($categories as $catIds) {
                if ($catIds) {
                    $offerCategory = new OfferCategory();
                    $offerCategory->category_id = $catIds;
                    $offerCategory->offer_id = $postedData['offer_id'];
                    $offerCategory->save();
                }
            }
        }
        return true;
    }

    public function imagesUpload($image, $destinationPath) {

        if (!empty($image)) {
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0775, true);
            }
            $fileExtension = $image->getClientOriginalExtension();
            $fileName = time() . '.' . $fileExtension;
            $image->move($destinationPath, $fileName);
            return $fileName;
        }
    }

    public function saveOfferImages($offerId, $imageName) {
        $this->offerImages->offer_id = $offerId;
        $this->offerImages->image_name = $imageName;
        $this->offerImages->save();
    }

    public function multiPleImagesUpload($images, $destinationPath) {
        $destinationPath = public_path() . '/uploads/offerimages';
        $imagesarray = array();

        if (!empty($images)) {
            foreach ($images as $im) {
                $fileExtension = $im->getClientOriginalExtension();
                $fileName = time() . '.' . $fileExtension;
                $im->move($destinationPath, $fileName);
                $imagesarray[] = $fileName;
            }
        }
        return $imagesarray;
    }

    public function getCategories($parentId = '') {
        $arr = array();
        $query = $this->categories->select('category_id', 'ctegory_title', 'parent_id', 'image_name')
                ->where('parent_id', 0);
        $res = $query->get();
        if (!empty($res)) {
            $categories = $res->toArray();
            foreach ($categories as $ca) {
                $data = array();
                $data['parent_categories'] = $ca['ctegory_title'];
                $data['parent_image_name'] = $ca['image_name'];
                $quer2 = $this->categories->select('ctegory_title', 'category_id', 'image_name')->where('parent_id', $ca['category_id'])->limit(3)->get();

                $childs = objToArray($quer2);
                foreach ($childs as $child) {
                    $data['child_categories'][] = $child['ctegory_title'];
                    $data['image_name'] = $child['image_name'];
                    $totalOffers = DB::table('offer_selected_category')->join('offers', 'offers.offer_id', '=', 'offer_selected_category.offer_id')
                                    ->where('offer_selected_category.category_id', $child['category_id'])->get();
                    //  print_r($totalOffers);

                    $data['total_offers'] = count($totalOffers);
                }
                $arr[] = $data;
            }
            return $arr;


            //  return $response;
        } else {
            return false;
        }
    }
    public function childCategories($parentId=''){
         $query = $this->categories->select('category_id', 'ctegory_title', 'parent_id', 'image_name')
                ->where('parent_id', $parentId);
        $res = $query->get();
        if (!empty($res)) {
            return $categories = $res->toArray();
        }else{
            return false;
        }
    }

    public function homeCategories($parentId = '') {
        $query = $this->categories->select('category_id', 'ctegory_title', 'parent_id', 'image_name')->where('status', '1');
        if ($parentId == '') {
            $query->where('parent_id', 0);
        } else {
            $query->where('parent_id', $parentId);
        }
        $res = $query->get();
        if (!empty($res)) {
            $response = $res->toArray();
            return $response;
        } else {
            return false;
        }
    }
      
    public function allOffers() {
        $query = $this->offer->where('offer_status', '1')->paginate(10);
        if (!empty($query)) {
            $offerdata = $query->toArray();
            return $offerdata;
        } else {
            return false;
        }
    }

    public function allTrendingOffers() {
        $query = $this->offer->where('offer_status', '1')->take(20)->orderBy('created_at', 'ASC')->get();
        if (!empty($query)) {
            $offerdata = $query->toArray();
            return $offerdata;
        } else {
            return false;
        }
    }

    public function storeOffers($storeId) {
        $query = $this->offer->where('offer_end_date', '>=', Carbon::today())->where('store_id', $storeId)->where('offer_status', '1')->paginate(10);
        if (!empty($query)) {
            $offerdata = $query->toArray();
            return $offerdata;
        } else {
            return false;
        }
    }

    public function productsByCatId($catId) {
        $data = array();
        $query = DB::table('offer_selected_category')->join('offers', 'offers.offer_id', '=', 'offer_selected_category.offer_id')->where('offer_selected_category.category_id', $catId)->get();
        if (!empty($query)) {
            $offersData = objToArray($query);
            $data['offers'] = $offersData;
            $data['totaloffers'] = count($offersData);
            return $data;
        } else {
            return false;
        }
    }

    public function countTotaloffers($catId) {
        // echo $catId."<br>";
        $query3 = DB::table('offer_selected_category')->join('offers', 'offers.offer_id', '=', 'offer_selected_category.offer_id')
                        ->where('offer_selected_category.category_id', $catId)->get();
        $offers = objToArray($query3);
        return $query3;
    }

    public function deleteOffers($offerId) {
        $userData = getCurrentData();
        $userId = $userData['id'];
        if (isset($userId) && $userId != '') {

            $offerCount = $this->offer->where('user_id', $userId)->where('offer_id', $offerId)->count();
            if ($offerCount > 0) {
                $this->offer->where('offer_id', $offerId)->delete();
                return true;
            } else {
                return false;
            }
        }
    }

    public function offerDetail($offerId) {

        $data = array();
        $query = $this->offer->where('offer_id', $offerId)->first();

        if (!empty($query)) {
            $offerData = $query->toArray();
            $data['offer_data'] = $offerData;

            $imagesQuery = $this->offerImages->select('image_name')->where('offer_id', $offerId)->get();
            if (!empty($imagesQuery)) {
                $imagesData = objToArray($imagesQuery);
                foreach ($imagesData as $img) {
                    $data['offer_images'][] = $img['image_name'];
                }
            }
            $offerCategory = new OfferCategory();
            $catQuery = $query3 = DB::table('offer_selected_category')->select('ctegory_title')->join('categories', 'categories.category_id', '=', 'offer_selected_category.category_id')
                            ->where('offer_selected_category.offer_id', $offerId)->get();
            if (!empty($catQuery)) {
                $catData = objToArray($catQuery);
                foreach ($catData as $cd) {
                    $data['offer_categories'][] = $cd['ctegory_title'];
                }
            }
           $storeQuery= $this->store->select('store_name')->where('store_id',$offerData['store_id'])->first();
            if (!empty($storeQuery)) {;
                $storeData=objToArray($storeQuery);
                $data['store_name'] =$storeData['store_name'] ;
                
            }
            return $data;
        }
    }

}
