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

namespace App\Repositories\Bookmark;

use App\BaseAccess;
use App;
use App\Models\Bookmark;
use Hash;
use Request;
use DB;
use File;
use Carbon\Carbon;

class Bookmarkaccess extends BaseAccess implements  BookmarkInterface{

    private $bookmark;

    //put your code here
    public function __construct() {
        $this->bookmark = new Bookmark();
    }

    public function saveBookmark($data) {
        $message = '';
        $userData = getCurrentData();
        $userId = isset($userData['id']) ? $userData['id'] : null;
        if (!empty($userId)) {
            $offerCount = $this->bookmark->where('offer_id', $data['offer_id'])->where('user_id', $userId)->count();

            if ($offerCount > 0) {
                $this->bookmark->where('offer_id', $data['offer_id'])->delete();
                $message = 'offer un book marked successfully';
                return $message;
            } else {
                $this->bookmark->offer_id = $data['offer_id'];
                $this->bookmark->user_id = $userId;
                $this->bookmark->save();
                $message = 'offer book marked successfully';
                return $message;
            }
        } else {
            return false;
        }
    }

    public function userBookmarks() {
        $userData = getCurrentData();
        $userId = isset($userData['id']) ? $userData['id'] : null;
        $query = DB::table('bookmarked_offers')->join('offers', 'offers.offer_id', '=', 'bookmarked_offers.offer_id')->where('bookmarked_offers.user_id',$userId)->get();
       
       // $query = $this->bookmark::find($userId)->with->offers()->get();
        if (!empty($query)) {
            return objToArray($query);
        } else {
            return false;
        }
    }

    public function delBookmark($data) {
        $userData = getCurrentData();
        $userId = isset($userData['id']) ? $userData['id'] : null;
        $this->bookmark->where('offer_id', $data['offer_id'])->where('user_id', $userId)->delete();
        return true;
    }

}
