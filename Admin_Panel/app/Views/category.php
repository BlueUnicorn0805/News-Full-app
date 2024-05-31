<!DOCTYPE html>
<html lang="en">

    <head>
        <title>Category | <?=($app_name) ? $app_name[0]->message : '' ?>
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
                            <div class="col-sm-12">
                                <h1 class="d-inline-block">Create and Manage Category</h1>
                                <?php if (is_category_enabled() != 1) { ?>
                                    <label class="badge badge-danger">Disabled</label>
                                <?php } ?>
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
                                        <h3 class="card-title">Add Category</h3>
                                    </div>
                                    <form action="<?= APP_URL ?>store_category" role="form" id="insert_form"
                                        method="POST" enctype="multipart/form-data">
                                        <?= csrf_field() ?>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label>Name</label>
                                                <input type="text" name="name" class="form-control"
                                                    placeholder="category name" required>
                                            </div>
                                            <!-- //-------- Language Added---------- -->
                                            <div class="form-group row">
                                                <div class="col-sm-12">
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
                                            </div>
                                            <!-- //------------------------------------- -->
                                            <div class="form-group">
                                            <label>Image <small class="text-danger">Only png, jpg, webp, gif and jpeg image allow</small></label>
                                                <div class="custom-file">
                                                    <input name="file" type="file" accept="image/*"
                                                        class="custom-file-input" id="exampleInputFile1" required> 
                                                    <label class="custom-file-label" for="customFile">Choose
                                                        file</label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <p style="display: none" id="img_error_msg" class="alert alert-danger">
                                                </p>
                                            </div>
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
                                        <h3 class="card-title">Categories <small>View / Update / Delete</small></h3>
                                    </div>
                                    <div class="card-body">
                                        <table aria-describedby="mydesc" class='table-striped' id='category_list'
                                            data-toggle="table" data-url="<?= APP_URL . 'Table/category' ?>"
                                            data-click-to-select="true" data-side-pagination="server"
                                            data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                                            data-search="true" data-show-columns="true" data-show-refresh="true"
                                            data-fixed-columns="true" data-fixed-number="1" data-fixed-right-number="1"
                                            data-trim-on-search="false" data-sort-name="id" data-sort-order="desc"
                                            data-mobile-responsive="true" data-maintain-selected="true"
                                            data-export-types='["txt","excel"]'
                                            data-export-options='{ "fileName": "category-list-<?= date('d-m-y') ?>" }'
                                            data-query-params="queryParams">
                                            <thead>
                                                <tr>
                                                    <th scope="col" data-field="id" data-sortable="true">ID</th>
                                                    <th scope="col" data-field="category_name" data-sortable="true">Name
                                                    </th>
                                                    <th scope="col" data-field="image" data-sortable="false">Image</th>
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
                            <h4 class="modal-title">Edit Category</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="<?= APP_URL ?>update_category" role="form" id="update_form" method="POST"
                            enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <input type='hidden' name="edit_id" id="edit_id" value='' />
                            <input type='hidden' name="image_url" id="image_url" value='' />
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control" id="edit_name"
                                        placeholder="category name" required>
                                </div>
                                <!-- //------------Edit language  -->
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Language</label>
                                        <select id="edit_language" name="edit_language" class="form-control" required>
                                            <option value="">Select language</option>
                                            <?php
                                            foreach ($languages as $row) { ?>
                                                <option value="<?php echo $row->id; ?>">
                                                    <?php echo $row->language; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <!-- //------------Edit language  -->
                                <div class="form-group">
                                <label>Image <small class="text-danger">Only png, jpg, webp, gif and jpeg image allow</small></label>
                                    <div class="custom-file">
                                        <input name="file" type="file" accept="image/*" class="custom-file-input"
                                            id="exampleInputFile11">
                                        <label class="custom-file-label" for="customFile">Choose file</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <p style="display: none" id="img_error_msg1" class="alert alert-danger"></p>
                                </div>
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
                    $("#image_url").val(row.image_url);
                    $("#edit_name").val(row.category_name);
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
                if (confirm('Are you sure? Want to delete Category? All related data will also be deleted')) {
                    var base_url = "<?= APP_URL ?>";
                    var id = $(this).data("id");
                    var image = $(this).data("image");
                    $.ajax({
                        url: base_url + 'delete_category',
                        type: "POST",
                        dataType: "json",
                        data: { id: id, image_url: image },
                        success: function (result) {
                            if (result) {
                                $('#category_list').bootstrapTable('refresh');
                            } else {
                                alert('Category could not be deleted');
                            }
                        }
                    });
                }
            });
        </script>
        <script type="text/javascript">
            var _URL = window.URL || window.webkitURL;
            $("#exampleInputFile1").change(function (e) {
                var file, img;
                if ((file = this.files[0])) {
                    img = new Image();
                    img.onerror = function () {
                        $("#exampleInputFile1").val('');
                        $('.custom-file-label').html('');
                        $('#img_error_msg').html("Invalid Image Type");
                        $('#img_error_msg').show().delay(3000).fadeOut();
                    };
                    img.src = _URL.createObjectURL(file);
                }
            });
            $("#exampleInputFile11").change(function (e) {
                var file, img;
                if ((file = this.files[0])) {
                    img = new Image();
                    img.onerror = function () {
                        $("#exampleInputFile11").val('')
                        $('.custom-file-label').html('');
                        $('#img_error_msg1').html("Invalid Image Type");
                        $('#img_error_msg1').show().delay(3000).fadeOut();
                    };
                    img.src = _URL.createObjectURL(file);
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