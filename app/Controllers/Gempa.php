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
        $save = $this->GempaModel->save([
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
        $file = $this->request->getFile('csvfile');

        if ($file->isValid() && !$file->hasMoved()) {
            $filePath = $file->getTempName();
            $csvData = array_map('str_getcsv', file($filePath));
            $header = array_shift($csvData);

            $model = new GempaModel();
            $existingData = $model->findAll();

            $newData = [];
            foreach ($csvData as $row) {
                $data = array_combine($header, $row);

                // Convert the date format if necessary
                if (isset($data['tgl'])) {
                    $data['tgl'] = $this->formatDate($data['tgl']);
                }

                // Check if the data already exists
                if (!$this->isDuplicate($data, $existingData)) {
                    $newData[] = $data;
                }
            }

            // Insert new data
            foreach ($newData as $data) {
                $model->insert($data);
            }

            return redirect()->to('/Gempa');
        } else {
            return redirect()->to('/Gempa/tambah')->with('error', 'There was a problem with the file upload');
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

    private function isDuplicate($data, $existingData)
    {
        foreach ($existingData as $existing) {
            if (
                $existing['tgl'] == $data['tgl'] &&
                $existing['lat'] == $data['lat'] &&
                $existing['lon'] == $data['lon'] &&
                $existing['depth'] == $data['depth'] &&
                $existing['mag'] == $data['mag'] &&
                $existing['remark'] == $data['remark']
            ) {
                return true;
            }
        }
        return false;
    }

    public function delete($idgempa)
    {
        $this->GempaModel->delete($idgempa);
        return redirect()->to('/Gempa');
    }
}
