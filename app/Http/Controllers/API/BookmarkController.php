<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use Crypt;
use App\Repositories\Bookmark\BookmarkInterface;

class BookmarkController extends BaseController {

    private $bookmarkRepo;
    private $request;

    public function __construct(Request $request, BookmarkInterface $bookmarkRepository) {

        $this->bookmarkRepo = $bookmarkRepository;
        $this->request = $request;
    }

    public function addBookmark() {
        $validator = Validator::make($this->request->all(), [
                    'offer_id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $input = $this->request->all();
        $response = $this->bookmarkRepo->saveBookmark($input);
        if (!empty($response)) {
            return $this->sendResponse('success', $response);
        } else {
            return $this->sendError('error', 'Some thing went wrong.');
        }
    }

    public function getUserBookmarks() {
        $response = $this->bookmarkRepo->userBookmarks();
        if (!empty($response)) {
            return $this->sendResponse('success', $response);
        } else {
            return $this->sendError('error', 'No record found.');
        }
    }

    public function deleteBookmark() {
        $validator = Validator::make($this->request->all(), [
                    'offer_id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $input = $this->request->all();
        $response = $this->bookmarkRepo->delBookmark($input);
        if ($response == true) {
            return $this->sendResponse('success', "offer un bookmark successfully");
        } else {
            return $this->sendResponse('error', 'Some thing went wrong.');
        }
    }

}
