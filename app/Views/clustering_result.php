<?= $this->extend('layout/layout') ?>
<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Hasil Clustering Data Gempa</h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('/Clustering') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Clustering Result</li>
        </ol>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Cluster Results</h3>
            </div>
            <div class="box-body">
                <?php if (!empty($clusters) && is_array($clusters)) : ?>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Depth</th>
                                <th>Magnitude</th>
                                <th>Remark</th>
                                <th>Cluster</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clusters as $point) : ?>
                                <tr>
                                    <td><?= $point['tgl'] ?></td>
                                    <td><?= $point['lat'] ?></td>
                                    <td><?= $point['lon'] ?></td>
                                    <td><?= $point['depth'] ?></td>
                                    <td><?= $point['mag'] ?></td>
                                    <td><?= $point['remark'] ?></td>
                                    <td><?= $point['cluster'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p>Tidak ada data clustering yang ditemukan atau terjadi kesalahan dalam pemrosesan.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>