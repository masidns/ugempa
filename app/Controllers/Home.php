<?php

namespace App\Controllers;

use App\Models\GempaModel; // Tambahkan model

class Home extends BaseController
{
    public function index(): string
    {
        $gempaModel = new GempaModel(); // Inisialisasi model
        $data = $gempaModel->getDataGempa(); // Ambil data dari model

        // Format tanggal menjadi tahun-bulan
        $data = array_map(function($item) {
            $item['year_month'] = date('Y-m', strtotime($item['tgl']));
            return $item;
        }, $data);

        // Log data untuk memastikan
        error_log(print_r($data, true));

        return view('dashboard', ['gempaData' => $data]); // Kirim data ke view
    }
}