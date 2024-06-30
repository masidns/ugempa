<?php

namespace App\Controllers;

use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Database\MigrationRunner;

class Home extends BaseController
{
    public function index(): string
    {
        $this->checkAndCreateDatabase();
        $this->checkAndRunMigrations();
        return view('welcome_message');
    }

    private function checkAndCreateDatabase()
    {
        $config = new \Config\Database();
        $defaultGroup = $config->defaultGroup;
        $dbConfig = $config->{$defaultGroup};

        // Buat koneksi tanpa memilih database
        $db = \Config\Database::connect([
            'hostname' => $dbConfig['hostname'],
            'username' => $dbConfig['username'],
            'password' => $dbConfig['password'],
            'DBDriver' => $dbConfig['DBDriver'],
            'charset'  => $dbConfig['charset'], // Tambahkan set karakter
            'DBCollat' => $dbConfig['DBCollat'], // Tambahkan collation
            'database' => null,
        ]);

        // Cek apakah database sudah ada
        $databaseName = $dbConfig['database'];
        $query = $db->query("SHOW DATABASES LIKE '$databaseName'");
        if ($query->getNumRows() == 0) {
            // Buat database jika belum ada
            $db->query("CREATE DATABASE $databaseName CHARACTER SET {$dbConfig['charset']} COLLATE {$dbConfig['DBCollat']}");
        }

        // Reconnect ke database yang baru dibuat
        $db->close();
        $db = \Config\Database::connect();
    }

    private function checkAndRunMigrations()
    {
        $db = \Config\Database::connect();
        $migration = \Config\Services::migrations();

        try {
            // Cek apakah tabel 'gempa' sudah ada
            if (!$db->tableExists('gempa')) {
                // Jalankan migrasi jika tabel belum ada
                $migration->latest();
            }
        } catch (DatabaseException $e) {
            // Tangani kesalahan jika ada
            log_message('error', $e->getMessage());
        }
    }
}