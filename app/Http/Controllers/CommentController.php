<?php

namespace App\Http\Controllers;

use App\Models\Foto;
use App\Models\Komentar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{

    public function __construct(Komentar $komentar)
    {
        $this->komentar = $komentar;
    }

    public function guestStoreComment(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'foto_id' => 'required|string',
            'user_id' => 'required|string',
            'isi_komentar' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()],422);
        }

        $comment = Komentar::create([
            'foto_id' => $request->input('foto_id'),
            'user_id' => $request->input('user_id'),
            'member_id' => $request->input('member_id'),
            'isi_komentar' => $request->input('isi_komentar')
        ]);

        return response()->json($comment,200);
    }

    public function guestDeleteComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'komentar_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()],422);
        }

        $likeID = $request->input('komentar_id');

        $like = Komentar::destroy($likeID);

        if (!$like) {
            return response()->json(['message' => "Cannot find like id $likeID"]);
        }

        return response()->json(['message' => "Succesfully delete like id $likeID"]);
    }


    public function guestUpdateComment(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'komentar_id' => 'required|string',
            'user_id' => 'required|string',
            'isi_komentar' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()],422);
        }

        $komentarId = $request->input('komentar_id');

        $comment = Komentar::find($komentarId);

        if (!$comment) {
            return response()->json(['message' => "Cannot find comment with id $komentarId"], 404);
        }

        $comment->user_id = $request->input('user_id');
        $comment->isi_komentar = $request->input('isi_komentar');
        $comment->member_id = $request->input('member_id');
        $comment->save();

        return response()->json([
            'message' => "Successfully updated comment with id $komentarId",
            'data' => $comment
        ]);

    }

    public function showComment(Request $request, $fotoID) 
    {
        $comment = Foto::with('comment.user')->find($fotoID);
        
        if (!$comment) {
            return response()->json(['message' => 'foto_id not found']);
        }

        return response()->json($comment, 200);

    }

}
