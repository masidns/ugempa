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
                <form method="post" action="<?= base_url('/Silhouette/calculate') ?>">
                    <!-- Existing year and cluster_by inputs -->
                    <div class="form-group">
                        <label for="year">Year</label>
                        <select name="year" id="year" class="form-control">
                            <option value="all">All</option>
                            <?php foreach ($years as $year) : ?>
                                <option value="<?= $year['year'] ?>"><?= $year['year'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="cluster_by">Cluster By</label>
                        <select name="cluster_by" id="cluster_by" class="form-control">
                            <option value="depth">Depth</option>
                            <option value="magnitude">Magnitude</option>
                            <option value="depth_magnitude">Depth & Magnitude</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="max_clusters">Max Clusters</label>
                        <input type="number" name="max_clusters" id="max_clusters" class="form-control" min="2" value="10">
                    </div>
                    <button type="submit" class="btn btn-primary">Calculate</button>
                </form>


            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>