<!DOCTYPE html>
<html lang="en">

    <head>
        <title>Users | <?=($app_name) ? $app_name[0]->message : '' ?></title>
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
                                        <h3 class="card-title">Users <small> View </small></h3>
                                    </div>
                                    <div class="card-body">
                                        <?php if ($this->session->getFlashdata('error')) { ?>
                                            <div class="col-sm-6">
                                                <p id="error_msg" class="alert alert-danger"><?php echo $this->session->getFlashdata('error'); ?></p>
                                            </div>
                                        <?php } ?>
                                        <?php if ($this->session->getFlashdata('success')) { ?>
                                            <div class="col-sm-6">
                                                <p id="success_msg" class="alert alert-success">
                                                    <?php echo $this->session->getFlashdata('success'); ?>
                                                </p>
                                            </div>
                                        <?php } ?>
                                        <table aria-describedby="mydesc" class='table-striped' id='users_list'
                                            data-toggle="table" data-url="<?= APP_URL . 'Table/users' ?>"
                                            data-click-to-select="true" data-side-pagination="server"
                                            data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                                            data-search="true" data-show-columns="true" data-show-refresh="true"
                                            data-fixed-columns="true" data-fixed-number="1" data-fixed-right-number="1"
                                            data-trim-on-search="false" data-sort-name="id" data-sort-order="desc"
                                            data-mobile-responsive="true" data-maintain-selected="true"
                                            data-export-types='["txt","excel"]'
                                            data-export-options='{ "fileName": "users-list-<?= date('d-m-y') ?>" }'
                                            data-query-params="queryParams">
                                            <thead>
                                                <tr>
                                                    <th scope="col" data-field="id" data-sortable="true">ID</th>
                                                    <th scope="col" data-field="profile" data-sortable="true">Profile
                                                    </th>
                                                    <th scope="col" data-field="name" data-sortable="true">Name</th>
                                                    <th scope="col" data-field="email" data-sortable="true">Email</th>
                                                    <th scope="col" data-field="type" data-sortable="false">Type</th>
                                                    <th scope="col" data-field="mobile" data-sortable="true">Mobile</th>
                                                    <th scope="col" data-field="status" data-sortable="false">Status
                                                    </th>
                                                    <th scope="col" data-field="date" data-sortable="true">Date</th>
                                                    <th scope="col" data-field="role" data-sortable="true">Role</th>
                                                    <th scope="col" data-field="operate" data-sortable="false"
                                                        data-events="actionEvents">Operate</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
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
        <div class="modal fade" id="editDataModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit User</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="<?= APP_URL ?>update_users" role="form" id="update_form" method="POST"
                        enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type='hidden' name="edit_id" id="edit_id" value='' />
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Role</label>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div id="status" class="btn-group">
                                        <select id="edit_role" name="role" class="form-control">
                                            <option value="">Select User</option>
                                            <?php foreach ($user_roles as $user_role): ?>
                                                <option value="<?= $user_role['id']; ?>">
                                                    <?= $user_role['role']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Status</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div id="status" class="btn-group">
                                        <label class="btn btn-danger" data-toggle-class="btn-primary"
                                            data-toggle-passive-class="btn-default">
                                            <input type="radio" name="edit_status" value="0"> Deactive
                                        </label>
                                        <label class="btn btn-success" data-toggle-class="btn-primary"
                                            data-toggle-passive-class="btn-default">
                                            <input type="radio" name="edit_status" value="1"> Active
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            window.actionEvents = {
                'click .edit-data': function (e, value, row, index) {
                    $('#edit_id').val(row.id);
                    $('#edit_role').val(row.role_id);
                    $("input[name=edit_status][value=1]").prop('checked', true);
                    if ($(row.status).text() == 'Deactive')
                        $("input[name=edit_status][value=0]").prop('checked', true);
                }
            };
        </script>
        <script type="text/javascript">
            function queryParams(p) {
                return {
                    limit: p.limit,
                    order: p.order,
                    offset: p.offset,
                    search: p.search
                };
            }
        </script>
    </body>

</html>