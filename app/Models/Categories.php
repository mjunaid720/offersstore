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
 * Description of Categories
 *
 * @author Asif javaid
 */
class Categories extends Authenticatable {

    protected $primaryKey = 'category_id';
    protected $table = 'categories';

    //put your code here
}
