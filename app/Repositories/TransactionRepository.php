<?php

namespace App\Repositories;

use App\Interfaces\TransactionRepositoryInterface;
use App\Models\Room;
use App\Models\Transaction;
use Override;

class TransactionRepository implements TransactionRepositoryInterface
{

    // Method untuk mengambil data transaksi dari session
    public function getTransactionDataFromSession()
    {
        return session()->get("transaction");
    }

    // Method untuk menyimpan data transaksi ke session 
    public function saveTransactionDataToSession($data)
    {
        $transaction = session()->get("transaction", []);

        foreach ($data as $key => $value) {
            $transaction[$key] = $value;
        }

        session()->put("transaction", $transaction);
    }

    // Method untuk menyimpan data transaksi ke database
    public function saveTransaction($data)
    {
        // Ambil data kamar berdasarkan room_id simpan kedalam variabel $room
        $room = Room::find($data['room_id']);

        // Prapare data transaksi dengan memanggil method prapareTransactionData untuk mempersiapkan data sebelum disimpan dan simpan kedalam variabel $data
        $data = $this->prapareTransactionData($data, $room);

        $transaction = Transaction::create($data);

        session()->forget("transaction");

        return $transaction;
    }

    // Method untuk mengambil data transaksi berdasarkan kode transaksi
    public function getTransactionByCode($code)
    {
        return Transaction::where('code', $code)->first();
    }

    // Method untuk mencari data transaksi berdasarkan kode transaksi, email, dan nomor telepon pelanggan
    public function getTransactionByCodeEmailPhone($code, $email, $phone)
    {
        return Transaction::where('code', $code)
            ->where('email', $email)
            ->where('phone_number', $phone)
            ->first();
    }

    // method untuk mempersiapkan data transaksi sebelum disimpan ke database
    private function prapareTransactionData($data, $room)
    {

        // Generate kode transaksi dengan memanggil method generateTransactionCode dan simpan kedalam $data['code']
        $data['code'] = $this->generateTransactionCode();
        $data['payment_status'] = "pending"; // Set status pembayaran menjadi pending
        $data['transaction_date'] = now(); // Set tanggal transaksi menjadi sekarang

        // Hitung total pembayaran dengan memanggil method calculateTotalAmount dan simpan kedalam $total
        $total = $this->calculateTotalAmount($room->price_per_month, $data['duration']);
        // hitung jumlah pembayaran berdasarkan metode pembayaran dengan memanggil method calculatePaymentAmount dan simpan kedalam $data['total_amount']
        $data['total_amount'] = $this->calculatePaymentAmount($total, $data['payment_method']);

        return $data;
    }

    // method untuk membuat kode transaksi unik
    private function generateTransactionCode()
    {
        do {
            $code = 'BHTRX-' . strtoupper(uniqid());
        } while (Transaction::where('code', $code)->exists());

        return $code;
    }

    // method untuk menghitung total amout
    private function calculateTotalAmount($pricePerMonth, $duration)
    {
        $subtotal = $pricePerMonth * $duration;
        $tax = $subtotal * 0.1; // Asumsikan pajak 10%
        $insurance = $subtotal * 0.01; // Biaya asuransi tetap    

        return $subtotal + $tax + $insurance;
    }

    private function calculatePaymentAmount($total, $paymentMethod)
    {
        return $paymentMethod === "full_payment" ? $total : $total * 0.3; // Jika metode pembayaran full_payment, bayar total, jika down_payment, bayar 30% dari total
    }
}
