<?php

namespace App\Http\Controllers;

use App\Models\Foto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class PhotoGuestController extends Controller
{
    
    public function guestStorePhoto(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'judul_foto' => 'required|string',
            'deskripsi_foto' => 'required|string',
            'user_id' => 'required|string',
            'kategori_id' => 'required|string',
            'type_foto' => 'required|string',
            'status' => 'required|string',
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
            return response()->json(['message' => 'User can only store 2 images only. Subscribe to unlimited photo store!'], 401);
        } 

        // $base64Image = base64_encode(file_get_contents(storage_path("app/public/{$imagePath}")));

        $imageModel = Foto::create([
            'judul_foto' => $request->input("judul_foto"),
            'deskripsi_foto' => $request->input("deskripsi_foto"),
            'user_id' => $request->input("user_id"),
            'member_id' => $request->input("member_id"),
            'kategori_id' => $request->input("kategori_id"),
            'status' => $request->input("status"), // Default 0, waiting for admin accepted
            'type_file' => $image->getClientMimeType(),
            'type_foto' => $request->input('type_foto'),
            'lokasi_file' => "storage/" . $imagePath, 
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
            'image_url' => url($newImagePath),
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
        $foto = Foto::with('user', 'member.user','comment.user','download','like', 'kategori')->find($fotoID);

        if (!$foto) {
            return response()->json(['message' => "Cannot find data foto_id $fotoID"]);
        }

        $appUrl = env('APP_URL');

        foreach ($foto->comment as $comment) {
            if ($comment->user && $comment->user->foto_profil) {
                $comment->user->foto_profil = "{$appUrl}/{$comment->user->foto_profil}";
            }
        }
        
        $foto->lokasi_file = "{$appUrl}/{$foto->lokasi_file}";

         if ($foto->user && $foto->user->foto_profil ) {
            $foto->user->foto_profil = "{$appUrl}/{$foto->user->foto_profil}";
        }   

        return response()->json($foto, 200);
    }

    // Temporary not used
    // public function getPhotoImage(Request $request, $fotoID)
    // {
    //    $foto = Foto::find($fotoID);

    //     if (!$foto) {
    //         return response()->json(['message' => "Cannot find data foto_id $fotoID"]);
    //     }

    //     $imageData = base64_decode($foto->lokasi_file);

    //     $randomFilename = uniqid('photogood_') . '.' . pathinfo($foto->type_file, PATHINFO_EXTENSION);

    //     $headers = [
    //         'Content-Type' => $foto->type_file,
    //         'Content-Disposition' => 'attachment; filename="' . $randomFilename . '"',
    //     ];

    //     return response($imageData, 200)->withHeaders($headers);
    // }


   public function showAllPhoto(Request $request)
    {
        $foto = Foto::where('status', 1)
        ->whereIn('type_foto', ['photo', 'PHOTO', 'Photo'])
        ->get();

        if (!$foto) {
            return response()->json(['message' => 'Not photo found!']);
        }

        $appUrl = env('APP_URL');

        foreach ($foto as $item) {
            $item->lokasi_file = "{$appUrl}/{$item->lokasi_file}";
        }

        return response()->json($foto, 200);
    }

    public function showAllGIF(Request $request)
    {
        $gif = Foto::where('status', 1)
                    ->whereIn('type_foto', ['gif', 'GIF', 'Gif'])
                    ->get();

        if ($gif->isEmpty()) {
            return response()->json(['message' => 'No GIF found!'], 404);
        }

        $appUrl = env('APP_URL');

        foreach ($gif as $item) {
            $item->lokasi_file = "{$appUrl}/{$item->lokasi_file}";
        }

        return response()->json($gif, 200);
    }

    public function showAllVector(Request $request)
    {
        $vector = Foto::where('status', 1)
        ->whereIn('type_foto', ['vector', 'VECTOR', 'Vector'])
        ->get();

        if ($vector->isEmpty()) {
            return response()->json(['message' => 'No vector found!'], 404);
        }

        $appUrl = env('APP_URL');

        foreach ($vector as $item) {
            $item->lokasi_file = "{$appUrl}/{$item->lokasi_file}";
        }

        return response()->json($vector, 200);
    }



}
