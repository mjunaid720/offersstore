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
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Description of Store
 *
 * @author Asif javaid
 */
class Store extends Authenticatable {

    protected $primaryKey = 'store_id';
    protected $table = 'stores';

    use SoftDeletes;

    protected $dates = ['deleted_at'];

}
