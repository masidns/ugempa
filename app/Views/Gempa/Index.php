<?= $this->extend('layout/layout') ?>
<?= $this->section('content') ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Data Gempa
            <!-- <small>Blank example to the fixed layout</small> -->
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('/Testing') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <!-- <li><a href="#">Layout</a></li> -->
            <!-- <li class="active">Fixed</li> -->
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Data Table With Full Features</h3>
                <a href="<?= base_url('/Gempa/tambah') ?>" class="btn bg-olive btn-flat margin" style="float: right;">Tambah
                    Data</a>
            </div><!-- /.box-header -->
            <div class="box-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Latitude</th>
                            <th>Longitude</th>
                            <th>Depth</th>
                            <th>Magnitude</th>
                            <th>Remark</th>
                            <th><i class="fa fa-gears"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datagempa as $key => $value) : ?>
                            <tr>
                                <td><?= $value['tanggal'] ?></td>
                                <td><?= $value['lat'] ?></td>
                                <td><?= $value['long'] ?></td>
                                <td><?= $value['depth'] ?></td>
                                <td><?= $value['mag'] ?></td>
                                <td><?= $value['remark'] ?></td>
                                <td align="center">
                                    <a href="" class="btn btn-warning fa fa-edit"></a>
                                    <a type="submit" href="Gempa/delete/<?= $value['idgempa'] ?>" class=" btn btn-danger fa fa-trash" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <!-- <tfoot>
                        <tr>
                            <th>Rendering engine</th>
                            <th>Browser</th>
                            <th>Platform(s)</th>
                            <th>Engine version</th>
                            <th>CSS grade</th>
                        </tr>
                    </tfoot> -->
                </table>
            </div><!-- /.box-body -->
        </div><!-- /.box -->

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<?= $this->endsection('content') ?>