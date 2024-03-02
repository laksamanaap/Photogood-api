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
            'user_id' => 'required|string',
            'profil_ruang' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()],422);
        }

        $uploadFolders = 'foto_profil_room';
        $image = $request->file('profil_ruang');
        $imagePath = $image->store($uploadFolders, 'public');

        $room = RuangDiskusi::create([
            'ruang_id' => Str::uuid()->toString(), 
            'nama_ruang' => $request->input('nama_ruang'),
            'deskripsi_ruang' => $request->input('deskripsi_ruang'),
            'user_id' => $request->input('user_id'),
            'profil_ruang' => "storage/" . $imagePath,
        ]);

        return response()->json($room,200);
    }

    public function updateRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ruang_id' => 'required|string', 
            'nama_ruang' => 'string',
            'deskripsi_ruang' => 'string',
            'profil_ruang' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()], 422);
        }

        $ruang_id = $request->input('ruang_id');

        $room = RuangDiskusi::where('ruang_id', $ruang_id)->first();

        $uploadFolders = 'foto_profil_room';
        $image = $request->file('profil_ruang');
        $imagePath = $image->store($uploadFolders, 'public');

        if (!$room) {
            return response()->json(['message' => 'Room not found!'], 404);
        }

        $room->nama_ruang = $request->filled('nama_ruang') ? $request->input('nama_ruang') : $room->nama_ruang;
        $room->deskripsi_ruang = $request->filled('deskripsi_ruang') ? $request->input('deskripsi_ruang') : $room->deskripsi_ruang;
        $room->profil_ruang = "storage/" . $imagePath;
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
        $rooms = RuangDiskusi::with(['lastMessage.user', 'owner'])->get();

        $appUrl = env('APP_URL');
        foreach ($rooms as $room ) {
            if (!empty($room->profil_ruang) && !Str::startsWith($room->profil_ruang, $appUrl)) {
                $room->profil_ruang = $appUrl . '/' . $room->profil_ruang;
            }
        }

        if (!$rooms) {
            return response()->json(['message' => 'No room found!'],404);
        }

        return response()->json($rooms,200);
    }


    public function showAllRoomMessages(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'ruang_id' => 'required|string', 
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([$validator->errors()], 422);
        // }

        $ruang_id = $request->input('ruang_id');

        if (!$ruang_id) {
            return response()->json(['message'=> 'Ruang id is required'],404);
        }

        $room = RuangDiskusi::with(['member.user', 'messages.user', 'owner'])->where('ruang_id', $ruang_id)->first();

        if (!$room) {
            return response()->json(['message' => 'Room not found!'],404);
        }

        $appUrl = env('APP_URL');
        if (!empty($room->profil_ruang) && !Str::startsWith($room->profil_ruang, $appUrl)) {
            $room->profil_ruang = $appUrl . '/' . $room->profil_ruang;
        }
        foreach ($room->messages as $message) {
            $messageUser = $message->user;
            if (!empty($messageUser->foto_profil) && !Str::startsWith($messageUser->foto_profil, $appUrl)) {
                $messageUser->foto_profil = $appUrl . '/' . $messageUser->foto_profil;
            }
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

    public function showMemberRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ruang_id' => 'required|string', 
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()], 422);
        }

        $ruang_id = $request->input('ruang_id');

        $room = RuangDiskusi::with('member.user')->where('ruang_id', $ruang_id)->first();

        if (!$room) {
            return response()->json(['message' => 'Room not found!'],404);
        }

        $appUrl = env('APP_URL');

        if (!empty($room->profil_ruang) && !Str::startsWith($room->profil_ruang, $appUrl)) {
            $room->profil_ruang = $appUrl . '/' . $room->profil_ruang;
        }

        foreach ($room->member as $member) {
            $memberUser = $member->user;
            if (!empty($memberUser->foto_profil) && !Str::startsWith($memberUser->foto_profil, $appUrl)) {
                $memberUser->foto_profil = $appUrl . '/' . $memberUser->foto_profil;
            }
        }

        return response()->json($room, 200);
    }

   


}
