<?php

namespace App\Http\Controllers;

use App\Models\Foto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PhotoMemberController extends Controller
{
     public function memberStorePhoto(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'judul_foto' => 'required|string',
            'deskripsi_foto' => 'required|string',
            'user_id' => 'required|string',
            'images.*' => 'required|image:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $uploadFolders = 'foto';

        $foto_id = $request->input('foto_id');
        $image = $request->file('images');
        $imagePath = $image->store($uploadFolders, 'public');

        $imageModel = Foto::create([
            'judul_foto' => $request->input("judul_foto"),
            'deskripsi_foto' => $request->input("deskripsi_foto"),
            'user_id' => $request->input("user_id"),
            'member_id' => $request->input("member_id"),
            'album_id' => $request->input("album_id"),
            'status' => 0, // Default, waiting for admin accepted
            'type_file' => $image->getClientMimeType(),
            'lokasi_file' => Storage::disk('public')->url($imagePath),
        ]);

        return response()->json([
            'message' => 'Image uploaded successfully',
            'data' => $imageModel
        ]);
    }

    public function memberUpdatePhoto(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'foto_id' => 'required|string',
            'images.*' => 'required|image:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $idGuestPhoto = $request->input('foto_id');

        $existingImage = Foto::findOrFail($idGuestPhoto);

        if (!$existingImage) {
            return response()->json(['error' => 'Image not found'], 404);
        }

        Storage::disk('public')->delete($existingImage->lokasi_file);

        $uploadFolders = 'campus';
        $newImage = $request->file('images');
        $newImagePath = $newImage->store($uploadFolders, 'public');

        $existingImage->update([
            'lokasi_file' => Storage::disk('public')->url($newImagePath),
        ]);

        $uploadImageResponse = [
            'foto_id' => $existingImage->foto_id,
            'image_name' => basename($newImagePath),
            'image_url' => Storage::disk('public')->url($newImagePath),
            'mime' => $newImage->getClientMimeType(),
        ];

        return response()->json([
            'message' => 'Image updated successfully',
            'data' => $uploadImageResponse
        ]);
    }

    public function memberDeletePhoto(Request $request)
    { 
            $validator = Validator::make($request->all(), [
            'foto_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $fotoID = $request->input('foto_id');

        if (!$fotoID) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        $existingImage = Foto::find($fotoID);

        if (!$existingImage) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        $imageDestroy = Foto::destroy($fotoID);

        return response()->json(['message' => "Successfully delete data foto_id $fotoID"],200);

    }
}
