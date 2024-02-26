<?php

namespace App\Http\Controllers;

use App\Models\Foto;
use App\Models\User;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function getAllUser(Request $request)
    {

        $user = User::where('status', 2)->get();

        if (!$user) {
            return response()->json(['message' => 'No user found!']);
        }

        return response()->json($user,200);

    }

     public function getAllMember(Request $request)
    {

        $member = Member::with('user')->get();

        if (!$member) {
            return response()->json(['message' => 'No user found!']);
        }

        return response()->json($member,200);

    }


    public function changePhotoActive(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'foto_id'
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors]);
        }

        $fotoID = $request->input('foto_id');

        $foto = Foto::find($fotoID);

        if (!$foto) {
            return response()->json(['message' => 'Foto not found']);
        }

        $foto->update(['status' => 1]);

        return response()->json(['message' => "Foto id $fotoID status updated active"]);
    }

     public function changePhotoDeactive(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'foto_id'
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors]);
        }

        $fotoID = $request->input('foto_id');

        $foto = Foto::find($fotoID);

        if (!$foto) {
            return response()->json(['message' => 'Foto not found']);
        }

        $foto->update(['status' => 0]);

        return response()->json(['message' => "Foto id $fotoID status updated to deactive"]);
    }

}
