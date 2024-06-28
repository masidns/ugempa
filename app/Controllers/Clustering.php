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

        // Jalankan skrip Python untuk clustering
        $pythonExecutable = 'python'; // Gunakan 'python' jika python3 tidak dikenali
        $clusterScriptPath = WRITEPATH . 'python_scripts/cluster.py';
        $command = escapeshellcmd("{$pythonExecutable} \"{$clusterScriptPath}\" \"{$tempCsvPath}\" {$n_clusters} 2>&1");
        log_message('debug', 'Menjalankan command clustering: ' . $command);
        $output = shell_exec($command);
        log_message('debug', 'Output dari skrip Python clustering: ' . $output);

        // Periksa hasil clustering
        $clusters = json_decode($output, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            log_message('error', 'JSON decoding error: ' . json_last_error_msg());
            $clusters = [];
        }

        // Tambahkan hasil clustering ke data gempa
        foreach ($gempaData as $key => $gempa) {
            $gempaData[$key]['cluster'] = $clusters[$key]['cluster'] ?? null;
        }

        // Simpan data gempa yang sudah termasuk cluster ke file sementara
        $file = fopen($tempCsvPath, 'w');
        fputcsv($file, ['tgl', 'lat', 'lon', 'depth', 'mag', 'remark', 'cluster']);
        foreach ($gempaData as $gempa) {
            fputcsv($file, [$gempa['tgl'], $gempa['lat'], $gempa['lon'], $gempa['depth'], $gempa['mag'], $gempa['remark'], $gempa['cluster']]);
        }
        fclose($file);

        // Jalankan skrip Python untuk menghasilkan peta
        $mapScriptPath = WRITEPATH . 'python_scripts/generate_map.py';
        $outputMapPath = FCPATH . 'uploads/cluster_map.html'; // Simpan di direktori public
        $command = escapeshellcmd("{$pythonExecutable} \"{$mapScriptPath}\" \"{$tempCsvPath}\" \"{$outputMapPath}\" 2>&1");
        log_message('debug', 'Menjalankan command generate map: ' . $command);
        $output = shell_exec($command);
        log_message('debug', 'Output dari skrip Python generate map: ' . $output);

        // Pastikan peta dihasilkan dengan benar
        if (file_exists($outputMapPath)) {
            $data['map_path'] = base_url('uploads/cluster_map.html');
        } else {
            log_message('error', 'Peta tidak berhasil dihasilkan.');
            $data['map_path'] = null;
        }

        // Jalankan skrip Python untuk visualisasi cluster
        $visualizeScriptPath = WRITEPATH . 'python_scripts/visualize_clusters.py';
        $command = escapeshellcmd("{$pythonExecutable} \"{$visualizeScriptPath}\" \"{$tempCsvPath}\" 2>&1");
        log_message('debug', 'Menjalankan command visualize clusters: ' . $command);
        $output = shell_exec($command);
        log_message('debug', 'Output dari skrip Python visualize clusters: ' . $output);

        // Periksa hasil visualisasi
        if (!empty($output) && strpos($output, 'Error') === false) {
            $data['image_base64'] = trim($output);
        } else {
            log_message('error', 'Visualisasi cluster tidak berhasil.');
            $data['image_base64'] = null;
        }

        $data['clusters'] = $clusters;
        echo view('clustering_result', $data);
    }
}
