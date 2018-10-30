<?php
/**
 * Created by PhpStorm.
 * User: junai
 * Date: 30/10/2018
 * Time: 01:21
 */

namespace App\Repositories\User;


interface UserInterface
{
    public function userRegister($data);
    public function getUserRole($email = '');
    public function userLogIn($data);
    public function userDetails();
    public function updateUser($data);
    public function changePassword($data);
    public function objToArray($arr);
    public function getUserById($id);
    public function registerEmail($str);
    public function forgetPassword($email);
    public function validateEmail($str);
    public function getDataByforgetkey($forgetKey);
    public function updatePassword($data);
    public function getStoreInfoByUserId($user_id);
}