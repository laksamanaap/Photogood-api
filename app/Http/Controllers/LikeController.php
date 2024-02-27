<?php

namespace App\Http\Controllers;

use App\Models\Foto;
use App\Models\Like;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LikeController extends Controller
{
    public function guestStoreLike(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
            'foto_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()],422);
        }

        $like = Like::create([
            'user_id' => $request->input('user_id'),
            'member_id' => $request->input('member_id'),
            'foto_id' => $request->input('foto_id')
        ]);

        return response()->json($like,200);
    }

    public function guestDeleteLike(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'like_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()],422);
        }

        $likeID = $request->input('like_id');

        $like = Like::destroy($likeID);

        if (!$like) {
            return response()->json(['message' => "Cannot find like id $likeID"]);
        }

        return response()->json(['message' => "Succesfully delete like id $likeID"]);
    }

    public function showPhotoLike(Request $request, $fotoID)
    {
        $foto = Foto::with('like')->find($fotoID);

        if (!$foto) {
            return response()->json(['message' => 'Photo not found']);
        }

        return response($foto,200);

    }

   public function showUserLike(Request $request)
    {
        $loginToken = $request->input('token');
        $user = User::where('login_tokens', $loginToken)->with('member')->first();
        $userID = $user->user_id;

        $like = Like::where('user_id', $userID)
            ->with('foto')
            ->get();

        if (!$like) {
            return response()->json(['message' => 'No like found!'], 404);
        }

        $appUrl = env('APP_URL');
          foreach ($like as $item) {
            $foto = $item->foto;
            if (!Str::startsWith($foto->lokasi_file, $appUrl)) {
                $foto->lokasi_file = $appUrl . '/' . $foto->lokasi_file;
            }
    }

        return response()->json($like, 200);
    }

}
