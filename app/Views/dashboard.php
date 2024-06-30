<?= $this->extend('layout/layout') ?>
<?= $this->section('content') ?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Data Gempa
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('/Testing') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <!-- DONUT CHART -->
                <div class="box box-danger">
                    <div class="box-header with-border">
                        <h3 class="box-title">Donut Chart</h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            <!-- <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
                        </div>
                    </div>
                    <div class="box-body chart-responsive">
                        <div class="chart" id="sales-chart" style="height: 300px;"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <!-- AREA CHART -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Area Chart</h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            <!-- <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
                        </div>
                    </div>
                    <div class="box-body chart-responsive">
                        <div class="chart" id="revenue-chart" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Tambahkan jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Tambahkan Morris.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.3.0/raphael.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>

<script>
$(function() {
    "use strict";

    // Data dari PHP
    var gempaData = <?= json_encode($gempaData) ?>;
    console.log(gempaData); // Tambahkan ini untuk memastikan data yang diterima

    // Pengelompokan data berdasarkan tahun-bulan
    var groupedData = {};
    gempaData.forEach(function(item) {
        var yearMonth = item.year_month;
        if (!groupedData[yearMonth]) {
            groupedData[yearMonth] = {
                year_month: yearMonth,
                count: 0
            };
        }
        groupedData[yearMonth].count += 1;
    });

    // Format data untuk Morris.js
    var formattedData = Object.keys(groupedData).map(function(yearMonth) {
        return {
            y: yearMonth,
            item1: groupedData[yearMonth].count
        };
    });

    console.log(formattedData); // Tambahkan ini untuk memastikan data yang diformat

    // AREA CHART
    var area = new Morris.Area({
        element: 'revenue-chart',
        resize: true,
        data: formattedData,
        xkey: 'y',
        ykeys: ['item1'],
        labels: ['Total Gempa'],
        lineColors: ['#a0d0e0'],
        hideHover: 'auto'
    });

    // DONUT CHART
    var donut = new Morris.Donut({
        element: 'sales-chart',
        resize: true,
        colors: ["#3c8dbc", "#f56954", "#00a65a"],
        data: [{
                label: "Magnitude < 4",
                value: gempaData.filter(item => item.mag < 4).length
            },
            {
                label: "Magnitude 4-6",
                value: gempaData.filter(item => item.mag >= 4 && item.mag <= 6).length
            },
            {
                label: "Magnitude > 6",
                value: gempaData.filter(item => item.mag > 6).length
            }
        ],
        hideHover: 'auto'
    });
});
</script>

<?= $this->endSection() ?>