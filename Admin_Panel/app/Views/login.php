    <!DOCTYPE html>
    <html lang="en">

        <head>
            <?php base_url() . include 'include.php'; ?>
            <title>Login | <?=($app_name) ? $app_name[0]->message : '' ?></title>
        </head>
    
        <body class="hold-transition login-page">
            <div class="login-box">
                <!-- /.login-logo -->
                <div class="card" id="login_form_card">
                    <div class="card-body login-card-body">
                        <div class="login-logo">
                            <a href="javascript:void(0)">
                                <img src="<?= APP_URL ?>public/images/<?= $app_logo_full[0]->message ?>" alt="image" width="200" />
                            </a>
                        </div>
                        
                        <h3 class="login-box-title">Welcome!</h3>
                        <p class="login-box-msg">Please Login to your Account</p>
                        <form role="form" id="login_form" action="<?= base_url(); ?>/checklogin" method="post">
                            <?= csrf_field() ?>
                            <div class="form-group inner-addon left-addon">
                                <i class="fa fa-user"></i>
                                <input type="text" name="username" class="form-control" id="exampleInputUsername1" placeholder="Username">
                            </div>
                            <div class="form-group inner-addon left-addon">
                                <i class="fa fa-lock"></i>
                                <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                            </div>
                            <div class="form-group text-right">
                            <a id="forgot_password_btn"href="">Forgot Password?</a>
                            </div>
                            <?php
                            $this->session = \Config\Services::session();
                            $this->session->start();
                            if ($this->session->getFlashdata('error')) {
                                ?>
                                <div class="row col-12">
                                    <p id="error_msg" class="alert alert-danger"><?php echo $this->session->getFlashdata('error'); ?></p>
                                </div>
                            <?php } ?>
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-block login-btn">Login</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /.forgot password-logo -->
                <div class="card" id="forgot_password_card">
                    <div class="card-body login-card-body">
                        <div class="login-logo">
                            <a href="javascript:void(0)">
                                <img src="<?= APP_URL ?>public/images/<?= $app_logo_full[0]->message ?>" alt="image" width="200" />
                            </a>
                        </div>
                        
                        <h3 class="login-box-title">Reset Your<br/>Password here!</h3>
                        <p class="login-box-msg"></p>
                        <form role="form" id="forgot_password_form" action="<?= base_url(); ?>/check_email" method="post">
                            <?= csrf_field() ?>
                            <div class="form-group inner-addon left-addon">
                                <i class="fa fa-user"></i>
                                <input type="email" name="email" class="form-control" id="email" placeholder="Email Address">
                            </div>
                            <div class="row col-12">
                                <p id="email_sent" class="alert alert-success"></p>
                            </div>
                            <div class="row col-12">
                                <p id="invalid_email" class="alert alert-danger"></p>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" id="forgot_submit_btn" class="btn btn-primary btn-block login-btn">Reset Password</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- jQuery -->
            <script src="<?= base_url(); ?>/public/plugins/jquery/jquery.min.js"></script>
            <!-- Bootstrap 4 -->
            <script src="<?= base_url(); ?>/public/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
            <!-- Admin App -->
            <script src="<?= base_url(); ?>/public/dist/js/adminlte.min.js"></script>
            <!-- Validadtion js -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.js"></script>

            <script>
                $(document).ready(function () {
                    $('#error_msg').delay(4000).fadeOut();
                    /*By Default Hide Forgot Password Form*/ 
                    $('#forgot_password_card').hide();

                    /*On click Forgot Password -> show Forgot password form*/ 
                    $('#forgot_password_btn').on('click', function (e) {
                        e.preventDefault();
                        $('#login_form_card').hide();
                        $('#forgot_password_card').show();
                    });
                     /*Hide error, success msg*/ 
                    $('#email_sent').hide();
                    $('#invalid_email').hide();
                    

                    /*on submit forgot password - email sent*/ 
                    $('#forgot_password_form').submit(function (e) {
                        e.preventDefault();
                        var email = $('#email').val();
                        $.ajax({
                            type: "POST",
                            dataType: "JSON",
                            url: "check_email",
                            data: {email: email},
                            beforeSend: function () {
                                $('#forgot_submit_btn').html('Checking...');
                            },
                            success: function (result) {
                                $('#forgot_submit_btn').html('Reset Password');
                                if (result == true) {
                                    $('#email_sent').html("Email Sent Successfully").show().delay(4000).fadeOut();
                                    allowsubmit = true;
                                } else {
                                    $('#invalid_email').html("Invalid Email ID.").show().delay(4000).fadeOut();
                                }
                            },
                            error: function (result) {
                                $('#old_status').html("Error" + result);
                            }
                        });
                    });

                });
            </script>
            <script type="text/javascript">
                $(document).ready(function () {
                    $('#login_form').validate({
                        rules: {
                            username: { required: true },
                            password: { required: true }
                        },
                        messages: {
                            username: { required: "Please enter username" },
                            password: { required: "Please enter password" }
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