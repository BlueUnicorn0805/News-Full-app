<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <em class="fas fa-bars"></em>
            </a>
        </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                <img src="<?= APP_URL ?>public/images/user.jpg" class="user-image img-circle elevation-2"
                    alt="User Image">
                <span class="d-none d-md-inline">
                    <?php
                    $this->session = \Config\Services::session();
                    $this->session->start();
                    ?>
                    <?= ucwords($this->session->get('adminName')); ?>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <a href="<?= APP_URL ?>edit_profile" class="dropdown-item">
                    <em class="fas fa-user mr-2"></em> Edit Profile
                </a>
                <div class="dropdown-divider"></div>
                <a href="<?= APP_URL ?>logout" class="dropdown-item">
                    <em class="fas fa-power-off mr-2"></em> Logout
                </a>
            </div>
        </li>
    </ul>
    
</nav>
<!-- /.navbar -->
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= APP_URL ?>" class="brand-link">
        <img src="<?= APP_URL ?>public/images/<?= $app_logo[0]->message ?>" alt="Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">
            <?=($app_name) ? $app_name[0]->message : '' ?>
        </span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
        <br />
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="<?= APP_URL ?>dashboard" class="nav-link">
                        <em class="nav-icon fas fa-tachometer-alt"></em>
                        <p> Dashboard </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= APP_URL ?>breaking_news" class="nav-link">
                        <em class="nav-icon fas fa-newspaper"></em>
                        <p> Breaking News </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= APP_URL ?>live_streaming" class="nav-link">
                        <em class="nav-icon fas fa-stream"></em>
                        <p> Live Streaming </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= APP_URL ?>category" class="nav-link">
                        <em class="nav-icon fas fa-cube"></em>
                        <p> Category </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= APP_URL ?>subcategory" class="nav-link">
                        <em class="nav-icon fas fa-cubes"></em>
                        <p> Subcategory </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= APP_URL ?>tag" class="nav-link">
                        <em class="nav-icon fas fa-tag"></em>
                        <p> Tag </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= APP_URL ?>news" class="nav-link">
                        <em class="nav-icon fas fa-newspaper"></em>
                        <p> News </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= APP_URL ?>pages" class="nav-link">
                        <em class="nav-icon fas fa-file"></em>
                        <p> Pages </p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <em class="nav-icon fas fa-layer-group"></em>
                        <p> Featured Sections <em class="right fas fa-angle-left"></em></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= APP_URL ?>featured_sections" class="nav-link">
                                <em class="fas fa-folder-plus nav-icon"></em>
                                <p>Manage Home Sections</p>
                            </a>
                        </li>

                    </ul>
                </li>
                <li class="nav-item">
                    <a href="<?= APP_URL ?>ad_spaces" class="nav-link">
                        <em class="nav-icon fas fa-ad"></em>
                        <p> Ad Spaces </p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="<?= APP_URL ?>users" class="nav-link">
                        <em class="nav-icon fas fa-user"></em>
                        <p> Users <em class="right fas fa-angle-left"></em></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= APP_URL ?>users" class="nav-link">
                                <em class="fas fa-user nav-icon"></em>
                                <p>User List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= APP_URL ?>user_roles" class="nav-link">
                                <em class="fas fa-user-tie nav-icon"></em>
                                <p>User Roles</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="<?= APP_URL ?>comments" class="nav-link">
                        <em class="nav-icon fas fa-comments"></em>
                        <p> Comments </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= APP_URL ?>comments_flag" class="nav-link">
                        <em class="nav-icon fas fa-book"></em>
                        <p> Comments Flag </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= APP_URL ?>notification" class="nav-link">
                        <em class="nav-icon fas fa-bullhorn"></em>
                        <p> Send Notifications </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= APP_URL ?>survey" class="nav-link">
                        <em class="nav-icon fas fa-poll-h"></em>
                        <p> Survey </p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <em class="nav-icon fas fa-cog"></em>
                        <p> Settings <em class="right fas fa-angle-left"></em> </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= APP_URL ?>system_configurations" class="nav-link">
                                <em class="far fa-circle nav-icon"></em>
                                <p>System Configurations</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= APP_URL ?>web_settings" class="nav-link">
                                <em class="far fa-circle nav-icon"></em>
                                <p>Web Settings</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= APP_URL ?>notification_settings" class="nav-link">
                                <em class="far fa-circle nav-icon"></em>
                                <p>Notification Setting</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="<?= APP_URL ?>language" class="nav-link">
                        <em class="nav-icon fas fa-language"></em>
                        <p> Language </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= APP_URL ?>system_updates" class="nav-link">
                        <em class="nav-icon fas fa-cloud-upload-alt"></em>
                        <p> System Update </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= APP_URL ?>database_backup" class="nav-link">
                        <em class="nav-icon fas fa-cloud-download-alt"></em>
                        <p> Database Backup </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>