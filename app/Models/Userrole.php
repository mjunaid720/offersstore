<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Userrole extends Authenticatable {

    protected $primaryKey = 'role_user_id';
    protected $table = 'role_user';
    public $timestamps = false;
    

}
