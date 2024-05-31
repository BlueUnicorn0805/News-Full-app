<!DOCTYPE html>
<html lang="en">
    <head>
        <title>System Configurations | <?= ($app_name) ? $app_name[0]->message : '' ?></title>        
        <?php base_url() . include 'include.php'; ?>  
    </head>
    <body class="hold-transition sidebar-mini">
        <div class="wrapper">
            <?php base_url() . include 'header.php'; ?>  
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6"></div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>
                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-secondary">
                                    <div class="card-header">
                                        <h3 class="card-title">System Update <small class="text-bold"> Current Version <?= ($app_version) ? $app_version[0]->message : '' ?></small></h3>                                       
                                    </div>
                                    <form action="<?= base_url(); ?>/store_system_update" role="form" id="insert_form" method="POST" enctype="multipart/form-data">
                                        <?= csrf_field() ?>
                                        <div class="card-body"> 
                                            <div class="form-group row">  
                                                <div class="col-sm-6">
                                                    <label>Update Zip <small class="text-danger">Only zip file allow</small></label>
                                                    <div class="custom-file">
                                                        <input name="file" type="file" accept=".zip,.rar,.7zip" required class="custom-file-input" id="exampleInputFile2">
                                                        <label class="custom-file-label" id="custom-file-label1" for="customFile">Choose file</label>   
                                                        <small class="text-danger"> Your Current Version is <?= ($app_version) ? $app_version[0]->message : '' ?>. Please update nearest version here if available</small>
                                                    </div>
                                                </div>
                                            </div>                                            
                                            <?php if ($this->session->getFlashdata('error')) { ?>
                                                <div class="col-sm-6 offset-2">
                                                    <p id="error_msg" class="alert alert-danger"><?php echo $this->session->getFlashdata('error'); ?></p>
                                                </div>
                                            <?php } ?> 
                                            <?php if ($this->session->getFlashdata('success')) { ?>
                                                <div class="col-sm-6 offset-2">
                                                    <p id="success_msg" class="alert alert-success"><?php echo $this->session->getFlashdata('success'); ?></p>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="card-footer">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- /.content -->
            </div>
            <!-- /.modal -->
            <?php base_url() . include 'footer.php'; ?>  
        </div>
        <!-- ./wrapper -->
        <script type="text/javascript">
            $(document).ready(function () {
                $('#insert_form').validate({
                    errorElement: 'span',
                    errorPlacement: function (error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.form-group div').append(error);
                    },
                    highlight: function (element, errorClass, validClass) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function (element, errorClass, validClass) {
                        $(element).removeClass('is-invalid');
                    }
                });
            });
        </script>
    </body>
</html>