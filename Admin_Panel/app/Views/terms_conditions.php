<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Terms Conditions | <?= ($app_name) ? $app_name[0]->message : '' ?></title>        
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
                    </div>
                </section>
                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-secondary">
                                    <div class="card-header">
                                        <h3 class="card-title">Terms Conditions <small> Policy for App Usage</small></h3>
                                        <div class="float-right">
                                            <a href="<?php echo base_url(); ?>/play_store_terms_conditions" target='_blank' rel="noopener noreferrer" class='btn btn-primary'>Terms Conditions Page for Play Store</a>
                                        </div>
                                    </div>
                                    <form action="<?= base_url(); ?>/store_terms_conditions" role="form" id="insert_form" method="POST" enctype="multipart/form-data">
                                        <?= csrf_field() ?>
                                        <div class="card-body"> 
                                            <div class="form-group row">                                                
                                                <label class="col-sm-2 col-form-label">Terms Conditions</label>
                                                <div class="col-sm-10">
                                                    <textarea id="message" name="message" class="form-control">
                                                        <?php
                                                        if ($setting) {
                                                            echo $setting[0]->message;
                                                        }
                                                        ?>
                                                    </textarea>
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
        <script>
            $(document).ready(function () {
                $(document).on('focusin', function (e) {
                    if ($(e.target).closest(".tox-tinymce-aux, .moxman-window, .tam-assetmanager-root").length) {
                        e.stopImmediatePropagation();
                    }
                });
                tinymce.init({
                    selector: "#message",
                    height: 400,
                    plugins: [
                        'advlist', 'autolink', 'lists', 'link', 'charmap', 'preview',
                        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                        'insertdatetime', 'table', 'wordcount'
                    ],
                    toolbar: 'undo redo | blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify bullist numlist outdent indent removeformat link',
                    setup: function (editor) {
                        editor.on("change keyup", function (e) {
                            //tinyMCE.triggerSave(); // updates all instances
                            editor.save(); // updates this instance's textarea
                            $(editor.getElement()).trigger('change'); // for garlic to detect change
                        });
                    }
                });
            });
        </script>
    </body>
</html>