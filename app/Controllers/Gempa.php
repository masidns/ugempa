<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\GempaModel;
use CodeIgniter\HTTP\ResponseInterface;
use DateTime;

class Gempa extends BaseController
{

    protected $GempaModel;
    public function __construct()
    {
        //Do your magic here
        $this->GempaModel = new GempaModel();
    }


    public function index()
    {
        //
        $data = [
            'datagempa' => $this->GempaModel->getDataGempa(),
        ];
        return view('Gempa/index', $data);
    }

    public function tambah()
    {
        //
        return view('Gempa/tambah');
    }
    public function save()
    {
        $this->GempaModel->save([
            'tgl' => $this->request->getVar('tgl'),
            'lat' => $this->request->getVar('lat'),
            'lon' => $this->request->getVar('lon'),
            'depth' => $this->request->getVar('depth'),
            'mag' => $this->request->getVar('mag'),
            'remark' => $this->request->getVar('remark'),
        ]);
        // dd($save);
        return redirect()->to('Gempa');
    }

    public function CSV()
    {
        //
        return view('Gempa/CSV');
    }

    public function uploadCsv()
    {
        // set_time_limit(1000); // Menambah batas waktu menjadi 300 detik (5 menit)

        $file = $this->request->getFile('csvfile');

        if ($file->isValid() && !$file->hasMoved()) {
            $filePath = $file->getTempName();
            $csvData = array_map('str_getcsv', file($filePath));
            $header = array_shift($csvData);

            $model = $this->GempaModel;
            $newData = [];
            foreach ($csvData as $row) {
                $data = array_combine($header, $row);

                // Convert the date format if necessary
                if (isset($data['tgl'])) {
                    $data['tgl'] = $this->formatDate($data['tgl']);
                }

                // Check if the data already exists
                if (!$this->isDuplicate($data)) {
                    $newData[] = $data;
                }
            }

            // Insert new data
            foreach ($newData as $data) {
                $model->insert($data);
            }

            return redirect()->to('/Gempa');
        } else {
            return redirect()->to('/Gempa/tambah')->with('error', 'Terjadi masalah saat mengunggah file');
        }
    }


    private function formatDate($date)
    {
        // Assuming the input date format is mm/dd/yyyy
        $dateObj = DateTime::createFromFormat('m/d/Y', $date);
        if ($dateObj) {
            return $dateObj->format('Y-m-d');
        } else {
            // Debugging: Log error if date is invalid
            error_log('Invalid date format: ' . $date);
            return null; // or handle the error as appropriate
        }
    }

    private function isDuplicate($data)
    {
        $model = $this->GempaModel;
        $exists = $model->where([
            'tgl' => $data['tgl'],
            'lat' => $data['lat'],
            'lon' => $data['lon'],
            'depth' => $data['depth'],
            'mag' => $data['mag'],
            'remark' => $data['remark'],
        ])->first();

        return !empty($exists);
    }


    public function delete($idgempa)
    {
        $this->GempaModel->delete($idgempa);
        return redirect()->to('/Gempa');
    }
}
