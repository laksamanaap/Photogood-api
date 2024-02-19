<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AlbumController extends Controller
{
    
     public function memberStoreAlbum(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
            'member_id' => 'required|string',
            'nama_album' => 'required|string',
            'deskripsi_album' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()],422);
        }

        $comment = Album::create([
            'user_id' => $request->input('user_id'),
            'member_id' => $request->input('member_id'),
            'nama_album' => $request->input('nama_album'),
            'deskripsi_album' => $request->input('deskripsi_album'),
        ]);

        return response()->json($comment,200);
    }


    public function memberDeleteAlbum(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'album_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()],422);
        }

        $albumID = $request->input('album_id');

        $album = Album::destroy($albumID);

        if (!$album) {
            return response()->json(['message' => "Cannot find album id $albumID"]);
        }

        return response()->json(['message' => "Succesfully delete album id $albumID"]);
    }


    public function memberUpdateAlbum(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'album_id' => 'required|string',
            'user_id' => 'required|string',
            'member_id' => 'required|string',
            'nama_album' => 'required|string',
            'deskripsi_album' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()],422);
        }

        $albumID = $request->input('album_id');

        $album = Album::find($albumID);

        if (!$album) {
            return response()->json(['message' => "Cannot find album with id $albumID"], 404);
        }

        $album->user_id = $request->input('user_id');
        $album->member_id = $request->input('member_id');
        $album->nama_album = $request->input('nama_album');
        $album->deskripsi_album = $request->input('deskripsi_album');
        $album->save();

        return response()->json([
            'message' => "Successfully updated album with id $albumID",
            'data' => $album
        ]);

    }

    public function showMemberAlbum(Request $request) 
    {
        $token = $request->input('token');
        $user = User::where("login_tokens", $token)->first();
        $userID = $user->user_id;

        $albums = Album::where("user_id", $userID)
            ->with("bookmark_fotos")
            ->get();

        if ($albums->isEmpty()) {
            return response()->json(['message' => "Album not found!"], 404);
        }

        $response = [];
        foreach ($albums as $album) {
            $totalDatas = count($album->bookmark_fotos);
            $albumData = $album->toArray();
            $albumData['total_bookmark_data'] = $totalDatas;
            $response[] = $albumData;
        }

        return response()->json($response, 200);
    }

    public function showDetailMemberAlbum(Request $request, $albumID)
    {
        $token = $request->input('token');
        $user = User::where("login_tokens", $token)->first();
        $userID = $user->user_id;

        $album = Album::with('bookmark_fotos.foto')->find($albumID);

        if (!$album) {
            return response()->json(['message' => 'Album not found!']);
        }

        return response()->json($album,200);
    }


  

}
