<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\UserMiddleware;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LikeController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\MemberMiddleware;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PhotoGuestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Auth
Route::post('/auth/login', [AuthController::class, 'loginUsers'])->name('loginUsers');
Route::post('/auth/register', [AuthController::class, 'registerUsers'])->name('registerUsers');
Route::get('/get-photo/{foto_id}', [PhotoGuestController::class, 'getPhotoDetail'])->name('getPhotoDetail');


Route::middleware(UserMiddleware::class)->group(
    function(){

        // Like
        Route::post('v1/store-guest-like', [LikeController::class, 'guestStoreLike'])->name('guestStoreLike');
        Route::delete('v1/delete-guest-like', [LikeController::class, 'guestDeleteLike'])->name('guestDeleteLike');

        // Comment
        Route::post('v1/store-guest-comment', [CommentController::class, 'guestStoreComment'])->name('guestStoreComment');
        Route::post('v1/update-guest-comment', [CommentController::class, 'guestUpdateComment'])->name('guestUpdateComment');
        Route::delete('v1/delete-guest-comment', [CommentController::class, 'guestDeleteComment'])->name('guestDeleteComment');

        // Search
        Route::get('search-photo', [SearchController::class, 'searchFoto'])->name('searchFoto');

        // Photos - ok
        Route::post('v1/store-guest-photo', [PhotoGuestController::class, 'guestStorePhoto'])->name('guestStorePhoto');
        Route::post('v1/update-guest-photo', [PhotoGuestController::class, 'guestUpdatePhoto'])->name('guestUpdatePhoto');
        Route::delete('v1/delete-guest-photo', [PhotoGuestController::class, 'guestDeletePhoto'])->name('guestDeletePhoto');


    }
);

Route::middleware(MemberMiddleware::class)->group(
    function(){
        // Album
        

    }
);

Route::middleware(AdminMiddleware::class)->group(
    function(){
        Route::get('/get-all-member', [AuthController::class, 'getAllMember'])->name('getAllMember');
        Route::get('/get-all-user', [AuthController::class, 'getAllUser'])->name('getAllUser');
    }
);
