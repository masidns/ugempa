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
                <form method="post" action="<?= base_url('clustering/cluster') ?>" id="clusteringForm">
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
                                <label for="cluster_by">Cluster Berdasarkan</label>
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
                    <button type="button" class="btn btn-secondary" id="calculateSilhouetteBtn">Hitung
                        Silhouette</button>
                </form>
            </div>
        </div>
    </section>
</div>

<!-- Modal for Silhouette Score -->
<div class="modal fade" id="silhouetteModal" tabindex="-1" role="dialog" aria-labelledby="silhouetteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="silhouetteModalLabel">Silhouette Score</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p>Total Data Sebelum Filtering: <span id="total_data_before"></span></p>
                        <p>Total Data Setelah Filtering: <span id="total_data_after"></span></p>
                        <p>Total Data yang Dihapus: <span id="total_data_removed"></span></p>
                        <p>Total cluster: <span id="idclusters"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p>Cluster Berdasarkan: <span id="cluster_by"></span></p>
                        <p>Tahun: <span id="year"></span></p>
                        <p>Silhouette Score: <span id="silhouette_score"></span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('calculateSilhouetteBtn').addEventListener('click', function() {
        const n_clusters = document.getElementById('n_clusters').value;
        const cluster_by = document.getElementById('cluster_by').value;
        const year = document.getElementById('year').value;

        if (!n_clusters || !cluster_by || !year) {
            alert('Please fill in all fields');
            return;
        }

        console.log('Sending data:', {
            n_clusters,
            cluster_by,
            year
        }); // Tambahkan ini untuk debugging

        fetch('<?= base_url('clustering/calculateSilhouette') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    n_clusters,
                    cluster_by,
                    year
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Received data:', data); // Tambahkan ini untuk debugging

                if (data.success) {
                    document.getElementById('silhouette_score').innerText = data.silhouette_score;
                } else {
                    document.getElementById('silhouette_score').innerText = 'Error: ' + data.message;
                }
                document.getElementById('total_data_before').innerText = data.total_data_before;
                document.getElementById('total_data_after').innerText = data.total_data_after;
                document.getElementById('total_data_removed').innerText = data.total_data_removed;
                document.getElementById('idclusters').innerText = n_clusters;
                document.getElementById('cluster_by').innerText = cluster_by;
                document.getElementById('year').innerText = year;
                $('#silhouetteModal').modal('show');
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });
</script>
<?= $this->endSection() ?>