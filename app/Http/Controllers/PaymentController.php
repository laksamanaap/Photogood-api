<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\RiwayatPembayaran;
use Illuminate\Support\Facades\Http;

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

            $response = json_decode($response->body());

            $payment = new RiwayatPembayaran();
            $payment->riwayat_id = $params['transaction_details']['order_id'];
            $payment->status = 'pending';
            $payment->user_id = $params['customer_details']['user_id'];
            $payment->checkout_link = $response->redirect_url;
            $payment->nominal_pembayaran = $params['item_details'][0]['price'];
            $payment->save();

            return response()->json([
                'payment_info' => $response,
                'payment' => $payment
            ],200);
    }

}
