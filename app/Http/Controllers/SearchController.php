<?php

namespace App\Http\Controllers;

use App\Models\Foto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SearchController extends Controller
{
  public function searchPhoto(Request $request)
{
    $judulFoto = $request->query('judul_foto');
    $deskripsiFoto = $request->query('deskripsi_foto');

    if ($judulFoto || $deskripsiFoto) {
        $photos = Foto::when($judulFoto, function ($query) use ($judulFoto) {
                    return $query->where('judul_foto', 'like', '%' . $judulFoto . '%');
                })
                ->when($deskripsiFoto, function ($query) use ($deskripsiFoto) {
                    return $query->orWhere('deskripsi_foto', 'like', '%' . $deskripsiFoto . '%');
                })
                ->get();
        if ($photos->isNotEmpty()) {
            $appUrl = env('APP_URL');
            foreach ($photos as $photo) {
                $photo->lokasi_file = "{$appUrl}/{$photo->lokasi_file}";
            }
            return response()->json($photos, 200);
        } else {
            return response()->json(['message' => 'Photo not found'], 404);
        }
    } else {
        return response()->json(['message' => 'Query not found!'], 400);
    }
}
}
