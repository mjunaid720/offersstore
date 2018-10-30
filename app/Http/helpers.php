<?php

use Illuminate\Support\Facades\Auth;

/*
 * Define helper functions for app
 */

function getAllCountries() {
    $query = DB::table('countries')
                    ->select('id', 'name')->get();
    if (!empty($query)) {
        $res = objToArray($query);
        return $res;
    } else {    
        return false;
    }
}

function getCountryById($countryId = '') {
    $query = DB::table('countries')
                    ->select('name')->where('id', $countryId)->first();
    if (!empty($query)) {
        $res = objToArray($query);
        return $res['name'];
    } else {
        return false;
    }
}

function getStatesById($stateid = '') {
    $query = DB::table('states')
                    ->where('id', $stateid)
                    ->select('name')->first();
    if (!empty($query)) {
        $res = objToArray($query);
        return $res['name'];
    } else {
        return false;
    }
}

function getStatesByCountryId($countryId = '') {
    $query = DB::table('states')
                    ->where('country_id', $countryId)
                    ->select('id', 'name')->get();
    if (!empty($query)) {
        $res = objToArray($query);
        return $res;
    } else {
        return false;
    }
}

function objToArray($arr) {

    $arr = json_encode($arr);
    $res = json_decode($arr, true);
    return $res;
}

function getCurrentData() {
    $data = Auth::user();
    $userData = objToArray($data);
    return ($userData);
}

function print_rr($arr) {
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
}
