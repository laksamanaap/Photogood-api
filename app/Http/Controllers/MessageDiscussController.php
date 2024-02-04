<?php

namespace App\Http\Controllers;

use App\Models\PesanDiskusi;
use Illuminate\Http\Request;
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
                return response()->json([$validator->errors()],422);
            }

            $messages = PesanDiskusi::create([
                'isi_pesan' => $request->input('isi_pesan'),
                'user_id' => $request->input('user_id'),
                'ruang_id' => $request->input('ruang_id'),
                'member_id' => $request->input('member_id'),
            ]);

            return response()->json($messages,200);
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

        $pesan_id = $request->input('pesan_id');

        $message = PesanDiskusi::find($pesan_id);

        if (!$message) {
            return response()->json(['message' => 'Messages not found!'], 404);
        }

        $message->isi_pesan = $request->input('isi_pesan');
        $message->ruang_id = $request->input('ruang_id');
        $message->user_id = $request->input('user_id');
        $message->member_id = $request->input('member_id');
        $message->save();

        return response()->json($message, 200);
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
