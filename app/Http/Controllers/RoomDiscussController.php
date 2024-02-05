<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\RuangDiskusi;
use Illuminate\Http\Request;
use App\Models\AnggotaDiskusi;
use Illuminate\Support\Facades\Validator;

class RoomDiscussController extends Controller
{
    
    public function createRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_ruang' => 'required|string',
            'deskripsi_ruang' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()],422);
        }

        $room = RuangDiskusi::create([
            'ruang_id' => Str::uuid()->toString(), 
            'nama_ruang' => $request->input('nama_ruang'),
            'deskripsi_ruang' => $request->input('deskripsi_ruang'),
            'profil_ruang' => $request->input('profil_ruang'),
        ]);

        return response()->json($room,200);
    }

    public function updateRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ruang_id' => 'required|string', 
            'nama_ruang' => 'required|string',
            'deskripsi_ruang' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()], 422);
        }

        $ruang_id = $request->input('ruang_id');

        $room = RuangDiskusi::where('ruang_id', $ruang_id)->first();

        if (!$room) {
            return response()->json(['message' => 'Room not found!'], 404);
        }

        $room->nama_ruang = $request->input('nama_ruang');
        $room->deskripsi_ruang = $request->input('deskripsi_ruang');
        $room->profil_ruang = $request->input('profil_ruang');
        $room->save();

        return response()->json($room, 200);
    }

    public function deleteRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ruang_id' => 'required|string', 
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()], 422);
        }

        $ruang_id = $request->input('ruang_id');

        $room = RuangDiskusi::where('ruang_id', $ruang_id)->first();

        if (!$room) {
            return response()->json(['message' => 'Room not found!'], 404);
        }

        $room->delete();

        return response()->json(['message' => 'Room succesfully deleted!'], 200);
    }

    public function showAllRoom(Request $request) 
    {
        $room = RuangDiskusi::all();

        if (!$room) {
            return response()->json(['message' => 'No room found!'],404);
        }

        return response()->json($room,200);

    }


    public function showAllRoomMessages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ruang_id' => 'required|string', 
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()], 422);
        }

        $ruang_id = $request->input('ruang_id');

        $room = RuangDiskusi::with(['member.user','messages.user'])->where('ruang_id', $ruang_id)->first();

        if (!$room) {
            return response()->json(['message' => 'Room not found!'],404);
        }

        return response()->json($room, 200);
    }


    public function joinRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ruang_id' => 'required|string', 
            'user_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()], 422);
        }

        $ruang_id = $request->input('ruang_id');
        $user_id = $request->input('user_id');

        $room = RuangDiskusi::where('ruang_id', $ruang_id)->first();

        if (!$room) {
            return response()->json(['message' => 'Room not found!'], 404);
        }

        $existingMember = AnggotaDiskusi::where('ruang_id', $ruang_id)->where('user_id', $user_id)->first();

        if ($existingMember) {
            return response()->json(['message' => 'User already joined this room.'], 400);
        }

        $member = new AnggotaDiskusi();
        $member->ruang_id = $ruang_id;
        $member->user_id = $user_id;
        $member->save();

        return response()->json(['message' => 'User successfully joined the room.'], 200);
    }

   public function leaveRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ruang_id' => 'required|string', 
            'user_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()], 422);
        }

        $ruang_id = $request->input('ruang_id');
        $user_id = $request->input('user_id');

        $existingMember = AnggotaDiskusi::where('ruang_id', $ruang_id)->where('user_id', $user_id)->first();

        if (!$existingMember) {
            return response()->json(['message' => 'User is not a member of this room.'], 400);
        }

        $existingMember->delete();

        return response()->json(['message' => 'User successfully left the room.'], 200);
    }
}
