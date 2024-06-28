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
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                </div>
            </div>
            <div class="box-body">
                <?php if (!empty($clusters) && is_array($clusters)) : ?>
                <table id="example1" class="table table-bordered table-striped">
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

        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Visualisasi Cluster</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                </div>
            </div>
            <div class="box-body">
                <?php if (isset($image_base64)) : ?>
                <img src="data:image/png;base64,<?= $image_base64 ?>" alt="Cluster Visualization"
                    style="max-width: 100%; height: auto;">
                <?php else : ?>
                <p>Failed to generate visualization. Check the log file for details.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Peta Cluster</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                </div>
            </div>
            <div class="box-body">
                <?php if (isset($map_path)) : ?>
                <iframe src="<?= $map_path ?>" width="100%" height="500px" frameborder="0"></iframe>
                <?php else : ?>
                <p>Peta tidak berhasil dihasilkan atau terjadi kesalahan dalam pemrosesan.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>