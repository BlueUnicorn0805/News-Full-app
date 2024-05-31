<?php
$db_config_path = '../.env';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST) {

    require_once('taskClass.php');
    require_once('includes/databaseLibrary.php');

    $core = new Core();
    $database = new Database();

    if (!empty($_POST['hostname']) && !empty($_POST['username']) && !empty($_POST['database'])) {
        if ($database->create_database($_POST) == false) {
            $message = $core->show_message('error', "The database could not be created, make sure your the host, username, password, database name is correct.");
        } else if ($core->write_config($_POST) == false) {
            $message = $core->show_message('error', "The database configuration file could not be written, please chmod .env file to 755");
        } else if ($database->create_tables($_POST) == false) {
            $message = $core->show_message('error', "The database could not be created, make sure your the host, username, password, database name is correct.");
        } else if ($core->checkFile() == false) {
            $message = $core->show_message('error', "File .env is Empty");
        }
        if (!isset($message)) {
            $urlWb = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $urlWb = str_replace('install/index.php', '', $urlWb);
            $urlWb = str_replace('install/', '', $urlWb);
            $core->delete_directory('../install/');
            $type = 'success';
            $message = $core->show_message('success', 'Congrats! Installation is successful. Please wait redirecting you to the main page in seconds.. .');
            header('Refresh:5; url=' . $urlWb);
        }
    } else {
        $message = $core->show_message('error', 'The host, username, password, database name required.');
    }
}
?>
<!DOCTYPE html>
<html>

    <head>
        <meta charset="UTF-8">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Welcome to Installer</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="assets/style.css" rel="stylesheet">
    </head>

    <body class="bg-theme">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-9 py-4">  
                    <div class="card p-3">
                        <h3 class="text-center">News App Installer</h3>
                        <hr>
                        <?php
                        if (is_writable($db_config_path)) {
                            ?>
                            <?php
                            if (isset($message)) {
                                if (isset($type) && $type == 'success') {
                                    ?>
                                    <div class="alert alert-success alert-dismissible" role="alert">
                                        <?= $message; ?>
                                    </div>
                                <?php } else { ?>
                                    <div class="alert alert-warning alert-dismissible" role="alert">
                                        <?= $message; ?>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                            <form class="" id="install_form" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                <div id="wizard_verticle" class="form_wizard wizard_horizontal">
                                    <ul class="list-unstyled wizard_steps">
                                        <li><a href="#step-1"><span class="step_no">1</span></a></li>
                                        <li><a href="#step-2"><span class="step_no">2</span></a></li>
                                        <li><a href="#step-3"><span class="step_no">3</span></a></li>
                                    </ul>

                                    <div id="step-1">
                                        <div class="col-md-12">
                                            <h5 class="text-center">Check Server Requirement</h5>
                                            <div class="form-group border p-2">
                                                <label class="control-label">PHP Version (>= 7.3)</label>
                                                <?php
                                                $value1 = 7.3;
                                                $value2 = phpversion();
                                                ?>
                                                <label class="checkboxone"> 
                                                    <input type="checkbox" disabled value="<?= ($value1 <= $value2) ? '1' : '0' ?>" name="php_version" <?= ($value1 <= $value2) ? 'checked' : '' ?> required>
                                                    <label class="checkboxInput"> </label>
                                                </label>
                                            </div>
                                            <div class="form-group border p-2">
                                                <?php $openssl_extension = extension_loaded('openssl'); ?>
                                                <label class="control-label">OpenSSL PHP Extension</label>                                               
                                                <label class="checkboxone"> 
                                                    <input type="checkbox" disabled value="<?= ($openssl_extension) ? '1' : '0' ?>" name="openssl_extension" <?= ($openssl_extension) ? 'checked' : '' ?> required>
                                                    <label class="checkboxInput"> </label>
                                                </label>
                                            </div>
                                            <div class="form-group border p-2">
                                                <?php $zip_extension = extension_loaded('zip'); ?>
                                                <label class="control-label">ZipArchive Extension</label>                                               
                                                <label class="checkboxone"> 
                                                    <input type="checkbox" disabled value="<?= ($zip_extension) ? '1' : '0' ?>" name="zip_extension" <?= ($zip_extension) ? 'checked' : '' ?> required>
                                                    <label class="checkboxInput"> </label>
                                                </label>
                                            </div>
                                        </div>                                        
                                    </div>

                                    <div id="step-2">
                                        <div class="col-md-12">
                                            <div class="outer_div">
                                                <div class="form-group row">
                                                    <div class="col-md-12 text-center">
                                                        <h5>Check Purchase Code</h5>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-12">
                                                        <label>Purchase Code | <a style="color:red;" href="https://cutt.ly/PLFZenO" target="_blank">NULLED Web Community</a><small class="text-danger">*</small></label>
                                                        <input name="purchase_code" type="text" id="purchase_code" class="form-control" required placeholder="Enter random value" />
                                                        <?php
                                                        $appurl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                                                        $appurl = preg_replace('#^https?://#i', '', $appurl);
                                                        $appurl = str_replace('install/index.php', '', $appurl);
                                                        $appurl = str_replace('install/', '', $appurl);
                                                        ?>
                                                        <input value="<?= $appurl; ?>" name="app_url" type="hidden" id="app_url" class="form-control" required/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="step-3">
                                        <div class="col-md-12">
                                            <div class="outer_div">
                                                <div class="form-group row">
                                                    <div class="col-md-12">
                                                        <div class="check_server_req">
                                                            <h6>Database Connection</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-6">
                                                        <label>Database Hostname <small class="text-danger">*</small></label>
                                                        <input name="hostname" type="text" id="hostname" value="localhost" class="form-control" required placeholder="Your Hostname" />
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Database Name <small class="text-danger">*</small></label>
                                                        <input  name="database" type="text" id="database" class="form-control" required placeholder="Your Database Name"/>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-6">
                                                        <label>Database Username <small class="text-danger">*</small></label>
                                                        <input name="username" type="text" id="username" class="form-control" required placeholder="Your Username"/>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Database Password</label>
                                                        <input name="password" type="password" id="password" class="form-control" placeholder="Your Password"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </form>
                            <?php
                        } else {
                            ?>
                            <p class="alert alert-danger">
                                Please make the .env file writable.<br>
                                <strong>Example</strong>:<br />
                                <code>chmod 755 .env</code>
                            </p>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js" type="text/javascript"></script>
        <script src="assets/jquery.smartWizard.js"></script>
        <script src="assets/smartWizard-validation.js"></script>

    </body>

</html>