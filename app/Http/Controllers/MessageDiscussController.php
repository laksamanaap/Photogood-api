<?php

namespace App\Http\Controllers;

use App\Models\PesanDiskusi;
use Illuminate\Http\Request;
use App\Models\AnggotaDiskusi;
use Illuminate\Support\Facades\Validator;

class MessageDiscussController extends Controller
{
    public function storeMessages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'isi_pesan' => 'required|string',
            'ruang_id' => 'required|string',
            'user_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()], 422);
        }

        // Find Anggota Diskusi
        $ruang_id = $request->input('ruang_id');
        $user_id = $request->input('user_id');

        $isMember = AnggotaDiskusi::where('ruang_id', $ruang_id)->where('user_id', $user_id)->exists();

         if (!$isMember) {
            return response()->json(['message' => 'You are not a member of this room. Please join the room before sending messages.'], 403);
        }

        $cleanMessage = $this->badwordFilter($request->input('isi_pesan'));

        $messages = PesanDiskusi::create([
            'isi_pesan' => $cleanMessage,
            'user_id' => $request->input('user_id'),
            'ruang_id' => $request->input('ruang_id'),
            'member_id' => $request->input('member_id'),
        ]);

        return response()->json($messages, 200);
    }


    public function updateMessages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pesan_id' => 'required|string', 
            'isi_pesan' => 'required|string',
            'ruang_id' => 'required|string',
            'user_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()], 422);
        }

        // Find Anggota Diskusi
        $ruang_id = $request->input('ruang_id');
        $user_id = $request->input('user_id');

        $isMember = AnggotaDiskusi::where('ruang_id', $ruang_id)->where('user_id', $user_id)->exists();

         if (!$isMember) {
            return response()->json(['message' => 'You are not a member of this room. Please join the room before sending messages.'], 403);
        }

        $message = PesanDiskusi::find($pesan_id);

        if (!$message) {
            return response()->json(['message' => 'Messages not found!'], 404);
        }

        $cleanMessage = $this->badwordFilter($request->input('isi_pesan'));

        $message->isi_pesan = $cleanMessage;
        $message->ruang_id = $request->input('ruang_id');
        $message->user_id = $request->input('user_id');
        $message->member_id = $request->input('member_id');
        $message->save();

        return response()->json($message, 200);
    }

    // Filter Comment
    private function badwordFilter($komentar)
    {
   
        $badWord = [
        'anjing ',
        'anjingg',
        'anjinggg',
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
        // $cleanWord = array_map(function ($word) use ($badWord) {
        //     if (in_array(strtolower($word), $badWord)) {
        //         $word = str_repeat('*', strlen($word));
        //     }
        //     return $word;
        // }, $commentWord);

        // Filter Badword (Remove it)
        $cleanWord = array_filter($commentWord, function ($word) use ($badWord) {
            return !in_array(strtolower($word), $badWord);
        });

        $cleanComentar = implode(' ', $cleanWord);

        return $cleanComentar;
    }



    public function deleteMessages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pesan_id' => 'required|string', 
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()], 422);
        }

        $pesan_id = $request->input('pesan_id');

        $message = PesanDiskusi::find($pesan_id);

        if (!$message) {
            return response()->json(['message' => 'Messages not found!'], 404);
        }

        $message->delete();

        return response()->json(['message' => 'Messages successfully deleted!'], 200);
    }


}
