<?= $this->extend('layout/layout') ?>
<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Tambah Data Gempa via CSV</h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('/Gempa') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Tambah CSV</li>
        </ol>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Upload CSV File</h3>
            </div>
            <div class="box-body">
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>
                <form action="<?= base_url('/Gempa/uploadCsv') ?>" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="csvfile">CSV File</label>
                        <input type="file" name="csvfile" class="form-control" id="csvfile" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </form>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>