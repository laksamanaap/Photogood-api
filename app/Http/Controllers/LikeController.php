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
            'foto_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()], 422);
        }

        $fotoID = $request->input('foto_id');

        $foto = Foto::with('like')->find($fotoID);

        if (!$foto) {
            return response()->json(['message' => 'Photo not found'], 404);
        }

        $latestLike = $foto->like->last();

        if (!$latestLike) {
            return response()->json(['message' => 'No likes found for this photo'], 404);
        }

        try {
            $latestLike->delete();
            return response()->json(['message' => "Successfully deleted the last like for photo with id $fotoID"]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete like'], 500);
        }
    }

    public function showPhotoLike(Request $request, $fotoID)
    {
        $foto = Foto::with('like')->find($fotoID);

        if (!$foto) {
            return response()->json(['message' => 'Photo not found']);
        }

        return response()->json([
            'likes_count' => $foto->like->count()
        ], 200);
    }


   public function showUserLike(Request $request)
    {
        $loginToken = $request->input('token');
        $user = User::where('login_tokens', $loginToken)->with('member')->first();
        $userID = $user->user_id;

        $like = Like::where('user_id', $userID)
            ->with('foto')
            ->whereHas('foto', function ($query) {
                            $query->where('status', 1);
                        })
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
