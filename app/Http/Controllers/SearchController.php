<?php

namespace App\Http\Controllers;

use App\Models\Foto;
use App\Models\Like;
use App\Models\User;
use App\Models\Album;
use App\Models\Download;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            return response()->json(['message' => 'Query not found!'], 404);
        }
    }

    public function searchAlbum(Request $request) {
    $namaAlbum = $request->query('nama_album');

        if ($namaAlbum) {
            $albums = Album::with('bookmark_fotos.foto')->when($namaAlbum, function ($query) use ($namaAlbum) {
                return $query->where('nama_album', 'like', '%' . $namaAlbum . '%');
            })->get();

            if ($albums->isNotEmpty()) {
                $appUrl = env('APP_URL');
                foreach ($albums as $album) {
                    $totalBookmark = count($album->bookmark_fotos);
                    $album->total_bookmark_data = $totalBookmark;
                    foreach ($album->bookmark_fotos as $bookmark) {
                        $bookmark->foto->lokasi_file = $appUrl . '/' . $bookmark->foto->lokasi_file;
                    }
                }
                return response()->json($albums, 200);
            } else {
                return response()->json(['message' => 'Album not found'], 404);
            }
        } else {
            return response()->json(['message' => 'Query not found!'], 404);
        }
    }

    public function searchHistory(Request $request)
    {
        $loginToken = $request->input('token');

        $user = User::where('login_tokens', $loginToken)->with('member')->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $like = $request->query('like_foto');
        $download = $request->query('download_foto');

        $response = [];

        if ($like) {
            $likeHistory = Like::with('foto')->whereHas('foto', function ($query) use ($like) {
                $query->where('judul_foto', 'like', '%' . $like . '%');
            })->where('user_id', $user->user_id)->get();

            if ($likeHistory->isNotEmpty()) {
                $response['Like History'] = $likeHistory;

                $appUrl = env('APP_URL');
                foreach ($response['Like History'] as $likeItem) {
                    if (!empty($likeItem->foto->lokasi_file) && !Str::startsWith($likeItem->foto->lokasi_file, env('APP_URL'))) {
                        $likeItem->foto->lokasi_file = "{$appUrl}/{$likeItem->foto->lokasi_file}";
                    }
                }
            } else {
                $response['Like History'] = ['message' => 'Like history not found for current user'];
            }
        }

        if ($download) {
            $downloadHistory = Download::with('foto')->whereHas('foto', function ($query) use ($download) {
                $query->where('judul_foto', 'like', '%' . $download . '%');
            })->where('user_id', $user->user_id)->get();

            if ($downloadHistory->isNotEmpty()) {
                $response['Download History'] = $downloadHistory;

                $appUrl = env('APP_URL');
            
                foreach ($response['Download History'] as $downloadItem) {
                    if (!empty($downloadItem->foto->lokasi_file) && !Str::startsWith($downloadItem->foto->lokasi_file, env('APP_URL'))) {
                      $downloadItem->foto->lokasi_file = "{$appUrl}/{$downloadItem->foto->lokasi_file}";
                    }
                }
            } else {
                $response['Download History'] = ['message' => 'Download history not found for current user'];
            }
        }

        if (!empty($response)) {
            return response()->json($response, 200);
        }

        return response()->json(['message' => 'Query not found!'], 404);
    }


}
