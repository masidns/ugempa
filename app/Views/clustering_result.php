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
                <p>Total Data Sebelum Filtering: <?= esc($total_data_before) ?></p>
                <p>Total Data Setelah Filtering: <?= esc($total_data_after) ?></p>
                <p>Total Data yang Dihapus: <?= esc($total_data_removed) ?></p>
                <p>Total cluster: <?= esc($idclusters) ?></p>

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
                                    <td><?= esc($point['tgl']) ?></td>
                                    <td><?= esc($point['lat']) ?></td>
                                    <td><?= esc($point['lon']) ?></td>
                                    <td><?= esc($point['depth']) ?></td>
                                    <td><?= esc($point['mag']) ?></td>
                                    <td><?= esc($point['remark']) ?></td>
                                    <td><?= esc($point['cluster']) ?></td>
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
                <?php if (isset($image_base64) && !empty($image_base64)) : ?>
                    <img src="data:image/png;base64,<?= esc($image_base64) ?>" alt="Cluster Visualization" style="max-width: 100%; height: auto;">
                <?php else : ?>
                    <p>Failed to generate visualization. Check the log file for details.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Peta Sebelum Praprosessing Clustering</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                </div>
            </div>
            <div class="box-body">
                <?php if (isset($post_clustered_map_path)) : ?>
                    <iframe src="<?= esc($post_clustered_map_path) ?>" width="100%" height="500px" frameborder="0"></iframe>
                <?php else : ?>
                    <p>Peta sebelum praprosessing clustering tidak berhasil dihasilkan atau terjadi kesalahan dalam
                        pemrosesan.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Peta Sesudah Praprosessing Clustering</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                </div>
            </div>
            <div class="box-body">
                <?php if (isset($pre_clustered_map_path)) : ?>
                    <iframe src="<?= esc($pre_clustered_map_path) ?>" width="100%" height="500px" frameborder="0"></iframe>
                <?php else : ?>
                    <p>Peta sesudah praprosessing clustering tidak berhasil dihasilkan atau terjadi kesalahan dalam
                        pemrosesan.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>