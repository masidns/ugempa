<?= $this->extend('layout/layout') ?>
<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Hasil Silhouette Score</h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('/Clustering') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Silhouette Score</li>
        </ol>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Hasil Silhouette Score</h3>
            </div>
            <div class="box-body">
                <p><strong>Tahun:</strong> <?= $year === 'all' ? 'All' : $year ?></p>
                <p><strong>Cluster By:</strong> <?= $cluster_by ?></p>
                <p><strong>Max Clusters:</strong> <?= $max_clusters ?></p>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Cluster</th>
                            <th>Silhouette Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($result)) : ?>
                            <?php
                            // Cari nilai Silhouette Score tertinggi
                            $max_score = max($result);
                            ?>
                            <?php foreach ($result as $cluster => $score) : ?>
                                <tr <?= $score == $max_score ? 'style="background-color: #d4edda;"' : '' ?>>
                                    <td><?= $cluster ?></td>
                                    <td><?= $score ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="2">No data available</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>