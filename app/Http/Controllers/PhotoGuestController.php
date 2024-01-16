<?php

namespace App\Http\Controllers;

use App\Models\Foto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PhotoGuestController extends Controller
{
    
    public function guestStorePhoto(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'judul_foto' => 'required|string',
            'deskripsi_foto' => 'required|string',
            'user_id' => 'required|string',
            'album_id' => 'required|string',
            'images.*' => 'required|image:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $uploadFolders = 'foto';

        $foto_id = $request->input('foto_id');
        $image = $request->file('images');
        $imagePath = $image->store($uploadFolders, 'public');

        $userImageCount = Foto::where('user_id', $request->input('user_id'))->count();

        if ($userImageCount >= 2) {
            return response()->json(['message' => 'User can only store 2 images only. Subscribe to unlimited photo store!'], 404);
        } 

        $imageModel = Foto::create([
            'judul_foto' => $request->input("judul_foto"),
            'deskripsi_foto' => $request->input("deskripsi_foto"),
            'user_id' => $request->input("user_id"),
            'member_id' => $request->input("member_id"),
            'album_id' => $request->input("album_id"),
            'type_file' => $image->getClientMimeType(),
            'lokasi_file' => Storage::disk('public')->url($imagePath),
        ]);

        return response()->json([
            'message' => 'Image uploaded successfully',
            'data' => $imageModel
        ]);
    }

    public function guestUpdatePhoto(Request $request)
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

        // Delete the existing image file
        Storage::disk('public')->delete($existingImage->lokasi_file);

        // Upload the new image file
        $uploadFolders = 'campus';
        $newImage = $request->file('images');
        $newImagePath = $newImage->store($uploadFolders, 'public');

        // Update the image data in the database
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

    public function guestDeletePhoto(Request $request)
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


    public function getPhotoDetail(Request $request, $fotoID)
    {

        $foto = Foto::with('album','user', 'member.user','comment.user')->find($fotoID);

        if (!$foto) {
            return response()->json(['message' => "Cannot find data foto_id $fotoID"]);
        }

        return response()->json($foto,200);

    }


    public function showAllPhoto(Request $request)
    {
        $foto = Foto::all();

        if (!$foto) {
            return response()->json(['message' => 'Not photo found!']);
        }

        return response()->json($foto, 200);
    }


}
