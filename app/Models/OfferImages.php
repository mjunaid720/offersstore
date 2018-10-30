<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



/**
 * Description of OfferImages
 *
 * @author Asif javaid
 */

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Description of Store
 *
 * @author Asif javaid
 */
class OfferImages extends Authenticatable {

//    protected $primaryKey = 'offer_id';
    protected $table = 'offer_images';
    public $timestamps = false;

}
