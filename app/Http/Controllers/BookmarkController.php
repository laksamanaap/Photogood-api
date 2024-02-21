<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookmarkController extends Controller
{
    public function storeBookmark(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'foto_id' => 'required|string',
            'album_id' => 'required|string',
            'user_id' => 'required|string',
            // 'member_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()], 422);
        }

        $token = $request->input('token');
        $user = User::where('login_tokens', $token)->first();
        $userID = $user->user_id;

        $bookmarkHistory = Bookmark::where('foto_id', $request->input("foto_id"))
                            ->where('album_id', $request->input("album_id"))
                            ->count();

        if ($bookmarkHistory >= 1) {
            return response()->json(['message' => 'This photo has been on your bookmark!'], 404);
        }
        
        $bookmark = Bookmark::create([
            'foto_id' => $request->input('foto_id'),
            'album_id' => $request->input('album_id'),
            'user_id' => $request->input('user_id'),
            // 'member_id' => $request->input('member_id')  
        ]);

        return response()->json($bookmark,200);
    }

    public function showBookmark(Request $request, $bookmarkID)
    {

        $user = User::where('login_tokens', $request->input('token'))->first();
        $userID = $user->user_id;
        $bookmark = Bookmark::where('user_id', $userID)
                            ->where('bookmark_id', $bookmarkID)
                            ->with('foto') 
                            ->first();

        
        return response()->json($bookmark);
    }

    public function deleteBookmark(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'bookmark_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()]);
        }

        $bookmarkID = $request->input('bookmark_id');
        $bookmark = Bookmark::destroy($bookmarkID);

        return response()->json(['message' => 'Bookmark was deleted succesfully']);
    }

  public function showAllBookmark(Request $request)
    {
        $loginToken = $request->input('token');
        $user = User::where('login_tokens', $loginToken)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $userID = $user->user_id;

        $bookmarks = Bookmark::with('foto')
                            ->where('user_id', $userID)
                            ->get();

        if ($bookmarks->isEmpty()) {
            return response()->json(['message' => "The user hasn't saved anything yet!"], 404);
        }

        $appUrl = env('APP_URL');
        foreach ($bookmarks as $bookmark) {
            $bookmark->foto->lokasi_file = "{$appUrl}/{$bookmark->foto->lokasi_file}";
        }

        return response()->json($bookmarks, 200);
    }

}
