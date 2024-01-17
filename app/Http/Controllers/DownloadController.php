<?php

namespace App\Http\Controllers;

use App\Models\Foto;
use App\Models\Download;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DownloadController extends Controller
{
    public function guestDownloadPhoto(Request $request, $fotoID)
    {
        $validator = Validator::make($request->all(), [
            'member_id' => 'required|string',
            'user_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors],422);
        }

        $download = Download::create([
            'foto_id' => $fotoID,
            'member_id' => $request->input('member_id'),
            'user_id' => $request->input('user_id')
        ]);
    
        return response()->json($download,200);

    }

}
