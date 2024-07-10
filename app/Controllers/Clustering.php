<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\GempaModel;

class Clustering extends Controller
{
    public function index()
    {
        $years = $this->getYears(); // Mendapatkan daftar tahun
        echo view('clustering_view', ['years' => $years]); // Menampilkan view untuk clustering dengan data tahun
    }

    public function cluster()
    {
        $n_clusters = $this->request->getPost('n_clusters'); // Mendapatkan jumlah cluster dari form input
        $cluster_by = $this->request->getPost('cluster_by'); // Mendapatkan metode clustering dari form input
        $year = $this->request->getPost('year'); // Mendapatkan tahun dari form input
        log_message('debug', 'Jumlah cluster yang diterima dari form: ' . $n_clusters);
        log_message('debug', 'Cluster berdasarkan: ' . $cluster_by);
        log_message('debug', 'Tahun: ' . $year);

        if (empty($n_clusters) || empty($cluster_by)) {
            log_message('error', 'Jumlah cluster atau metode clustering tidak ditemukan di form.'); // Log error jika input tidak ditemukan
            return;
        }

        $gempaData = $this->getGempaData($year);
        $filteredData = $this->filterData($gempaData);
        $this->logDataCounts($gempaData, $filteredData);

        $unfilteredCsvPath = $this->saveDataToCsv($gempaData, 'unfiltered_gempa_data.csv');
        $tempCsvPath = $this->saveDataToCsv($filteredData, 'temp_gempa_data.csv');

        $result_csv_path = $this->runClusteringScript($tempCsvPath, $n_clusters, $cluster_by);
        if (!$result_csv_path) return;

        $clusteredData = $this->readClusteredData($result_csv_path);

        $preClusteredMapPath = $this->generateMap($unfilteredCsvPath, 'pre_clustered_map.html', true);
        $postClusteredMapPath = $this->generateMap($result_csv_path, 'post_clustered_map.html', false);

        $year_range = null;
        if ($year === 'all') {
            $year_range = $this->getYearRange();
        }

        $image_base64 = $this->visualizeClusters($result_csv_path, $cluster_by, $year, $year_range);

        $silhouette_score = $this->calculateSilhouetteScore($result_csv_path, $n_clusters, $cluster_by);

        $data = [
            'idclusters' => $n_clusters,
            'clusters' => $clusteredData,
            'total_data_before' => count($gempaData),
            'total_data_after' => count($filteredData),
            'total_data_removed' => count($gempaData) - count($filteredData),
            'image_base64' => $image_base64,
            'pre_clustered_map_path' => $preClusteredMapPath,
            'post_clustered_map_path' => $postClusteredMapPath,
            'cluster_by' => $cluster_by, // Menambahkan cluster_by ke data
            'year' => $year, // Menambahkan year ke data
            'silhouette_score' => $silhouette_score, // Menambahkan silhouette score ke data
        ];

        return view('clustering_result', $data); // Menampilkan hasil ke view
    }

    private function getGempaData($year)
    {
        $model = new GempaModel();
        if ($year === 'all') {
            return $model->findAll(); // Mengambil semua data gempa dari model
        } else {
            return $model->where('YEAR(tgl)', $year)->findAll(); // Mengambil data gempa berdasarkan tahun
        }
    }

    private function getYears()
    {
        $model = new GempaModel();
        return $model->select('YEAR(tgl) as year')->distinct()->orderBy('year', 'DESC')->findAll();
    }

    private function getYearRange()
    {
        $model = new GempaModel();
        $minYear = $model->select('MIN(YEAR(tgl)) as min_year')->first()['min_year'];
        $maxYear = $model->select('MAX(YEAR(tgl)) as max_year')->first()['max_year'];
        return [$minYear, $maxYear];
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

    private function runClusteringScript($csvPath, $n_clusters, $cluster_by)
    {
        $pythonExecutable = 'python';
        $clusterScriptPath = WRITEPATH . 'python_scripts/cluster.py';
        $command = escapeshellcmd("{$pythonExecutable} \"{$clusterScriptPath}\" \"{$csvPath}\" {$n_clusters} {$cluster_by} 2>&1");
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

    private function visualizeClusters($csvPath, $cluster_by, $year, $year_range = null)
    {
        $pythonExecutable = 'python';
        $visualizeScriptPath = WRITEPATH . 'python_scripts/visualize_clusters.py';
        $command = escapeshellcmd("{$pythonExecutable} \"{$visualizeScriptPath}\" \"{$csvPath}\" \"{$cluster_by}\" \"{$year}\" " . ($year_range ? implode(' ', $year_range) : '') . " 2>&1");
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

    private function calculateSilhouetteScore($csvPath, $n_clusters, $cluster_by)
    {
        $pythonExecutable = 'python';
        $silhouetteScriptPath = WRITEPATH . 'python_scripts/calculate_silhouette.py';
        $command = escapeshellcmd("{$pythonExecutable} \"{$silhouetteScriptPath}\" \"{$csvPath}\" {$n_clusters} {$cluster_by}");
        log_message('debug', 'Menjalankan command calculate silhouette: ' . $command);
        $output = shell_exec($command);
        log_message('debug', 'Output dari skrip Python calculate silhouette: ' . $output);

        if (!empty($output) && strpos($output, 'Error') === false) {
            $silhouette_score = trim($output); // Mengambil output silhouette score jika tidak ada error
            log_message('debug', 'Silhouette Score: ' . $silhouette_score);
        } else {
            log_message('error', 'Perhitungan Silhouette Score tidak berhasil.'); // Log error jika perhitungan tidak berhasil
            $silhouette_score = null;
        }
        return $silhouette_score;
    }
}