<!DOCTYPE html>
<html lang="en">

    <head>
        <?php base_url() . include 'include.php'; ?>
        <title>Login | <?=($app_name) ? $app_name[0]->message : '' ?></title>

    </head>
 
    <body class="hold-transition login-page">
        <div class="login-box">
            <!-- /.Reset-password -->
            <div class="card">
                <div class="card-body login-card-body">
                    <div class="login-logo">
                        <a href="javascript:void(0)">
                            <img src="<?= APP_URL ?>public/images/<?= $app_logo_full[0]->message ?>" alt="image" width="200" />
                        </a>
                    </div>
                    
                    <h3 class="login-box-title">Reset Password</h3>
                    <p></p>
                    <form role="form" id="reset_password_form" action="<?= base_url(); ?>/update_password" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="forgot_unique_code" class="form-control" value="<?= $_GET['forgot_code']; ?>" >
                        <div class="form-group inner-addon left-addon">
                            <i class="fa fa-lock"></i>
                            <input type="password" name="password" class="form-control" id="password" placeholder="Password">
                        </div>
                        <div class="form-group inner-addon left-addon">
                            <i class="fa fa-lock"></i>
                            <input type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="Confirm Password">
                        </div>
                        
                        <div class="row col-12">
                            <p id="success_msg" class="alert alert-success"></p>
                        </div>
                        <div class="row col-12">
                            <p id="error_msg" class="alert alert-danger"></p>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-block login-btn" id="reset_submit_btn">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.Reset password-logo -->
        </div>
        <!-- jQuery -->
        <script src="<?= base_url(); ?>/public/plugins/jquery/jquery.min.js"></script>
        <!-- Bootstrap 4 -->
        <script src="<?= base_url(); ?>/public/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        <!-- Admin App -->
        <script src="<?= base_url(); ?>/public/dist/js/adminlte.min.js"></script>
        <!-- Validadtion js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.js"></script>

        <script type="text/javascript">
            $(document).ready(function () {
                /*Hide error, success msg*/ 
                $('#success_msg').hide();
                $('#error_msg').hide();
                /*on submit forgot password - email sent*/ 
                $('#reset_password_form').submit(function (e) {
                    e.preventDefault();
                    var form = $("#reset_password_form");
                    $.ajax({
                        type: "POST",
                        dataType: "JSON",
                        url: "update_password",
                        data: form.serialize(),
                        beforeSend: function () {
                            $('#reset_submit_btn').html('Checking...');
                        },
                        success: function (result) {
                            $('#reset_submit_btn').html('Reset Password');
                            if (result.error == false) {
                                $('#success_msg').html(result.message).show().delay(4000).fadeOut();
                                setTimeout(window.location.href = "/", 5000);
                            } else {
                                $('#error_msg').html(result.message).show().delay(4000).fadeOut();
                            }
                        },
                        error: function (result) {
                            $('#old_status').html("Error" + result);
                        }
                    });
                });

                $('#reset_password_form').validate({
                    rules: {
                        password: { required: true },
                        confirm_password: { required: true, equalTo: "#password" }
                    },
                    messages: {
                        password: { required: "Please enter password" },
                        confirm_password: { required: "Please enter password" }
                    },
                    errorElement: 'span',
                    errorPlacement: function (error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.form-group').append(error);
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