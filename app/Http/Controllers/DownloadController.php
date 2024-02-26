<?php

namespace App\Http\Controllers;

use App\Models\Foto;
use App\Models\Download;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DownloadController extends Controller
{

    public function guestDownloadPhoto(Request $request, $fotoID)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
        ]);

        $foto = Foto::find($fotoID);        

        if (!$foto) {
            return response()->json(['message' => "Cannot find data foto_id $fotoID"]);
        }

        $path =  $foto->lokasi_file;

        if (!file_exists($path)) {
            return response()->json(['message' => "Cannot find file"]);
        }

        $download = Download::create([
            'foto_id' => $fotoID,
            'member_id' => $request->input('member_id'),
            'user_id' => $request->input('user_id')
        ]);

        if (Download::where("user_id",$request->user_id)->count() > 2) {
            return response()->json(['message' => 'Untuk mendownload foto secara unlimited anda harus menjadi member terlebih dahulu!'],404);
        }
    
        $fileContent = file_get_contents($path);

        $mimeType = mime_content_type($path);

        $filename = basename($path);

        return response($fileContent)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        
    }

    public function memberDownloadPhoto(Request $request, $fotoID)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
            'member_id' => 'required|string'
        ]);

        $foto = Foto::find($fotoID);        

        if (!$foto) {
            return response()->json(['message' => "Cannot find data foto_id $fotoID"]);
        }

        $path =  $foto->lokasi_file;

        if (!file_exists($path)) {
            return response()->json(['message' => "Cannot find file"]);
        }

        $download = Download::create([
            'foto_id' => $fotoID,
            'member_id' => $request->input('member_id'),
            'user_id' => $request->input('user_id')
        ]);
    
        $fileContent = file_get_contents($path);

        $mimeType = mime_content_type($path);

        $filename = basename($path);

        return response($fileContent)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function mobileMemberDownloadPhoto(Request $request, $fotoID)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
            'member_id' => 'required|string'
        ]);

        $foto = Foto::find($fotoID);        

        if (!$foto) {
            return response()->json(['message' => "Cannot find data foto_id $fotoID"]);
        }

        $download = Download::create([
            'foto_id' => $fotoID,
            'member_id' => $request->input('member_id'),
            'user_id' => $request->input('user_id')
        ]);

        return response()->json($download,200);
    }

    public function mobileGuestDownloadPhoto(Request $request, $fotoID)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
        ]);

        $foto = Foto::find($fotoID);        

        if (!$foto) {
            return response()->json(['message' => "Cannot find data foto_id $fotoID"]);
        }

        $download = Download::create([
            'foto_id' => $fotoID,
            'member_id' => $request->input('member_id'),
            'user_id' => $request->input('user_id')
        ]);

        if (Download::where("user_id",$request->user_id)->count() > 2) {
            return response()->json(['message' => 'Untuk mendownload foto secara unlimited anda harus menjadi member terlebih dahulu!'],404);
        }

        return response()->json($download,200);
    }

}
