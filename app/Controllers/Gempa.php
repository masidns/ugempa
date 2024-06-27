<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\GempaModel;
use CodeIgniter\HTTP\ResponseInterface;

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
            'tanggal' => $this->request->getVar('tanggal'),
            'lat' => $this->request->getVar('lat'),
            'long' => $this->request->getVar('long'),
            'depth' => $this->request->getVar('depth'),
            'mag' => $this->request->getVar('mag'),
            'remark' => $this->request->getVar('remark'),
        ]);
        // dd($save);
        return redirect()->to('Gempa');
    }

    public function delete($idgempa)
    {
        $this->GempaModel->delete($idgempa);
        return redirect()->to('/Gempa');
    }
}
