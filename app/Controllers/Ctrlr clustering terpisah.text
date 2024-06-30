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

        $gempaData = $this->getGempaData();
        $filteredData = $this->filterData($gempaData);
        $this->logDataCounts($gempaData, $filteredData);

        $unfilteredCsvPath = $this->saveDataToCsv($gempaData, 'unfiltered_gempa_data.csv');
        $tempCsvPath = $this->saveDataToCsv($filteredData, 'temp_gempa_data.csv');

        $result_csv_path = $this->runClusteringScript($tempCsvPath, $n_clusters);
        if (!$result_csv_path) return;

        $clusteredData = $this->readClusteredData($result_csv_path);

        $preClusteredMapPath = $this->generateMap($unfilteredCsvPath, 'pre_clustered_map.html', true);
        $postClusteredMapPath = $this->generateMap($result_csv_path, 'post_clustered_map.html', false);

        $image_base64 = $this->visualizeClusters($result_csv_path);

        $data = [
            'idclusters' => $n_clusters,
            'clusters' => $clusteredData,
            'total_data_before' => count($gempaData),
            'total_data_after' => count($filteredData),
            'total_data_removed' => count($gempaData) - count($filteredData),
            'image_base64' => $image_base64,
            'pre_clustered_map_path' => $preClusteredMapPath,
            'post_clustered_map_path' => $postClusteredMapPath,
        ];

        return view('clustering_result', $data); // Menampilkan hasil ke view
    }

    private function getGempaData()
    {
        $model = new GempaModel();
        return $model->findAll(); // Mengambil semua data gempa dari model
    }

    private function filterData($gempaData)
    {
        return array_filter($gempaData, function ($item) {
            return stripos($item['remark'], 'Indonesia') !== false;
        });
    }

    private function logDataCounts($gempaData, $filteredData)
    {
        $total_data_before = count($gempaData);
        $total_data_after = count($filteredData);
        $total_data_removed = $total_data_before - $total_data_after;

        log_message('debug', 'Total data before filtering: ' . $total_data_before);
        log_message('debug', 'Total data after filtering: ' . $total_data_after);
        log_message('debug', 'Total data removed: ' . $total_data_removed);
    }

    private function saveDataToCsv($data, $filename)
    {
        $csvPath = WRITEPATH . 'uploads/' . $filename;
        $file = fopen($csvPath, 'w');
        fputcsv($file, ['tgl', 'lat', 'lon', 'depth', 'mag', 'remark']); // Menulis header CSV

        foreach ($data as $row) {
            fputcsv($file, [$row['tgl'], $row['lat'], $row['lon'], $row['depth'], $row['mag'], $row['remark']]);
        }
        fclose($file);

        log_message('debug', 'Data disimpan ke ' . $csvPath);
        return $csvPath;
    }

    private function runClusteringScript($csvPath, $n_clusters)
    {
        $pythonExecutable = 'python';
        $clusterScriptPath = WRITEPATH . 'python_scripts/cluster.py';
        $command = escapeshellcmd("{$pythonExecutable} \"{$clusterScriptPath}\" \"{$csvPath}\" {$n_clusters} 2>&1");
        log_message('debug', 'Menjalankan command clustering: ' . $command);
        $output = shell_exec($command);
        log_message('debug', 'Output dari skrip Python clustering: ' . $output);

        $result_csv_path = str_replace(".csv", "_clustered.csv", $csvPath);
        if (!file_exists($result_csv_path)) {
            log_message('error', 'File hasil clustering tidak ditemukan: ' . $result_csv_path);
            return false;
        }
        return $result_csv_path;
    }

    private function readClusteredData($csvPath)
    {
        $clusteredData = array_map('str_getcsv', file($csvPath));
        $header = array_shift($clusteredData);
        return array_map(function ($row) use ($header) {
            return array_combine($header, $row);
        }, $clusteredData);
    }

    private function generateMap($csvPath, $mapFilename, $isPreClustered)
    {
        $mapPath = FCPATH . 'uploads/' . $mapFilename;
        if (file_exists($mapPath)) {
            unlink($mapPath);
            log_message('debug', 'File ' . $mapFilename . ' dihapus.');
        }

        $pythonExecutable = 'python';
        $mapScriptPath = WRITEPATH . 'python_scripts/generate_map.py';
        $command = escapeshellcmd("{$pythonExecutable} \"{$mapScriptPath}\" \"{$csvPath}\" \"{$mapPath}\" " . ($isPreClustered ? 'true' : 'false') . " 2>&1");
        log_message('debug', 'Menjalankan command generate map (' . ($isPreClustered ? 'pre-clustered' : 'post-clustered') . '): ' . $command);
        $output = shell_exec($command);
        log_message('debug', 'Output dari skrip Python generate map (' . ($isPreClustered ? 'pre-clustered' : 'post-clustered') . '): ' . $output);

        if (file_exists($mapPath)) {
            log_message('debug', 'Peta ' . ($isPreClustered ? 'pre-clustered' : 'post-clustered') . ' berhasil dihasilkan: ' . $mapPath);
            return base_url('uploads/' . $mapFilename);
        } else {
            log_message('error', 'Peta ' . ($isPreClustered ? 'sebelum' : 'setelah') . ' clustering tidak berhasil dihasilkan.');
            return null;
        }
    }

    private function visualizeClusters($csvPath)
    {
        $pythonExecutable = 'python';
        $visualizeScriptPath = WRITEPATH . 'python_scripts/visualize_clusters.py';
        $command = escapeshellcmd("{$pythonExecutable} \"{$visualizeScriptPath}\" \"{$csvPath}\" 2>&1");
        log_message('debug', 'Menjalankan command visualize clusters: ' . $command);
        $output = shell_exec($command);
        log_message('debug', 'Output dari skrip Python visualize clusters: ' . $output);

        if (!empty($output) && strpos($output, 'Error') === false) {
            $image_base64 = trim($output); // Mengambil output visualisasi cluster jika tidak ada error
            log_message('debug', 'Image Base64: ' . $image_base64);
        } else {
            log_message('error', 'Visualisasi cluster tidak berhasil.'); // Log error jika visualisasi cluster tidak berhasil
            $image_base64 = null;
        }
        return $image_base64;
    }
}