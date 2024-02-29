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

        // User Status
        // 0 = Suspended
        // 1 = Common User
        // 2 = Membership
        // 3 = Admin

        $user = User::where('status', 1)->get();

        if (!$user) {
            return response()->json(['message' => 'No user found!']);
        }

        return response()->json($user,200);

    }

     public function getAllMember(Request $request)
    {

        $member = User::where('status', 2)->get();
        if (!$member) {
            return response()->json(['message' => 'No user found!']);
        }

        return response()->json($member,200);

    }

    // Photo Status
    // 0 : Deactive
    // 1 : Active
   public function changePhotoActive(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'foto_ids' => 'required|array', 
            'foto_ids.*' => 'required|integer|exists:foto,foto_id', 
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $fotoIDs = $request->input('foto_ids');

        foreach ($fotoIDs as $fotoID) {
            $foto = Foto::find($fotoID);
            if ($foto) {
                $foto->update(['status' => 1]);
            }
        }

        return response()->json(['message' => 'Foto status updated to active']);
    }

    public function changePhotoDeactive(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'foto_ids' => 'required|array', 
            'foto_ids.*' => 'required|integer|exists:foto,foto_id', 
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $fotoIDs = $request->input('foto_ids');

        foreach ($fotoIDs as $fotoID) {
            $foto = Foto::find($fotoID);
            if ($foto) {
                $foto->update(['status' => 0]);
            }
        }

        return response()->json(['message' => 'Foto status updated to deactive']);
    }

    // User
    public function showUserDetail(Request $request, $userID)
    {

        $user = User::where('user_id', $userID)->with('member')->first();

        if (!$user) {
            return response()->json(['message' => 'User not found!'], 404);
        }

        $appUrl = env('APP_URL');
        if ($user->foto_profil != null) {
            $user->foto_profil = "{$appUrl}/{$user->foto_profil}";
        }

        return response()->json($user, 200);
    }

    public function suspendUser(Request $request, $userID)
    {
        $user = User::where('user_id', $userID)->with('member')->first();

        if (!$user) {
            return response()->json(['message' => 'User not found!'], 404);
        }

        $user->update(['status' => 0]);

        return response()->json(['message' => "User id $userID status updated to deactive"]);
    }

    public function activateUser(Request $request, $userID)
    {
        $user = User::where('user_id', $userID)->with('member')->first();

        if (!$user) {
            return response()->json(['message' => 'User not found!'], 404);
        }

        $user->update(['status' => 1]);

        return response()->json(['message' => "User id $userID status updated to active"]);
    }

    // Member
    
    // Album
    




}
