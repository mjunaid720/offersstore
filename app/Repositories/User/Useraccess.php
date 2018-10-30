<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Repositories\User;

use App\BaseAccess;
use App;
use App\Models\User;
use App\Models\Userrole;
use Hash;
use Request;
use Carbon\Carbon;
use Crypt;
use URL;
use DB;

/**
 * Description of Useraccess
 *
 * @author Asif
 */
class Useraccess extends BaseAccess implements UserInterface{

    private $_user;
    private $_userRole;

    public function __construct() {
        $this->_user = new User();
        $this->_userRole = new Userrole();
    }

    public function userRegister($data) {
        // DB::transaction(function () {
        $this->_user->name = $data['name'];
        $this->_user->email = $data['email'];
        $this->_user->password = $data['password'];
        $this->_user->register_source = 'website';
        $this->_user->ip_address = Request::ip();
        $this->_user->save();
        $lastInsertid = $this->_user->id;
        $this->_userRole->user_id = $lastInsertid;
        $this->_userRole->role_id = 3;
        $this->_userRole->save();
        return true;
        //  });
    }

    public function getUserRole($email = '') {
        if (!empty($email)) {
            $query = $this->_user->where('email', $email)->select('id')->first();
            if (!empty($query)) {
                $userId = $query->toArray();
                $roles = DB::table('role_user')->select('roles.role_id', 'roles.role_name')->join('roles', 'roles.role_id', '=', 'role_user.role_id')->where('role_user.user_id', '=', $userId['id'])->get()->toArray();
                $roleResult = $this->objToArray($roles);
                $prefix = $userRole = '';
                foreach ($roleResult as $rr) {
                    $userRole .= $prefix . '' . $rr['role_name'] . '';
                    $prefix = ' ';
                }
                return $userRole;
            } else {
                return false;
            }
        }
    }

    public function userLogIn($data) {
        $postdata = json_encode($data);
        $url = env('APP_URL') . 'oauth/token';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $postdata,
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        return $response;
    }

    public function userDetails() {
        $userData = getCurrentData();
        $currentUserId = isset($userData['id']) ? $userData['id'] : null;
        if (!empty($currentUserId)) {
            $query = $this->_user->select('id', 'name', 'email','user_phone')->where('id', $currentUserId)->first();
            return objToArray($query);
        } else {
            return false;
        }
    }

    public function updateUser($data) {
        $updatedData = array();
        $userData = getCurrentData();
        $currentUserId = isset($userData['id']) ? $userData['id'] : null;
        if (!empty($currentUserId)) {
            $updatedData['name'] = isset($data['name']) ? $data['name'] : '';
            $updatedData['email'] = isset($data['email']) ? $data['email'] : '';
            $updatedData['user_phone'] = isset($data['phone']) ? $data['phone'] : '';
            $this->_user->where('id', $currentUserId)->update($updatedData);
            return true;
        } else {
            return false;
        }
    }

    public function changePassword($data) {

        $message = "";
        $userData = getCurrentData();
        $currentUserId = isset($userData['id']) ? $userData['id'] : null;
        if (!empty($currentUserId)) {
            $this->_user->where('id', $currentUserId)->update(['password' => bcrypt($data['password'])]);
            return $message = "password changed successfully";
        } else {
            return $message = "some thing went wrong";
        }
    }

    public function objToArray($arr) {

        $arr = json_encode($arr);
        $res = json_decode($arr, true);
        return $res;
    }

    public function getUserById($id) {
        $userSessionData = array();
        $userData = $this->_user->find($id)->toArray();
        $roles = DB::table('role_user')->join('roles', 'roles.role_id', '=', 'role_user.role_id')->where('role_user.user_id', '=', $userData['user_id'])->get()->toArray();
        $roleResult = $this->objToArray($roles);
        $userSessionData = $userData;
        $userSessionData['roles'] = $roleResult;
        return $userSessionData;
    }

    public function registerEmail($str) {
        return $this->_user->where('login_key', '=', $str)->first()->toArray();
    }

    public function forgetPassword($email) {
        $randomString = str_random(25);
        $now = Carbon::now();
        $updateinfo = array(
            'forget_key' => $randomString,
            'forget_key_date' => $now
        );
        $userData = $this->_user->where('email', '=', $email)->first();
        if (!empty($userData)) {
            $userData->toArray();
            $this->_user->where('email', '=', $userData['email'])->update($updateinfo);
            $userUpdatedData = $this->_user->where('email', '=', $email)->first()->toArray();
            $subject = 'Forget Password Request';
            $templateSlug = 'forget-password';
            $toEmail = $email;
            $toCompletename = $userData["name"];
            $url = URL::to('/change-password/' . $randomString);
            $varsArray = array(
                array(
                    'name' => 'FULLNAME',
                    'content' => $userData["name"]),
                array(
                    'name' => 'FORGETLINK',
                    'content' => $url),
            );

            $results = $this->mandrill->sendMandrillEmail($subject, $templateSlug, $toEmail, $toCompletename, $varsArray);
            return true;
        } else {
            return false;
        }
    }

    public function validateEmail($str) {
        $result = $this->_user->where('login_key', '=', $str)->first();
        if (!empty($result)) {
            $result->toArray();
            $this->_user->where('user_id', '=', $result['user_id'])->update(['status' => "1"]);
            return true;
        } else {
            return false;
        }
    }

    public function getDataByforgetkey($forgetKey) {
        $result = $this->_user->where('forget_key', '=', $forgetKey)->where('forget_key_date', '>', Carbon::now()->subDays(1))->first();
        if (!empty($result)) {
            return $result->toArray();
        } else {
            return false;
        }
    }

    public function updatePassword($data) {
        $password = md5($data['password']);
        $userId = Crypt::decryptString($data['resetkey']);
        $updatedData = array(
            'password' => $password,
            'forget_key' => ''
        );
        $this->_user->where('user_id', '=', $userId)->update($updatedData);
        return true;
    }

    public function getStoreInfoByUserId($user_id) {
        $result = Store::where('user_id', '=', $user_id)->get()->toArray();
        if (count($result) > 0) {
            return $result;
        } else {
            return 0;
        }
    }

}
