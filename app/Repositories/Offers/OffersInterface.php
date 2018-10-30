<?php
/**
 * Created by PhpStorm.
 * User: junai
 * Date: 30/10/2018
 * Time: 01:11
 */

namespace App\Repositories\Offers;


interface OffersInterface
{
    public function saveOffer($data = array());
    public function offerById($offerId);
    public function getCategoriesbyOfferCount();
    public function updateOffer($postedData = array());
    public function imagesUpload($image, $destinationPath);
    public function saveOfferImages($offerId, $imageName);
    public function multiPleImagesUpload($images, $destinationPath);
    public function getCategories($parentId = '');
    public function childCategories($parentId='');
    public function homeCategories($parentId = '');
    public function allOffers();
    public function allTrendingOffers();
    public function storeOffers($storeId);
    public function productsByCatId($catId);
    public function countTotaloffers($catId);
    public function deleteOffers($offerId);
    public function offerDetail($offerId);
}