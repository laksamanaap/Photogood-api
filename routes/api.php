<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\UserMiddleware;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LikeController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlbumController;
use App\Http\Middleware\MemberMiddleware;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\PhotoGuestController;
use App\Http\Controllers\PhotoMemberController;
use App\Http\Controllers\RoomDiscussController;
use App\Http\Controllers\MessageDiscussController;

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
Route::get('/get-photo-image/{foto_id}', [PhotoGuestController::class, 'getPhotoImage'])->name('getPhotoImage');

Route::get('/get-all-image', [PhotoGuestController::class, 'showAllImage'])->name('showAllImage');
Route::get('/get-all-photo', [PhotoGuestController::class, 'showAllPhoto'])->name('showAllPhoto');
Route::get('/get-all-gif', [PhotoGuestController::class, 'showAllGIF'])->name('showAllGIF');
Route::get('/get-all-vector', [PhotoGuestController::class, 'showAllVector'])->name('showAllVector');

Route::get('search-photo', [SearchController::class, 'searchPhoto'])->name('searchPhoto');
Route::get('search-album', [SearchController::class, 'searchAlbum'])->name('searchAlbum');
Route::get('search-history', [SearchController::class, 'searchHistory'])->name('searchHistory');
Route::get('search-room-discuss', [AdminController::class, 'searchRoomDiscuss'])->name('searchRoomDiscuss');


Route::middleware(UserMiddleware::class)->group(
    function(){

        // Download Photo Routes
        // v1 - v2 : Website
        // v3 - v4 : Mobile
        // Download Photo - ok
        Route::post('v1/download-photo/{foto_id}', [DownloadController::class, 'guestDownloadPhoto'])->name('guestDownloadPhoto');

        // Subscribe - (Midtrans) - ok
        Route::post('v1/store-midtrans-payment', [PaymentController::class, 'createMidtransPayment'])->name('createMidtransPayment');
        Route::post('v1/store-qris-payment', [PaymentController::class, 'createQRISPayment'])->name('createQRISPayment');
        
        // Show payment - ok
        Route::get('v1/show-user-payment-list', [PaymentController::class, 'showUserPaymentList'])->name('showUserPaymentList');
        Route::get('v1/show-payment-detail', [PaymentController::class, 'showPaymentDetail'])->name('showPaymentDetail');

        // Update User Profile - ok
        Route::get('v1/show-user-detail', [AuthController::class, 'showUserDetail'])->name('showUserDetail');
        Route::post('v1/update-user-detail', [AuthController::class, 'updateUserDetail'])->name('updateUserDetail');
       
        // Update User Profile Photo - ok
        Route::post('v1/store-user-photo', [AuthController::class, 'storeUserPhoto'])->name('storeUserPhoto');
        Route::post('v1/update-user-photo', [AuthController::class, 'updateUserPhoto'])->name('updateUserPhoto');
        Route::post('v1/delete-user-photo', [AuthController::class, 'deleteUserPhoto'])->name('deleteUserPhoto');

        // Like - ok
        Route::get('v1/show-photo-like/{foto_id}', [LikeController::class, 'showPhotoLike'])->name('showPhotoLike');
        Route::post('v1/store-guest-like', [LikeController::class, 'guestStoreLike'])->name('guestStoreLike');
        Route::post('v1/delete-guest-like', [LikeController::class, 'guestDeleteLike'])->name('guestDeleteLike');

        // Comment - ok
        Route::get('v1/show-photo-comment/{foto_id}', [CommentController::class, 'showComment'])->name('showComment');
        Route::post('v1/store-guest-comment', [CommentController::class, 'guestStoreComment'])->name('guestStoreComment');
        Route::post('v1/delete-guest-comment', [CommentController::class, 'guestDeleteComment'])->name('guestDeleteComment');
        Route::post('v1/update-guest-comment', [CommentController::class, 'guestUpdateComment'])->name('guestUpdateComment');

        // Photos - ok (Encrypted but still bug)
        Route::post('v1/store-guest-photo', [PhotoGuestController::class, 'guestStorePhoto'])->name('guestStorePhoto');
        Route::post('v1/update-guest-photo', [PhotoGuestController::class, 'guestUpdatePhoto'])->name('guestUpdatePhoto');
        Route::delete('v1/delete-guest-photo', [PhotoGuestController::class, 'guestDeletePhoto'])->name('guestDeletePhoto');        

        // Get user post
        Route::get('v1/show-user-post', [PhotoGuestController::class, 'showUserPost'])->name('showUserPost');
        Route::get('v1/show-user-post/{user_id}', [PhotoGuestController::class, 'showUserPostByID'])->name('showUserPostByID');
        
        // Get user like
        Route::get('v1/show-user-like', [LikeController::class, 'showUserLike'])->name('showUserLike');

        // Get user download
        Route::get('v1/show-user-download', [DownloadController::class, 'showUserDownload'])->name('showUserDownload');

        // Discuss - ok
        Route::post('v1/create-room', [RoomDiscussController::class, 'createRoom'])->name('createRoom');
        Route::post('v1/update-room', [RoomDiscussController::class, 'updateRoom'])->name('updateRoom');
        Route::delete('v1/delete-room', [RoomDiscussController::class, 'deleteRoom'])->name('deleteRoom');
        Route::get('v1/show-all-room', [RoomDiscussController::class, 'showAllRoom'])->name('showAllRoom');
        Route::get('v1/show-room-messages', [RoomDiscussController::class, 'showAllRoomMessages'])->name('showAllRoomMessages');
        Route::post('v1/store-photo-room', [RoomDiscussController::class, 'storeRoomPhoto'])->name('storeRoomPhoto');
        

        // Discuss Messages - ok
        Route::post('v1/store-message', [MessageDiscussController::class, 'storeMessages'])->name('storeMessages');
        Route::post('v1/update-message', [MessageDiscussController::class, 'updateMessages'])->name('updateMessages');
        Route::delete('v1/delete-message', [MessageDiscussController::class, 'deleteMessages'])->name('deleteMessages');

        // Join Room - ok
        Route::post('v1/join-room', [RoomDiscussController::class, 'joinRoom'])->name('joinRoom');
        Route::post('v1/leave-room', [RoomDiscussController::class, 'leaveRoom'])->name('leaveRoom');
        Route::get('v1/show-room-member', [RoomDiscussController::class, 'showMemberRoom'])->name('showMemberRoom');
        
        // Mobile
        Route::post('v3/download-photo/{foto_id}', [DownloadController::class, 'mobileGuestDownloadPhoto'])->name('mobileGuestDownloadPhoto');

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

        // Bookmark - ok
        Route::get('v2/show-bookmark/{bookmark_id}', [BookmarkController::class, 'showBookmark'])->name('showBookmark');
        Route::get('v2/show-all-bookmark', [BookmarkController::class, 'showAllBookmark'])->name('showAllBookmark');
        Route::post('v2/store-bookmark', [BookmarkController::class, 'storeBookmark'])->name('storeBookmark');
        Route::post('v2/delete-bookmark', [BookmarkController::class, 'deleteBookmark'])->name('deleteBookmark');
        
        // Photos - ok
        Route::post('v2/store-member-photo', [PhotoMemberController::class, 'memberStorePhoto'])->name('memberStorePhoto');
        Route::post('v2/update-member-photo', [PhotoMemberController::class, 'memberUpdatePhoto'])->name('memberUpdatePhoto');
        Route::delete('v2/delete-member-photo', [PhotoMemberController::class, 'memberDeletePhoto'])->name('memberDeletePhoto');

        // Download Photo
        Route::post('v2/download-photo/{foto_id}', [DownloadController::class, 'memberDownloadPhoto'])->name('memberDownloadPhoto');

        // Mobile
        Route::post('v4/download-photo/{foto_id}', [DownloadController::class, 'mobileMemberDownloadPhoto'])->name('mobileMemberDownloadPhoto');


    }
);

Route::middleware(AdminMiddleware::class)->group(
    function(){  
        // Temporary
        // Ok
        Route::get('v3/show-all-image-pending', [AdminController::class, 'showAllImageNonActive'])->name('showAllImageNonActive');
        Route::get('v3/show-all-image-active', [AdminController::class, 'showAllImageActive'])->name('showAllImageActive');
        Route::post('v3/update-photo-active', [AdminController::class, 'changePhotoActive'])->name('changePhotoActive');
        Route::post('v3/update-photo-deactive', [AdminController::class, 'changePhotoDeactive'])->name('changePhotoDeactive');
        
        // Dashboard - (Add income detail)
        Route::get('v3/get-all-statistic', [AdminController::class, 'getAllStatistic'])->name('getAllStatistic');
        Route::get('v3/get-weekly-overview', [AdminController::class, 'getWeeklyOverview'])->name('getWeeklyOverview');
        Route::get('v3/get-current-user-registered', [AdminController::class, 'getCurrentUserRegistered'])->name('getCurrentUserRegistered');
        
        // Midtrans acc payment
        Route::get('v3/get-payment-history-pending', [AdminController::class, 'getPaymentHistoryPending'])->name('getPaymentHistoryPending');
        Route::get('v3/get-payment-history-success', [AdminController::class, 'getPaymentHistorySuccess'])->name('getPaymentHistorySuccess');
        Route::post('v3/accept-payment-history', [AdminController::class, 'acceptPaymentHistory'])->name('acceptPaymentHistory');
        
        // Pagination (Optional)
        
        // Manage Room Discuss
        Route::get('v3/show-all-room', [AdminController::class, 'showAllRoom'])->name('showAllRoom');
        Route::post('v3/delete-room', [AdminController::class, 'deleteRoom'])->name('deleteRoom');
        
        // Need Improve
        // Route::post('v3/create-room', [AdminController::class, 'createRoom'])->name('createRoom');
        // Route::post('v3/update-room', [AdminController::class, 'updateRoom'])->name('updateRoom');
        // Route::post('v3/store-photo-room', [RoomDiscussController::class, 'storeRoomPhoto'])->name('storeRoomPhoto');
        
        // Manage User
        Route::get('v3/get-all-user', [AdminController::class, 'getAllUser'])->name('getAllUser');
        Route::get('v3/show-user-detail/{user_id}', [AdminController::class, 'showUserDetail'])->name('showUserDetail');
        Route::post('v3/suspend-user/{user_id}', [AdminController::class, 'suspendUser'])->name('suspendUser');
        Route::post('v3/activate-user/{user_id}', [AdminController::class, 'activateUser'])->name('activateUser');
        
        // Manage Member
        Route::get('v3/get-all-member', [AdminController::class, 'getAllMember'])->name('getAllMember');
        Route::get('v3/show-member-detail/{user_id}', [AdminController::class, 'showMemberDetail'])->name('showMemberDetail');
        Route::post('v3/activate-member/{user_id}', [AdminController::class, 'activateMember'])->name('activateMember');
        
        // Search
        Route::get('v3/search-room-discuss', [AdminController::class, 'searchRoomDiscuss'])->name('searchRoomDiscuss');
        Route::get('v3/search-user', [AdminController::class, 'searchUser'])->name('searchUser');
        Route::get('v3/search-member', [AdminController::class, 'searchMember'])->name('searchMember');
    }
);

