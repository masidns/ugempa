<?= $this->extend('layout/layout') ?>
<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Clustering Data Gempa</h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('/Clustering') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Clustering</li>
        </ol>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Pilih Jumlah Cluster</h3>
            </div>
            <div class="box-body">
                <form action="<?= base_url('/Clustering/cluster') ?>" method="post">
                    <div class="form-group">
                        <label for="n_clusters">Jumlah Cluster</label>
                        <input type="number" name="n_clusters" class="form-control" id="n_clusters" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Cluster</button>
                </form>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>