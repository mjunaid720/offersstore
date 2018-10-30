<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Validator;
use Crypt;
use App\Repositories\User\Useraccess;

class RegisterController extends BaseController {

    private $userobj;
    private $request;

    public function __construct(Request $request) {

        $this->userobj = new Useraccess();
        $this->request = $request;
    }

//    public function login(Request $request) {
//        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
//            $user = Auth::user();
//            $request->request->add([
//                'scope' => 'place-orders'
//            ]);
//            $success['token'] = $user->createToken('LaraPassport')->accessToken;
//            return response()->json([
//                        'status' => 'success',
//                        'data' => $success
//            ]);
//        } else {
//            return response()->json([
//                        'status' => 'error',
//                        'data' => 'Unauthorized Access'
//            ]);
//        }
//    }

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required',
                    'c_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $input = $this->request->all();
        $input['password'] = bcrypt($input['password']);
        $input['register_source'] = 'website';
        $input['ip_address'] = $this->request->ip();
        $response = $this->userobj->userRegister($input);
        if ($response == true) {
            return $this->sendResponse('success', 'User register successfully.');
        } else {
            return $this->sendResponse('error', 'Some thing went wrong.');
        }
    }

    /**
     * details api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function getDetails() {
        $user = Auth::user();
        return response()->json(['success' => $user]);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request) {
        $request->user()->token()->revoke();
        return response()->json([
                    'message' => 'Successfully logged out'
        ]);
    }

    public function login() {
        $validator = Validator::make($this->request->all(), [
                    'email' => 'required|email',
                    'password' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $userRoles = $this->userobj->getUserRole($this->request->input('email'));
        $data = array();
        $input = $this->request->all();
        $data['client_id'] = 3;
        $data['client_secret'] = env('Client_Secret');
        $data['grant_type'] = 'password';
        $data['username'] = $input['email'];
        $data['email '] = $input['email'];
        $data['password'] = $input['password'];
        $data['scope'] = isset($userRoles) ? $userRoles : '';
        // $data['scope'] = 'user storeowner';
        $response = $this->userobj->userLogIn($data);
        if (!empty($response)) {
            $res = json_decode($response, true);
            return $this->sendResponse('response', $res);
        }
    }

    public function getUserDetail() {
        $response = $this->userobj->userDetails();
        if (!empty($response)) {
            return $this->sendResponse('success', $response);
        } else {
            return $this->sendError('error', "Some thing went wrong");
        }
    }

    public function updateUserDetail() {
        $validator = Validator::make($this->request->all(), [
                    'name' => 'required',
                    'email' => 'required|email',
                    'phone' => 'required',
                        //'password' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $input = $this->request->all();
        $response = $this->userobj->updateUser($input);
        if ($response == true) {
            return $this->sendResponse('success', 'User data updated successfully');
        } else {
            return $this->sendError('error', 'some thing went wrong');
        }
    }

    public function changeUserPassword() {
        $validator = Validator::make($this->request->all(), [
                   // 'currentpassword' => 'required',
                    'password' => 'required',
                    'c_password' => 'required|same:password',
                        //'password' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $input= $this->request->all();
        $response=$this->userobj->changePassword($input);
        if($response){
             return $this->sendResponse('success', $response);
        }else{
               return $this->sendError('error', 'some thing went wrong');
        }
    }

}
