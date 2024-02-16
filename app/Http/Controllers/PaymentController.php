<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Member;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\RiwayatPembayaran;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    
    public function createMidtransPayment(Request $request)
    {
        $params = array(
            'transaction_details' => array(
                'order_id' => Str::uuid(),
                'gross_amount' => 30000,
            ),
            'item_details' => array(
                array(
                    'price' => 30000,
                    'quantity' => 1,
                    'name' => 'Membership'
                )
            ),
            'customer_details' => array(
                'user_id' => $request->user_id
            ),
            'enabled_payments' => array(
                'credit_card','bca_va','bni_va','bri_va'
            )
        );

        $auth = base64_encode(env('MIDTRANS_SERVER_KEY'));

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => "Basic $auth",
        ])->post('https://app.sandbox.midtrans.com/snap/v1/transactions', $params);

        $existingMember = Member::where('user_id', $params['customer_details']['user_id'])->first();

        $response = json_decode($response->body());
        $payment = new RiwayatPembayaran();
        $payment->riwayat_id = $params['transaction_details']['order_id'];
        $payment->user_id = $params['customer_details']['user_id'];
        $payment->status = 'pending';
        $payment->nominal_pembayaran = $params['item_details'][0]['price'];
        $payment->payment_gateway = 'midtrans';
        $payment->checkout_link = $response->redirect_url;
        $payment->save();

        if ($existingMember) {
            return response()->json(['message' => 'This user is already be photogood member!'], 401);
        } else {
            $member = new Member();
            $member->user_id = $params['customer_details']['user_id'];
            $member->save();

            // Status 2 (Member)
            $user = User::find($params['customer_details']['user_id']);
            if ($user) {
                $user->status = 2;
                $user->save();
            } else {
                return response()->json(['error' => 'User not found'], 404);
            }
        }

        return response()->json([
            'payment_info' => $response,
            'member_info' => $member,
            'payment' => $payment
        ],200);
    }

    // Update soon
    public function createQRISPayment(Request $request)
    {

    }

    // Get Payment Information
    public function showUserPaymentList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()], 422);
        }

        $token = $request->input('token');

        $user = User::where('login_tokens', $token)->first();
        $userID = $user->user_id;

        $riwayatPembayaran = RiwayatPembayaran::where('user_id',$userID)->get();

        if (!$riwayatPembayaran) {
            return response()->json(['message' => 'No Payment History Found!'], 404);
        }

        return response()->json($riwayatPembayaran,200);
    }

    public function showPaymentDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'riwayat_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()], 422);
        }

        $riwayat_id = $request->input('riwayat_id');

        $detailPembayaran = RiwayatPembayaran::where('riwayat_id', $riwayat_id)->first();

        if (!$detailPembayaran) {
            return response()->json(['message' => "There's no payment history detail"], 404);
        }

        return response()->json($detailPembayaran,200);
 
    }

}
