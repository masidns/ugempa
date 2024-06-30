<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\GempaModel;
use CodeIgniter\HTTP\ResponseInterface;

class Dashboard extends BaseController
{
    public function index()
    {
        $gempaModel = new GempaModel(); // Inisialisasi model
        $data = $gempaModel->getDataGempa(); // Ambil data dari model

        // Format tanggal menjadi tahun-bulan
        $data = array_map(function ($item) {
            $item['year_month'] = date('Y-m', strtotime($item['tgl']));
            return $item;
        }, $data);

        // Log data untuk memastikan
        error_log(print_r($data, true));

        return view('dashboard', ['gempaData' => $data]); // Kirim data ke view
    }
}
