<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Offersaccess
 *
 * @author Muhammad Junaid Aslam
 */

namespace App\Repositories\Flyer;

use App\BaseAccess;
use App;
use App\Models\Offer;
use App\Models\Flyer;
use Hash;
use Request;
use DB;
use File;
use Carbon\Carbon;

class Flyeraccess extends BaseAccess implements FlyerInterface{

    private $flyer;

    //put your code here
    public function __construct() {
        $this->flyer = new Flyer();
    }

    public function saveFlyer($data) {
        $userData = getCurrentData();
        $userId = isset($userData['id']) ? $userData['id'] : null;
        if (!empty($userId)) {
            if (isset($data['flyer_image']) && $data['flyer_image'] != '') {
                $destinationPath = public_path() . '/uploads/flyer';
                $imageName = $this->imagesUpload($data['flyer_image'], $destinationPath);
                $this->flyer->image_name = $imageName;
            }
            $this->flyer->title = isset($data['flyer_title']) ? $data['flyer_title'] : '';
            $this->flyer->save();
            return true;
        } else {
            return false;
        }

        //categories save
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
    public function getFlyers(){
        $query= $this->flyer->select('id','title','image_name')->get();
        if(!empty($query)){
            return objToArray($query);
        }else{
            return false;
        }
        
    }

}
