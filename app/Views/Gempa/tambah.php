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

    <section class="content">
        <!-- SELECT2 EXAMPLE -->
        <form action="/Gempa/save" method="post" enctype="multipart/form-data">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Tambah data gempa</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input name="tanggal" type="date" class="form-control" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask="">
                                </div><!-- /.input group -->
                            </div>
                            <div class="form-group">
                                <label>Latitude</label>
                                <input name="lat" type="text" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Longitude</label>
                                <input name="long" type="text" class="form-control">
                            </div>

                        </div><!-- /.col -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Depth</label>
                                <input name="depth" type="text" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Magnitude</label>
                                <input name="mag" type="text" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Remark</label>
                                <input name="remark" type="text" class="form-control">
                            </div>

                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.box-body -->
                <div class="box-footer">
                    <button type="submit" class="btn btn-info btn-flat margin" style="float: right;">Simpan</button>
                </div>
            </div><!-- /.box -->
        </form>
    </section>

</div>
<?= $this->endsection('content') ?>