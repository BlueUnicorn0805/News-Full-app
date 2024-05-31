<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Reset Password | <?= ($app_name) ? $app_name[0]->message : '' ?></title>        
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
                                        <h3 class="card-title">Edit Profile</h3>
                                    </div>
                                    <form action="<?= base_url(); ?>/update_profile" role="form" id="insert_form" method="POST" enctype="multipart/form-data">
                                        <?= csrf_field() ?>
                                        <div class="card-body">  
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label offset-2">User Name</label>
                                                <div class="col-sm-4">
                                                    <input type="text" id="username" name="username" class="form-control" placeholder="User Name" value="<?=($admin_info->username) ? $admin_info->username: '' ?>" required>
                                                </div>                                  
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label offset-2">Email ID</label>
                                                <div class="col-sm-4">
                                                    <input type="email" id="email" name="email" class="form-control" placeholder="Email" value="<?=($admin_info->email) ? $admin_info->email: '' ?>" required>
                                                </div>                                  
                                            </div>                                          
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label offset-2">Old Password</label>
                                                <div class="col-sm-4">
                                                    <input type="password" id="old_password" name="oldpassword" class="form-control" placeholder="Old Password" value="<?=($password) ? $password: '' ?>" required>
                                                </div>
                                                <label id="old_status"></label>                                                
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label offset-2">New Password </label>
                                                <div class="col-sm-4">
                                                    <input type="password" id="new_password" name="newpassword" class="form-control" placeholder="New Password">
                                                </div>                                                
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label offset-2">Confirm Password</label>
                                                <div class="col-sm-4">
                                                    <input type="password" id="confirm_password" name="confirmpassword" class="form-control" placeholder="Confirm Password">
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
                                            <button type="submit" class="btn btn-primary offset-4">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- /.content -->

            </div>

            <?php base_url() . include 'footer.php'; ?>  
        </div>
        <!-- ./wrapper -->  
        <script type="text/javascript">
            var allowsubmit = false;
            $(document).ready(function () {
                $('#insert_form').validate({
                    rules: {
                        new_password: {
                           
                        },
                        confirm_password: {
                           
                            equalTo: "#new_password"
                        }
                    },
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
                $('#old_password').on('change', function () {
                    var old_password = $(this).val();
                    $.ajax({
                        type: "POST",
                        dataType: "JSON",
                        url: "checkOldPass",
                        data: {oldpass: old_password},
                        beforeSend: function () {
                            $('#old_status').html('Checking..');
                        },
                        success: function (result) {
                            if (result == true) {
                                $('#old_status').html("<i class='fa fa-check-circle fa-2x text-success'></i>");=
                                allowsubmit = true;
                            } else {
                                $('#old_status').html("<i class='fa fa-times-circle fa-2x text-danger'></i>");=
                                allowsubmit = false;
                            }
                        },
                        error: function (result) {
                            $('#old_status').html("Error" + result);
                        }
                    });
                });
            });
           
            $(document).ready(function () {
                $('#insert_form').submit(function () {
                    if (allowsubmit) {
                        return true;
                    } else {
                       $('#old_password').focus();
                        return false;
                    }
                });
            });
        </script>
    </body>
</html>