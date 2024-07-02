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
                <form method="post" action="<?= base_url('clustering/cluster') ?>">

                    <div class="row">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="n_clusters">Jumlah Cluster:</label>
                                <input type="number" class="form-control" id="n_clusters" name="n_clusters" required>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label for="year">Tahun:</label>
                                <select class="form-control" id="year" name="year" required>
                                    <option value="all">Semua</option>
                                    <?php foreach ($years as $year) : ?>
                                        <option value="<?= $year['year'] ?>">
                                            <?= $year['year'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label for="n_clusters">Cluster Berdasarkan</label>
                                <select class="form-control" id="cluster_by" name="cluster_by" required>
                                    <option>Pilih lah Cluster berdasarkan</option>
                                    <option value="depth">Depth</option>
                                    <option value="magnitude">Magnitude</option>
                                    <option value="depth_magnitude">Depth dan Magnitude</option>
                                </select>
                            </div>
                        </div>
                    </div>


                    <button type="submit" class="btn btn-primary">Cluster</button>
                </form>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>