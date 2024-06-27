<?php

namespace App\Models;

use CodeIgniter\Model;

class GempaModel extends Model
{
    protected $table            = 'gempa';
    protected $primaryKey       = 'idgempa';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'idgempa',
        'tanggal',
        'lat',
        'long',
        'depth',
        'mag',
        'remark'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    function getDataGempa($idgempa = false)
    {
        if ($idgempa == false) {
            # code...
            return $this->db->table('gempa')
                // ->orderBy('idgempa', 'DESC')
                ->get()->getResultArray();
        }
        return $this->db->table('gempa')
            // ->orderBy('idgempa', 'DESC')
            ->getWhere(['idgempa' => $idgempa])->getRowArray();
    }
}
