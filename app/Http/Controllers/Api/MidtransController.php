<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Twilio\Rest\Client;

class MidtransController extends Controller
{
    // membuat function untuk menerima callback dari api midtrans
    public function callback(Request $request)
    {
        $serverKey = config('midtrans.serverKey'); // Ambil server key dari konfigurasi midtrans

        // Generate signature key dengan menggabungkan order_id, status_code, gross_amount, dan server key, lalu hash dengan algoritma sha512
        $hashedKey = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        // Membandingkan signature key yang diterima dari midtrans dengan signature key yang dihasilkan dari data yang diterima dan server key
        if ($hashedKey !== $request->signature_key) {
            // Jika signature key tidak valid, kembalikan response error dengan code 403 Forbidden
            return response()->json(['message' => 'Invalid signature key'], 403);
        }

        $transactionStatus = $request->transaction_status; // Ambil status transaksi dari request
        $orderId = $request->order_id; // Ambil order id dari request
        $transaction = Transaction::where('code', $orderId)->first(); // Cari transaksi di database berdasarkan order id

        // cek apakah ada transasksi yang ditemukan dengan order id yang diterima
        if (!$transaction) {
            // Jika transaksi tidak ditemukan, kembalikan response error dengan code 404 Not Found
            return response()->json(['message' => 'Transaction not found'], 404);
        }


        // Inisialisasi Twilio Client dengan SID dan Token yang didapat dari akun Twilio Anda
        $sid    = ""; //Set your Twilio SID here
        $token  = ""; //Set your Twilio Token here
        $twilio = new Client($sid, $token);

        $message =
            "Halo, " . $transaction->name . "!" . PHP_EOL . PHP_EOL .
            "Kami telah menerima pembayaran Anda dengan kode transaksi: " . $transaction->code . "." . PHP_EOL .
            "Total pembayaran: Rp " . number_format($transaction->total_amount, 0, ',', '.') . "." . PHP_EOL . PHP_EOL .
            "Anda bisa datang ke boarding house: " . $transaction->boarding_house->name . "." . PHP_EOL .
            "Alamat: " . $transaction->boarding_house->address . "." . PHP_EOL .
            "Mulai tanggal: " . date('d-m-Y', strtotime($transaction->start_date)) . "." . PHP_EOL .
            "Terima kasih atas kepercayaan Anda! 😊" . PHP_EOL .
            "Kami tunggu kedatangan Anda di boarding house kami! 🏠";


        // Switch case untuk menangani berbagai status transaksi yang diterima dari midtrans
        switch ($transactionStatus) {
            case 'capture':
                // Jika status transaksi adalah capture, periksa jenis pembayaran
                if ($request->payment_type == 'credit_card') {
                    // Jika jenis pembayaran adalah credit card, periksa status fraud
                    if ($request->fraud_status == 'challenge') {
                        // Jika status fraud adalah challenge, update status pembayaran transaksi menjadi pending
                        $transaction->update(['payment_status' => 'pending']);
                    } else {
                        // Jika status fraud bukan challenge, update status pembayaran transaksi menjadi success
                        $transaction->update(['payment_status' => 'success']);
                    }
                }
                break;

            case 'settlement':
                // Jika status transaksi adalah settlement, update status pembayaran transaksi menjadi success
                $transaction->update(['payment_status' => 'success']);

                // Kirim pesan WhatsApp ke pelanggan menggunakan Twilio API
                $twilio->messages
                    ->create(
                        "whatsapp:+" . $transaction->phone_number, // to
                        array(
                            "from" => "whatsapp:+14155238886",
                            "body" => $message
                        )
                    );

                break;

            case 'pending':
                // Jika status transaksi adalah pending, update status pembayaran transaksi menjadi pending
                $transaction->update(['payment_status' => 'pending']);
                break;

            case 'deny':
                // Jika status transaksi adalah deny, update status pembayaran transaksi menjadi failed
                $transaction->update(['payment_status' => 'failed']);
                break;

            case 'expired':
                // Jika status transaksi adalah expired, update status pembayaran transaksi menjadi expired
                $transaction->update(['payment_status' => 'expired']);
                break;

            case 'cancel':
                // Jika status transaksi adalah cancel, update status pembayaran transaksi menjadi terkensel
                $transaction->update(['payment_status' => 'canceled']);
                break;

            default:
                // Jika status transaksi tidak dikenal, update status pembayaran transaksi menjadi unknown
                $transaction->update(['payment_status' => 'unknown']);
                break;
        }

        // Kembalikan response sukses dengan code 200 OK
        return response()->json(['message' => 'Callback received successfully'], 200);
    }
}
