<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\UserMiddleware;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LikeController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\AlbumController;
use App\Http\Middleware\MemberMiddleware;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\DownloadController;
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
Route::post('/auth/member', [AuthController::class, 'addMember'])->name('addMember');

Route::get('/get-photo/{foto_id}', [PhotoGuestController::class, 'getPhotoDetail'])->name('getPhotoDetail');
Route::get('/get-all-photo', [PhotoGuestController::class, 'showAllPhoto'])->name('showAllPhoto');

Route::get('search-photo', [SearchController::class, 'searchPhoto'])->name('searchPhoto');

Route::middleware(UserMiddleware::class)->group(
    function(){

        // Subscribe - Ongoing - Hardest
        
        // Download Photo - ok
        Route::post('v1/download-photo/{foto_id}', [DownloadController::class, 'guestDownloadPhoto'])->name('guestDownloadPhoto');

        // Update User Profile - ok
        Route::get('v1/show-user-detail/{user_id}', [AuthController::class, 'showUserDetail'])->name('showUserDetail');
        Route::post('v1/update-user-detail/{user_id}', [AuthController::class, 'updateUserDetail'])->name('updateUserDetail');
        Route::post('v1/store-user-photo/{user_id}', [AuthController::class, 'storeUserPhoto'])->name('storeUserPhoto');

        // Like - ok
        Route::post('v1/store-guest-like', [LikeController::class, 'guestStoreLike'])->name('guestStoreLike');
        Route::delete('v1/delete-guest-like', [LikeController::class, 'guestDeleteLike'])->name('guestDeleteLike');

        // Comment - ok
        Route::get('v1/show-photo-comment', [CommentController::class, 'showComment'])->name('showComment');
        Route::post('v1/store-guest-comment', [CommentController::class, 'guestStoreComment'])->name('guestStoreComment');
        Route::delete('v1/delete-guest-comment', [CommentController::class, 'guestDeleteComment'])->name('guestDeleteComment');
        Route::post('v1/update-guest-comment', [CommentController::class, 'guestUpdateComment'])->name('guestUpdateComment');

        // Photos - Ongoing validation
        Route::post('v1/store-guest-photo', [PhotoGuestController::class, 'guestStorePhoto'])->name('guestStorePhoto');
        Route::post('v1/update-guest-photo', [PhotoGuestController::class, 'guestUpdatePhoto'])->name('guestUpdatePhoto');
        Route::delete('v1/delete-guest-photo', [PhotoGuestController::class, 'guestDeletePhoto'])->name('guestDeletePhoto');

    }
);

Route::middleware(MemberMiddleware::class)->group(
    function() {
        // Album - ok
        Route::get('v2/show-album', [AlbumController::class, 'showMemberAlbum'] )->name('showMemberAlbum');
        Route::get('v2/show-detail-album/{album_id}', [AlbumController::class, 'showDetailMemberAlbum'] )->name('showDetailMemberAlbum');
        Route::post('v2/store-album', [AlbumController::class, 'memberStoreAlbum'])->name('memberStoreAlbum');
        Route::post('v2/update-album', [AlbumController::class, 'memberUpdateAlbum'])->name('memberUpdateAlbum');
        Route::delete('v2/delete-album', [AlbumController::class, 'memberDeleteAlbum'])->name('memberDeleteAlbum');

        // Bookmark - ongoing
        Route::get('v2/show-bookmark/{bookmark_id}', [BookmarkController::class, 'showBookmark'])->name('showBookmark');
        Route::post('v2/store-bookmark', [BookmarkController::class, 'storeBookmark'])->name('storeBookmark');
        Route::delete('v2/delete-bookmark', [BookmarkController::class, 'deleteBookmark'])->name('deleteBookmark');
        
        // Photos (Unlimited) - Ongoing

    }
);

Route::middleware(AdminMiddleware::class)->group(
    function(){
        Route::get('/get-all-user', [AuthController::class, 'getAllUser'])->name('getAllUser');
        Route::get('/get-all-member', [AuthController::class, 'getAllMember'])->name('getAllMember');
    }
);
