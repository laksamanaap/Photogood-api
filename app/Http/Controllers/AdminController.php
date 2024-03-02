<?php

namespace App\Http\Controllers;

use App\Models\Foto;
use App\Models\User;
use App\Models\Member;
use Illuminate\Support\Str;
use App\Models\RuangDiskusi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\RiwayatPembayaran;
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
    public function getAllMember(Request $request)
    {

        $member = User::where('status', 2)->get();
        if (!$member) {
            return response()->json(['message' => 'No user found!']);
        }

        return response()->json($member,200);
    }

    public function showMemberDetail(Request $request, $userID)
    {

        $user = User::where('user_id', $userID)
        ->where('status',2)
        ->with('member')
        ->first();

        if (!$user) {
            return response()->json(['message' => 'Member not found!'], 404);
        }

        $appUrl = env('APP_URL');
        if ($user->foto_profil != null) {
            $user->foto_profil = "{$appUrl}/{$user->foto_profil}";
        }

        return response()->json($user, 200);
    }

    public function activateMember(Request $request, $userID)
    {
        $user = User::where('user_id', $userID)->with('member')->first();

        if (!$user) {
            return response()->json(['message' => 'User not found!'], 404);
        }

        $user->update(['status' => 2]);

        return response()->json(['message' => "User (Member) id $userID status updated to active "]);
    }

    // Room Discuss
    public function showAllRoom(Request $request) 
    {
        $rooms = RuangDiskusi::with(['owner'])->get();

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

    // Search
    public function searchUser(Request $request)
    {
        $username = $request->query('username');
        $nama_lengkap = $request->query('nama_lengkap');

        $users = User::query()->where('status', 1);

        if ($username || $nama_lengkap) {
            $users->where(function ($query) use ($username, $nama_lengkap) {
                $query->where('username', 'LIKE', '%' . $username . '%')
                    ->orWhere('nama_lengkap', 'LIKE', '%' . $nama_lengkap . '%');
            });
        }

        $result = $users->get();
        
        if ($result->isNotEmpty()) {
            return response()->json($result, 200);
        } else {
            return response()->json(['message' => 'User not found'], 404);
        }
    }

    public function searchMember(Request $request)
    {
        $username = $request->query('username');
        $nama_lengkap = $request->query('nama_lengkap');

        $members = User::query()->where('status', 2);

        if ($username || $nama_lengkap) {
            $members->where(function ($query) use ($username, $nama_lengkap) {
                $query->where('username', 'LIKE', '%' . $username . '%')
                    ->orWhere('nama_lengkap', 'LIKE', '%' . $nama_lengkap . '%');
            });
        }

        $result = $members->get();

        if ($result->isNotEmpty()) {
            return response()->json($result, 200);
        } else {
            return response()->json(['message' => 'Member not found'], 404);
        }

    }

    public function searchRoomDiscuss(Request $request)
    {
        $nama_ruang = $request->query('nama_ruang');
        $deskripsi_ruang = $request->query('deskripsi_ruang');

        $rooms = RuangDiskusi::query();

        if ($nama_ruang) {
            $rooms->where(function ($query) use ($nama_ruang) {
                $query->where('nama_ruang', 'LIKE', '%' . $nama_ruang . '%');
            });
        }

        $result = $rooms->with(['lastMessage.user', 'owner'])->get();

        $appUrl = env('APP_URL');
        foreach ($result as $room) {
            if (!Str::startsWith($room->profil_ruang, $appUrl)) {
                $room->profil_ruang = $appUrl . '/' . $room->profil_ruang;
            }
        }

        if ($result->isNotEmpty()) {
            return response()->json($result, 200);
        } else {
            return response()->json(['message' => 'Room not found'], 404);
        }
    }

  // Dashboard
  public function getAllStatistic(Request $request)
    {
        $totalUsers = User::where('status', 1)->orWhere('status', 2)->count();
        $totalMembers = User::where('status', 2)->count();
        $jumlahPembayaran = RiwayatPembayaran::all()->count();
        $totalPembayaran = $jumlahPembayaran * 30000;

        if ($totalUsers > 0) {
            $memberPercent = ($totalMembers / $totalUsers) * 100;
        } else {
            $memberPercent = 0;
        }

        return response()->json([
            'member_percent' => round($memberPercent),
            'total_pembayaran' => $totalPembayaran,
            'user_total_data' => $totalUsers,
            'member_total_data' => $totalMembers,
            'photo_total_data' => Foto::where('status', 1)->count()
        ]);
    }

    // Get Weekly Overview
    public function getWeeklyOverview(Request $request)
    {
        $weekStart = Carbon::now()->startOfWeek(); 
        $weekEnd = Carbon::now()->endOfWeek(); 

        $weeklyPayments = RiwayatPembayaran::whereBetween('created_at', [$weekStart, $weekEnd])->get();
        $totalWeeklyPayments = $weeklyPayments->count() * 30000;
        $totalWeeklyPercents = (RiwayatPembayaran::all()->count() / 7) * 100;

        $dailyPayments = [];
        $currentDay = clone $weekStart;

        while ($currentDay <= $weekEnd) {
            $dailyPayments[$currentDay->format('l')] = 0;
            $currentDay->addDay(); 
        }

        foreach ($weeklyPayments as $payment) {
            $day = Carbon::parse($payment->created_at)->format('l');
            $dailyPayments[$day] += 30000; 
        }

        return response()->json([
            'weekly_payments' => $dailyPayments,
            'total_weekly_payments' => $totalWeeklyPayments,
            'total_weekly_percents' => round($totalWeeklyPercents)
        ]);
    }

   public function getCurrentUserRegistered(Request $request)
    {
        $currentUsers = User::latest()->take(8)->get();

        $appUrl = env('APP_URL');
        foreach ($currentUsers as $currentUser) {
            if (!empty($currentUser->foto_profil && !Str::startsWith($currentUser->foto_profil, env('APP_URL')))) {
                $currentUser->foto_profil = $appUrl . '/' . $currentUser->foto_profil;
            }
        }

        return response()->json($currentUsers);
    }

    // Midtrans acc payment

    public function getAllPaymentHistory(Request $request)
    {
        $payments = RiwayatPembayaran::all();

        if (!$payments) {
            return response()->json(['message' => 'No payment history found!'], 404);
        }

        return response()->json($payments, 200);
    }

    public function getPaymentHistoryPending(Request $request)
    {
        $payments = RiwayatPembayaran::where('status', 'pending')
        ->with('user')
        ->get();

        if (!$payments) {
            return response()->json(['message' => 'No payment history status pending found!'], 404);
        }

        return response()->json($payments, 200);
    }

    
    public function getPaymentHistorySuccess(Request $request)
    {
        $payments = RiwayatPembayaran::where('status', 'success')
        ->with('user')
        ->get();

        if (!$payments) {
            return response()->json(['message' => 'No payment history status success found!'], 404);
        }

        return response()->json($payments, 200);
    }

    public function acceptPaymentHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'riwayat_id' => 'required|string',
            'user_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()], 422);
        }

        $payment = RiwayatPembayaran::where('riwayat_id', $request->riwayat_id)
                                    ->where('user_id', $request->user_id)
                                    ->where('status', 'pending')
                                    ->first();

        if (!$payment) {
            return response()->json(['error' => 'Payment not found or already processed'], 404);
        }

        $payment->status = 'success';
        $payment->save();

        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->status = 2;
        $user->save();

        return response()->json(['message' => 'Payment accepted successfully and user status updated to member'], 200);
    }

    // Pagination

}
