<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Description of Bookmark
 *
 * @author Asif javaid
 */
class Bookmark extends Authenticatable {

    //put your code here
    protected $table = 'bookmarked_offers';

    public function offers() {
        return $this->hasOne('App\Models\Offer', 'offer_id', 'offer_id');
    }

}
