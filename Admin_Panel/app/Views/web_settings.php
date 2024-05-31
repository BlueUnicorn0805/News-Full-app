<!DOCTYPE html>
<html lang="en">
<head>
    <title>Web Settings | <?=($app_name) ? $app_name[0]->message : '' ?></title>
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
                                    <h3 class="card-title">System Settings for App <small> Note that this will directly reflect the changes in App</small></h3>
                                </div>
                                <form action="<?= base_url(); ?>/store_web_settings" role="form" id="insert_form" method="POST" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <div class="card-body">
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>Web Name</label>
                                                <input type="text" name="web_name" value="<?=($web_name) ? $web_name[0]->message : '' ?>" class="form-control" placeholder="Web Name" required />
                                            </div>
                                            <div class="col-sm-2">
                                                <label>Web Color</label>
                                                <input type="color" name="web_color_code" value="<?=($web_color_code) ? $web_color_code[0]->message : '' ?>" class="form-control" placeholder="Web Name" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>Header Logo <small class="text-danger">( size 180 * 60 )</small></label>
                                                <div class="custom-file">
                                                    <input name="web_header_logo" type="file" class="custom-file-input" id="web_header_logo">
                                                    <label class="custom-file-label" id="custom-file-label1" for="customFile">Choose file</label>
                                                    <small class="text-danger">Only png, jpg, webp, gif and jpeg image allow</small>
                                                    <p style="display: none" id="img_error_msg1" class="alert alert-danger"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label>Footer Logo <small class="text-danger">( size 180 * 60 )</small></label>
                                                <div class="custom-file">
                                                    <input name="web_footer_logo" type="file" class="custom-file-input" id="web_footer_logo">
                                                    <label class="custom-file-label" id="custom-file-label" for="customFile">Choose file</label>
                                                    <small class="text-danger">Only png, jpg, webp, gif and jpeg image allow</small>
                                                    <p style="display: none" id="img_error_msg" class="alert alert-danger"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <?php if ($web_header_logo) { ?>
                                                    <img src="<?= APP_URL ?>public/images/<?= $web_header_logo[0]->message ?>" width="100" />
                                                <?php } ?>
                                            </div>
                                            <div class="col-sm-6">
                                                <?php if ($web_footer_logo) { ?>
                                                    <img src="<?= APP_URL ?>public/images/<?= $web_footer_logo[0]->message ?>" height="100" />
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-12">
                                                <label>Footer Description</label>
                                                <textarea name="web_footer_description" class="form-control"><?=($web_footer_description) ? $web_footer_description[0]->message : '' ?></textarea>
                                            </div>
                                        </div>
                                        <hr>
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
        var _URL = window.URL || window.webkitURL;
        $("#web_header_logo").change(function(e) {
            var file, img;
            if ((file = this.files[0])) {
                img = new Image();
                img.onerror = function() {
                    $("#web_header_logo").val('');
                    $('#custom-file-label').html('');
                    $('#img_error_msg').html("Invalid Image Type");
                    $('#img_error_msg').show().delay(3000).fadeOut();
                };
                img.src = _URL.createObjectURL(file);
            }
        });
        $("#web_footer_logo").change(function(e) {
            var file, img;
            if ((file = this.files[0])) {
                img = new Image();
                img.onerror = function() {
                    $("#web_footer_logo").val('');
                    $('#custom-file-label1').html('');
                    $('#img_error_msg1').html("Invalid Image Type");
                    $('#img_error_msg1').show().delay(3000).fadeOut();
                };
                img.src = _URL.createObjectURL(file);
            }
        });
        $("#favicon_icon").change(function(e) {
            var file, img;
            if ((file = this.files[0])) {
                img = new Image();
                img.onerror = function() {
                    $("#favicon_icon").val('');
                    $('#custom-file-label1').html('');
                    $('#img_error_msg1').html("Invalid Image Type");
                    $('#img_error_msg1').show().delay(3000).fadeOut();
                };
                img.src = _URL.createObjectURL(file);
            }
        });
    </script>
    <script type="text/javascript">
        $('#insert_form').validate({
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group div').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
    </script>
    <script type="text/javascript">
        $("input[data-bootstrap-switch]").each(function() {
            $(this).bootstrapSwitch('state', $(this).prop('checked'));
        });
    </script>
</body>
</html>