<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingShowRequest;
use App\Http\Requests\CustomerInformationStoreRequest;
use App\Interfaces\BoardingHouseRepositoryInterface;
use App\Interfaces\TransactionRepositoryInterface;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    // dependency injection untuk repository
    private BoardingHouseRepositoryInterface $boardingHouseRepository;
    private TransactionRepositoryInterface $transactionRepository;

    public function __construct(BoardingHouseRepositoryInterface $boardingHouseRepository, TransactionRepositoryInterface $transactionRepository)
    {
        $this->boardingHouseRepository = $boardingHouseRepository;
        $this->transactionRepository = $transactionRepository;
    }

    // save dulu room id ke session, untuk nanti ditampilkan di halaman informasi booking
    public function booking(Request $request, $slug)
    {
        $this->transactionRepository->saveTransactionDataToSession($request->all()); // Menyimpan data booking sementara ke sesi pengguna

        return redirect()->route('booking.information', ['slug' => $slug]);
    }

    // menampilkan informasi booking berdasarkan slug boarding house
    public function information($slug)
    {
        $transaction = $this->transactionRepository->getTransactionDataFromSession();
        $boardingHouse = $this->boardingHouseRepository->getBoardingHouseBySlug($slug);
        $room = $this->boardingHouseRepository->getBoardingHouseRoomById($transaction['room_id']);

        return view('pages.booking.information', compact('transaction', 'boardingHouse', 'room'));
    }

    public function saveInformation(CustomerInformationStoreRequest $request, $slug)
    {
        // datanya sudah tervalidasi dengan fungsi validated yang customer info store request
        $data = $request->validated();

        $this->transactionRepository->saveTransactionDataToSession($data); // Menyimpan data informasi booking ke sesi

        return redirect()->route('booking.checkout', $slug);
    }

    public function checkout($slug)
    {
        $transaction = $this->transactionRepository->getTransactionDataFromSession();
        $boardingHouse = $this->boardingHouseRepository->getBoardingHouseBySlug($slug);
        $room = $this->boardingHouseRepository->getBoardingHouseRoomById($transaction['room_id']);

        return view('pages.booking.checkout', compact('transaction', 'boardingHouse', 'room'));
    }

    public function payment(Request $request)
    {
        $this->transactionRepository->saveTransactionDataToSession($request->all()); // Menyimpan data metode pembayaran ke sesi

        $transaction = $this->transactionRepository->saveTransaction($this->transactionRepository->getTransactionDataFromSession()); // Menyimpan data transaksi ke database

        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = config('midtrans.serverKey');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = config('midtrans.isProduction');
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = config('midtrans.isSanitize');
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = config('midtrans.is3ds');

        // setting parameter untuk dikirim ke midtrans
        $params = array(
            'transaction_details' => [
                'order_id' => $transaction->code,
                'gross_amount' => $transaction->total_amount,
            ],
            'customer_details' => [
                'first_name' => $transaction->name,
                'email' => $transaction->email,
                'phone' => $transaction->phone_number,
            ],
        );

        // membuat transaksi ke midtrans dan mendapatkan url untuk redirect ke halaman pembayaran midtrans
        $paymentUrl = \Midtrans\Snap::createTransaction($params)->redirect_url;
        return redirect($paymentUrl);
    }

    public function success(Request $request)
    {
        $transaction = $this->transactionRepository->getTransactionByCode($request->order_id);

        // jika transaksi tidak ditemukan, redirect ke halaman utama
        if (!$transaction) {
            return redirect()->route('home');
        }

        // jika status pembayaran tidak sukses, redirect ke halaman utama
        // if ($transaction->payment_status != 'success') {
        //     return redirect()->route('home');
        // }

        return view('pages.booking.success', compact('transaction'));
    }


    // function untuk check booking
    public function check()
    {
        return view('pages.booking.check-booking');
    }

    public function show(BookingShowRequest $request)
    {

        $transaction = $this->transactionRepository->getTransactionByCodeEmailPhone($request->code, $request->email, $request->phone_number);

        // cek jika tidak ada transaksi yang ditemukan, maka kembalikan ke halaman cek booking dengan pesan error
        if (!$transaction) {
            return redirect()->back()->withErrors(['message' => 'Data booking tidak ditemukan. Silakan periksa kembali inputan Anda.']);
        }

        // jika transaksi ditemukan, tampilkan halaman detail booking dengan data transaksi
        return view('pages.booking.detail', compact('transaction'));
    }
}
