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
        log_message('debug', 'Jumlah cluster yang diterima dari form: ' . $n_clusters);

        if (empty($n_clusters)) {
            log_message('error', 'Jumlah cluster tidak ditemukan di form.');
            return;
        }

        $model = new GempaModel();
        $gempaData = $model->findAll();

        // Filter data untuk hanya menyertakan baris yang mengandung kata "Indonesia"
        $filteredData = array_filter($gempaData, function ($item) {
            return stripos($item['remark'], 'Indonesia') !== false;
        });

        // Jumlah total data sebelum dan setelah filtering
        $total_data_before = count($gempaData);
        $total_data_after = count($filteredData);
        $total_data_removed = $total_data_before - $total_data_after;

        // Simpan data gempa yang telah difilter ke file sementara
        $tempCsvPath = WRITEPATH . 'uploads/temp_gempa_data.csv';
        $file = fopen($tempCsvPath, 'w');
        fputcsv($file, ['tgl', 'lat', 'lon', 'depth', 'mag', 'remark']);

        foreach ($filteredData as $gempa) {
            fputcsv($file, [$gempa['tgl'], $gempa['lat'], $gempa['lon'], $gempa['depth'], $gempa['mag'], $gempa['remark']]);
        }
        fclose($file);

        // Jalankan skrip Python untuk clustering
        $pythonExecutable = 'python';
        $clusterScriptPath = WRITEPATH . 'python_scripts/cluster.py';
        $command = escapeshellcmd("{$pythonExecutable} \"{$clusterScriptPath}\" \"{$tempCsvPath}\" {$n_clusters} 2>&1");
        log_message('debug', 'Menjalankan command clustering: ' . $command);
        $output = shell_exec($command);
        log_message('debug', 'Output dari skrip Python clustering: ' . $output);

        // Path hasil clustering
        $result_csv_path = str_replace(".csv", "_clustered.csv", $tempCsvPath);
        if (!file_exists($result_csv_path)) {
            log_message('error', 'File hasil clustering tidak ditemukan.');
            return;
        }

        // Baca hasil clustering
        $clusteredData = array_map('str_getcsv', file($result_csv_path));
        $header = array_shift($clusteredData); // Mengambil header
        $clusteredData = array_map(function ($row) use ($header) {
            return array_combine($header, $row);
        }, $clusteredData);

        // Jalankan skrip Python untuk menghasilkan peta sebelum clustering
        $mapScriptPath = WRITEPATH . 'python_scripts/generate_map.py';
        $preClusteredMapPath = FCPATH . 'uploads/pre_clustered_map.html';
        $command = escapeshellcmd("{$pythonExecutable} \"{$mapScriptPath}\" \"{$tempCsvPath}\" \"{$preClusteredMapPath}\" true 2>&1");
        log_message('debug', 'Menjalankan command generate map (pre-clustered): ' . $command);
        $output = shell_exec($command);
        log_message('debug', 'Output dari skrip Python generate map (pre-clustered): ' . $output);

        if (file_exists($preClusteredMapPath)) {
            $data['pre_clustered_map_path'] = base_url('uploads/pre_clustered_map.html');
        } else {
            log_message('error', 'Peta sebelum clustering tidak berhasil dihasilkan.');
            $data['pre_clustered_map_path'] = null;
        }

        // Jalankan skrip Python untuk menghasilkan peta setelah clustering
        $postClusteredMapPath = FCPATH . 'uploads/post_clustered_map.html';
        $command = escapeshellcmd("{$pythonExecutable} \"{$mapScriptPath}\" \"{$result_csv_path}\" \"{$postClusteredMapPath}\" false 2>&1");
        log_message('debug', 'Menjalankan command generate map (post-clustered): ' . $command);
        $output = shell_exec($command);
        log_message('debug', 'Output dari skrip Python generate map (post-clustered): ' . $output);

        if (file_exists($postClusteredMapPath)) {
            $data['post_clustered_map_path'] = base_url('uploads/post_clustered_map.html');
        } else {
            log_message('error', 'Peta setelah clustering tidak berhasil dihasilkan.');
            $data['post_clustered_map_path'] = null;
        }

        // Jalankan skrip Python untuk visualisasi cluster
        $visualizeScriptPath = WRITEPATH . 'python_scripts/visualize_clusters.py';
        $command = escapeshellcmd("{$pythonExecutable} \"{$visualizeScriptPath}\" \"{$result_csv_path}\" 2>&1");
        log_message('debug', 'Menjalankan command visualize clusters: ' . $command);
        $output = shell_exec($command);
        log_message('debug', 'Output dari skrip Python visualize clusters: ' . $output);

        if (!empty($output) && strpos($output, 'Error') === false) {
            $image_base64 = trim($output);
            log_message('debug', 'Image Base64: ' . $image_base64);
        } else {
            log_message('error', 'Visualisasi cluster tidak berhasil.');
            $image_base64 = null;
        }

        // Data yang dikirim ke view
        $data = [
            'clusters' => $clusteredData,
            'total_data_before' => $total_data_before,
            'total_data_after' => $total_data_after,
            'total_data_removed' => $total_data_removed,
            'image_base64' => $image_base64,
            'pre_clustered_map_path' => $data['pre_clustered_map_path'],
            'post_clustered_map_path' => $data['post_clustered_map_path'],
        ];

        echo view('clustering_result', $data);
    }
}