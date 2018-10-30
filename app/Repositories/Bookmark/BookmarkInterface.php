<?php
/**
 * Created by PhpStorm.
 * User: junai
 * Date: 30/10/2018
 * Time: 01:07
 */

namespace App\Repositories\Bookmark;


interface BookmarkInterface
{
    public function saveBookmark($data);
    public function userBookmarks();
    public function delBookmark($data);
}