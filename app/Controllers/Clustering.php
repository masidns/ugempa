<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\GempaModel;

class Clustering extends Controller
{
    public function index()
    {
        echo view('clustering_view'); // Menampilkan view untuk clustering
    }

    public function cluster()
    {
        $n_clusters = $this->request->getPost('n_clusters'); // Mendapatkan jumlah cluster dari form input
        log_message('debug', 'Jumlah cluster yang diterima dari form: ' . $n_clusters);

        if (empty($n_clusters)) {
            log_message('error', 'Jumlah cluster tidak ditemukan di form.'); // Log error jika jumlah cluster tidak ditemukan
            return;
        }

        $model = new GempaModel();
        $gempaData = $model->findAll(); // Mengambil semua data gempa dari model

        // Filter data untuk hanya menyertakan baris yang mengandung kata "Indonesia"
        $filteredData = array_filter($gempaData, function ($item) {
            return stripos($item['remark'], 'Indonesia') !== false;
        });

        // Jumlah total data sebelum dan setelah filtering
        $total_data_before = count($gempaData);
        $total_data_after = count($filteredData);
        $total_data_removed = $total_data_before - $total_data_after;

        // Simpan data gempa yang telah difilter ke file sementara
        $tempCsvPath = WRITEPATH . 'uploads/temp_gempa_data.csv'; // Path untuk menyimpan file sementara
        $file = fopen($tempCsvPath, 'w');
        fputcsv($file, ['tgl', 'lat', 'lon', 'depth', 'mag', 'remark']); // Menulis header CSV

        foreach ($filteredData as $gempa) {
            fputcsv($file, [$gempa['tgl'], $gempa['lat'], $gempa['lon'], $gempa['depth'], $gempa['mag'], $gempa['remark']]); // Menulis data gempa
        }
        fclose($file);

        // Jalankan skrip Python untuk clustering
        $pythonExecutable = 'python'; // Menentukan executable Python
        $clusterScriptPath = WRITEPATH . 'python_scripts/cluster.py'; // Path untuk skrip Python clustering
        $command = escapeshellcmd("{$pythonExecutable} \"{$clusterScriptPath}\" \"{$tempCsvPath}\" {$n_clusters} 2>&1"); // Membuat perintah untuk menjalankan skrip Python
        log_message('debug', 'Menjalankan command clustering: ' . $command);
        $output = shell_exec($command); // Menjalankan perintah
        log_message('debug', 'Output dari skrip Python clustering: ' . $output);

        // Path hasil clustering
        $result_csv_path = str_replace(".csv", "_clustered.csv", $tempCsvPath); // Path untuk file hasil clustering
        if (!file_exists($result_csv_path)) {
            log_message('error', 'File hasil clustering tidak ditemukan.'); // Log error jika file hasil clustering tidak ditemukan
            return;
        }

        // Baca hasil clustering
        $clusteredData = array_map('str_getcsv', file($result_csv_path)); // Membaca file CSV hasil clustering
        $header = array_shift($clusteredData); // Mengambil header
        $clusteredData = array_map(function ($row) use ($header) {
            return array_combine($header, $row); // Menggabungkan header dengan data
        }, $clusteredData);

        // Jalankan skrip Python untuk menghasilkan peta sebelum clustering
        $mapScriptPath = WRITEPATH . 'python_scripts/generate_map.py'; // Path untuk skrip Python generate_map
        $preClusteredMapPath = FCPATH . 'uploads/pre_clustered_map.html'; // Path untuk menyimpan peta pre-clustered
        $command = escapeshellcmd("{$pythonExecutable} \"{$mapScriptPath}\" \"{$tempCsvPath}\" \"{$preClusteredMapPath}\" true 2>&1"); // Membuat perintah untuk menjalankan skrip Python generate_map dengan flag pre-clustered
        log_message('debug', 'Menjalankan command generate map (pre-clustered): ' . $command);
        $output = shell_exec($command); // Menjalankan perintah
        log_message('debug', 'Output dari skrip Python generate map (pre-clustered): ' . $output);

        if (file_exists($preClusteredMapPath)) {
            $data['pre_clustered_map_path'] = base_url('uploads/pre_clustered_map.html'); // Menyimpan path peta pre-clustered ke variabel data
        } else {
            log_message('error', 'Peta sebelum clustering tidak berhasil dihasilkan.'); // Log error jika peta pre-clustered tidak berhasil dihasilkan
            $data['pre_clustered_map_path'] = null;
        }

        // Jalankan skrip Python untuk menghasilkan peta setelah clustering
        $postClusteredMapPath = FCPATH . 'uploads/post_clustered_map.html'; // Path untuk menyimpan peta post-clustered
        $command = escapeshellcmd("{$pythonExecutable} \"{$mapScriptPath}\" \"{$result_csv_path}\" \"{$postClusteredMapPath}\" false 2>&1"); // Membuat perintah untuk menjalankan skrip Python generate_map dengan flag post-clustered
        log_message('debug', 'Menjalankan command generate map (post-clustered): ' . $command);
        $output = shell_exec($command); // Menjalankan perintah
        log_message('debug', 'Output dari skrip Python generate map (post-clustered): ' . $output);

        if (file_exists($postClusteredMapPath)) {
            $data['post_clustered_map_path'] = base_url('uploads/post_clustered_map.html'); // Menyimpan path peta post-clustered ke variabel data
        } else {
            log_message('error', 'Peta setelah clustering tidak berhasil dihasilkan.'); // Log error jika peta post-clustered tidak berhasil dihasilkan
            $data['post_clustered_map_path'] = null;
        }

        // Jalankan skrip Python untuk visualisasi cluster
        $visualizeScriptPath = WRITEPATH . 'python_scripts/visualize_clusters.py'; // Path untuk skrip Python visualize_clusters
        $command = escapeshellcmd("{$pythonExecutable} \"{$visualizeScriptPath}\" \"{$result_csv_path}\" 2>&1"); // Membuat perintah untuk menjalankan skrip Python visualize_clusters
        log_message('debug', 'Menjalankan command visualize clusters: ' . $command);
        $output = shell_exec($command); // Menjalankan perintah
        log_message('debug', 'Output dari skrip Python visualize clusters: ' . $output);

        if (!empty($output) && strpos($output, 'Error') === false) {
            $image_base64 = trim($output); // Mengambil output visualisasi cluster jika tidak ada error
            log_message('debug', 'Image Base64: ' . $image_base64);
        } else {
            log_message('error', 'Visualisasi cluster tidak berhasil.'); // Log error jika visualisasi cluster tidak berhasil
            $image_base64 = null;
        }

        // Data yang dikirim ke view
        $data = [
            'idclusters' => $n_clusters, // yang di cluster
            'clusters' => $clusteredData, // Data cluster
            'total_data_before' => $total_data_before, // Total data sebelum filter
            'total_data_after' => $total_data_after, // Total data setelah filter
            'total_data_removed' => $total_data_removed, // Total data yang dihapus
            'image_base64' => $image_base64, // Visualisasi cluster
            'pre_clustered_map_path' => $data['pre_clustered_map_path'], // Path peta pre-clustered
            'post_clustered_map_path' => $data['post_clustered_map_path'], // Path peta post-clustered
        ];

        return view('clustering_result', $data); // Menampilkan hasil ke view
    }
}