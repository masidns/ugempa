<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\GempaModel;

class Clustering extends Controller
{
    public function index()
    {
        echo view('clustering_view');
    }

    public function cluster()
    {
        $n_clusters = $this->request->getPost('n_clusters');
        $model = new GempaModel();
        $gempaData = $model->findAll();

        // Simpan data gempa ke file sementara
        $tempCsvPath = WRITEPATH . 'uploads/temp_gempa_data.csv';
        $file = fopen($tempCsvPath, 'w');
        // Pastikan header sesuai dengan kolom di database
        fputcsv($file, ['tgl', 'lat', 'lon', 'depth', 'mag', 'remark']);

        foreach ($gempaData as $gempa) {
            fputcsv($file, [$gempa['tgl'], $gempa['lat'], $gempa['lon'], $gempa['depth'], $gempa['mag'], $gempa['remark']]);
        }
        fclose($file);

        // Debugging: Baca isi file CSV dan log
        $csvContent = file_get_contents($tempCsvPath);
        log_message('debug', 'Isi file CSV: ' . $csvContent);

        // Jalankan skrip Python untuk clustering
        $pythonExecutable = 'python'; // Gunakan 'python' jika python3 tidak dikenali
        $scriptPath = WRITEPATH . 'python_scripts/cluster.py';
        $command = escapeshellcmd("{$pythonExecutable} \"{$scriptPath}\" \"{$tempCsvPath}\" {$n_clusters} 2>&1");
        log_message('debug', 'Menjalankan command: ' . $command);
        $output = shell_exec($command);

        // Debugging: Log output dari skrip Python
        log_message('debug', 'Output dari skrip Python: ' . $output);

        // Debugging: Periksa apakah output adalah JSON yang valid
        if (is_null($output) || empty($output)) {
            log_message('error', 'Output skrip Python kosong atau null.');
        }

        $clusters = json_decode($output, true);

        // Tambahkan penanganan error JSON decoding di sini
        if (json_last_error() !== JSON_ERROR_NONE) {
            log_message('error', 'JSON decoding error: ' . json_last_error_msg());
        }

        // Debugging: Log hasil decode JSON
        log_message('debug', 'Hasil decode JSON: ' . print_r($clusters, true));

        if (!is_array($clusters)) {
            $clusters = []; // Pastikan $clusters adalah array
        }

        $data['clusters'] = $clusters;
        echo view('clustering_result', $data);
    }
}