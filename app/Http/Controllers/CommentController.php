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

        $isi_komentar = $request->input('isi_komentar');
        $isi_komentar_bersih = $this->badwordFilter($isi_komentar);

        $comment = Komentar::create([
            'foto_id' => $request->input('foto_id'),
            'user_id' => $request->input('user_id'),
            'member_id' => $request->input('member_id'),
            'isi_komentar' => $isi_komentar_bersih
        ]);

        return response()->json($comment,200);
    }

    // Filter Comment
   private function badwordFilter($komentar)
    {
   
        $badWord = [
        'anjingg',
        'babi',
        'setan',
        'jancok',
        'asu',
        'ngent',
        'ngentot',
        'ngentit',
        'anjir',
        'dancok',
        'kerek',
        'jancuk',
        'ndhasmu',
        'picek',
        'goblok',
        'gobloq',
        'goblog',
        'jangkrik',
        'damput',
        'jamput',
        'nggateli',
        'anying',
        'podol',
        'koplok',
        'kehed',
        'eusleum',
        'bagoy',
        'ontohod',
        'tai',
        'tailaso',
        'telaso',
        'telasota',
        'lacu',
        'bokep',
        'tolor',
        'sundal',
        'keparat',
        'kalera',
        'kampret',
        'poyok',
        'bangsat',
        'bastard',
        'fuck',
        'ashole',
        'boobs',
        'ass',
        'jurig',
        'pantek',
        'jiamput',
        'fucking',
        'damn',
        'bitch',
        'shit',
        'shibal',
        'wtf',
        'wth',
        'taek',
        'meki',
        'jiangkrek',
        'buangsat',
        'eek',
        'eeq',
        'eeg',
        'fucek',
        'bajingan',
        'anjing'
    ];        

        $commentWord = explode(' ', $komentar);
        
        // Filter Badword To Star
        $cleanWord = array_map(function ($word) use ($badWord) {
            if (in_array(strtolower($word), $badWord)) {
                $word = str_repeat('*', strlen($word));
            }
            return $word;
        }, $commentWord);

        // Filter Badword (Remove it)
        // $cleanWord = array_filter($commentWord, function ($word) use ($badWord) {
        //     return !in_array(strtolower($word), $badWord);
        // });

        $cleanComentar = implode(' ', $cleanWord);

        return $cleanComentar;
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
