<!DOCTYPE html>
<html lang="en">

    <head>
        <title>Tag | <?=($app_name) ? $app_name[0]->message : '' ?>
        </title>
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
                            <div class="col-sm-6">
                                <h1>Create and Manage Tag</h1>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card card-secondary">
                                    <div class="card-header">
                                        <h3 class="card-title">Add Tag</h3>
                                    </div>
                                    <form action="<?= APP_URL ?>store_tag" role="form" id="insert_form" method="POST"
                                        enctype="multipart/form-data">
                                        <?= csrf_field() ?>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label>Name</label>
                                                <input type="text" name="name" class="form-control"
                                                    placeholder="tag name" required>
                                            </div>
                                            <!-- //-------- Language Added---------- -->
                                            <div class="form-group">
                                                <label>Language</label>
                                                <select id="language" name="language" class="form-control" required>
                                                    <option value="1" selected disabled>Select Language</option>
                                                    <?php
                                                    foreach ($languages as $row) { ?>
                                                        <option value="<?php echo $row->id; ?>">
                                                            <?php echo $row->language; ?>
                                                        </option>
                                                    <?php }
                                                    ?>
                                                </select>
                                            </div>
                                            <!-- //------------------------------------- -->
                                            <?php
                                            if ($this->session->getFlashdata('error')) {
                                                ?>
                                                <div class="row form-group">
                                                    <p id="error_msg" class="alert alert-danger">
                                                        <?php echo $this->session->getFlashdata('error'); ?>
                                                    </p>
                                                </div>
                                            <?php } ?>
                                            <?php if ($this->session->getFlashdata('success')) { ?>
                                                <p id="success_msg" class="alert alert-success">
                                                    <?php echo $this->session->getFlashdata('success'); ?>
                                                </p>
                                            <?php } ?>
                                        </div>
                                        <div class="card-footer">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card card-secondary">
                                    <div class="card-header">
                                        <h3 class="card-title">Tags <small>View / Update / Delete</small></h3>
                                    </div>
                                    <div class="card-body">
                                        <table aria-describedby="mydesc" class='table-striped' id='tag_list'
                                            data-toggle="table" data-url="<?= APP_URL . 'Table/tag' ?>"
                                            data-click-to-select="true" data-side-pagination="server"
                                            data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                                            data-search="true" data-trim-on-search="false" data-show-columns="true"
                                            data-show-refresh="true" data-fixed-columns="true" data-fixed-number="1"
                                            data-fixed-right-number="1" data-sort-name="id" data-sort-order="desc"
                                            data-mobile-responsive="true" data-maintain-selected="true"
                                            data-export-types='["txt","excel"]'
                                            data-export-options='{ "fileName": "tag-list-<?= date('d-m-y') ?>" }'
                                            data-query-params="queryParams">
                                            <thead>
                                                <tr>
                                                    <th scope="col" data-field="id" data-sortable="true">ID</th>
                                                    <th scope="col" data-field="tag_name" data-sortable="true">Name</th>
                                                    <th scope="col" data-field="language" data-sortable="false">Language
                                                    </th>
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
            <div class="modal fade" id="editDataModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Edit Tag</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="<?= APP_URL ?>update_tag" role="form" id="update_form" method="POST"
                            enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <input type='hidden' name="edit_id" id="edit_id" value='' />
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control" id="edit_name"
                                        placeholder="tag name" required>
                                </div>
                                <!-- //------------Edit language  -->
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <label>Language</label>
                                        <select id="edit_language" name="edit_language" class="form-control" required>
                                            <option value="">Select language</option>
                                            <?php
                                            foreach ($languages as $row) { ?>
                                                <option value="<?php echo $row->id; ?>">
                                                    <?php echo $row->language; ?>
                                                </option>
                                            <?php }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <!-- //------------Edit language  -->
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php base_url() . include 'footer.php'; ?>
        </div>
        <!-- ./wrapper -->
        <script type="text/javascript">
            window.actionEvents = {
                'click .edit-data': function (e, value, row, index) {
                    $('#edit_id').val(row.id);
                    $("#edit_name").val(row.tag_name);
                    $("#edit_language").val(row.language_id);
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
        <script type="text/javascript">
            $(document).on('click', '.delete-data', function () {
                if (confirm('Are you sure? Want to delete Tag? All related data will also be deleted')) {
                    var base_url = "<?= APP_URL ?>";
                    var id = $(this).data("id");
                    $.ajax({
                        url: base_url + 'delete_tag',
                        type: "POST",
                        dataType: "json",
                        data: { id: id },
                        success: function (result) {
                            if (result) {
                                $('#tag_list').bootstrapTable('refresh');
                            } else {
                                alert('Tag could not be deleted');
                            }
                        }
                    });
                }
            });
        </script>
        <script type="text/javascript">
            $(document).ready(function () {
                $('#insert_form').validate({
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
        <script type="text/javascript">
            $(document).ready(function () {
                $('#update_form').validate({
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