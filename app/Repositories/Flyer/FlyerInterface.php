<?php
/**
 * Created by PhpStorm.
 * User: junai
 * Date: 30/10/2018
 * Time: 01:09
 */

namespace App\Repositories\Flyer;


interface FlyerInterface
{
    public function saveFlyer($data);
    public function imagesUpload($image, $destinationPath);
    public function getFlyers();
}