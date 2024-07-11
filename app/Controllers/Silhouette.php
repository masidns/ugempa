<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\GempaModel;

class Silhouette extends BaseController
{
    public function index()
    {
        $gempaModel = new GempaModel();
        $years = $gempaModel->select('YEAR(tgl) as year')->distinct()->findAll();

        return view('Silhouette_view', ['years' => $years]);
    }

    public function calculate()
    {
        ini_set('memory_limit', '512M'); // Atur batas memori menjadi 512MB
        ini_set('max_execution_time', 300); // Set batas waktu eksekusi menjadi 300 detik (5 menit)

        $year = $this->request->getPost('year');
        $cluster_by = $this->request->getPost('cluster_by');
        $max_clusters = $this->request->getPost('max_clusters');

        // Ambil data dari model
        $gempaModel = new GempaModel();
        if ($year === 'all') {
            $data = $gempaModel->findAll();
        } else {
            $data = $gempaModel->where('YEAR(tgl)', $year)->findAll();
        }

        // Simpan data ke file CSV sementara
        $csvPath = WRITEPATH . 'uploads/temp_gempa_data_Silhouette_Score.csv';
        $file = fopen($csvPath, 'w');
        fputcsv($file, ['depth', 'mag', 'remark']); // Header CSV
        foreach ($data as $row) {
            fputcsv($file, [$row['depth'], $row['mag'], $row['remark']]);
        }
        fclose($file);

        // Gunakan path lengkap ke Python executable
        $pythonPath = 'python';
        $command = escapeshellcmd("$pythonPath " . WRITEPATH . "python_scripts/silhouette_calculator.py $csvPath $cluster_by $max_clusters");
        log_message('debug', 'Command yang dijalankan: ' . $command);

        // Tambahkan `2>&1` untuk menangkap output error
        $output = shell_exec($command . ' 2>&1');
        log_message('debug', 'Output dan Error dari script Python: ' . $output);

        // Ambil hanya output JSON terakhir
        $json_output = substr($output, strrpos($output, '{'));
        log_message('debug', 'Output JSON terakhir: ' . $json_output);

        // Debugging: Periksa apakah output JSON valid
        if ($json_output === null || empty($json_output)) {
            log_message('error', 'Script Python tidak memberikan output atau output kosong.');
            $data['result'] = [];
        } else {
            // Parsing output dari script Python
            $decoded_output = json_decode($json_output, true);
            log_message('debug', 'Hasil decoding JSON: ' . print_r($decoded_output, true));
            if (json_last_error() === JSON_ERROR_NONE) {
                $data['result'] = $decoded_output;
            } else {
                log_message('error', 'Error parsing JSON: ' . json_last_error_msg());
                $data['result'] = [];
            }
        }

        // Debugging: Log hasil parsing
        log_message('debug', 'Hasil parsing JSON: ' . print_r($data['result'], true));

        // Pastikan $data['result'] tidak null
        if ($data['result'] === null) {
            $data['result'] = [];
        }

        return view('Silhouette_result', [
            'result' => $data['result'],
            'year' => $year,
            'cluster_by' => $cluster_by,
            'max_clusters' => $max_clusters
        ]);
    }
}