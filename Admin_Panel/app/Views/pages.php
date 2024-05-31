<!DOCTYPE html>
<html lang="en">

    <head>
        <title>Page | <?=($app_name) ? $app_name[0]->message : '' ?>
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
                                <h1>Create and Manage Pages</h1>
                            </div>
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
                                        <h3 class="card-title">Add Pages</h3>
                                    </div>
                                    <form action="<?= APP_URL ?>store_pages" role="form" id="insert_form" method="POST"
                                        enctype="multipart/form-data">
                                        <?= csrf_field() ?>
                                        <div class="card-body">
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <label>Title</label>
                                                    <input type="text" name="title" class="form-control"
                                                        placeholder="page title" required>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label>Slug</label>
                                                    <input type="text" name="slug" class="form-control"
                                                        placeholder="page slug">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <label>Meta Description</label>
                                                    <input type="text" name="meta_description" class="form-control"
                                                        placeholder="Meta Description">
                                                </div>
                                                <div class="col-sm-6">
                                                    <label>Meta Keywords</label>
                                                    <input type="text" name="meta_keywords" class="form-control"
                                                        placeholder="Meta Keywords">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-4">
                                                    <label>Language</label>
                                                    <select id="language" name="language" class="form-control" required>
                                                        <option value="1" selected disabled required>Select Language
                                                        </option>
                                                        <?php
                                                        foreach ($languages as $row) { ?>
                                                            <option value="<?php echo $row->id; ?>">
                                                                <?php echo $row->language; ?>
                                                            </option>
                                                        <?php }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-sm-4">
                                                <label>Page Icon <small class="text-danger">Only png, jpg, webp, gif and jpeg image allow</small></label>

                                                    <div class="custom-file">
                                                        <input name="file" type="file" accept="image/*"
                                                            class="custom-file-input" id="exampleInputFile1" required>
                                                        <label class="custom-file-label" id="image"
                                                            for="customFile">Choose file</label>
                                                    </div>
                                                    <p style="display:none" id="img_error_msg"
                                                        class="alert alert-danger"></p>
                                                </div>
                                                <div class="col-sm-2">
                                                    <label>Is Terms & Policy</label>
                                                    <div>
                                                        <input type="checkbox" id="is_termspolicy"
                                                            class="is_termspolicy" name="is_termspolicy"
                                                            data-bootstrap-switch data-off-color="danger"
                                                            data-on-color="success">
                                                        <input type="hidden" id="termspolicy_mode"
                                                            class="termspolicy_mode" name="termspolicy_mode" value="1">
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <label>Is Privacy Policy</label>
                                                    <div>
                                                        <input type="checkbox" id="is_privacypolicy"
                                                            class="is_privacypolicy" name="is_privacypolicy"
                                                            data-bootstrap-switch data-off-color="danger"
                                                            data-on-color="success">
                                                        <input type="hidden" id="privacypolicy_mode"
                                                            class="privacypolicy_mode" name="privacypolicy_mode"
                                                            value="1">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <label>Page Content</label>
                                                    <textarea id="page_content" name="page_content"
                                                        class="form-control"></textarea>
                                                </div>
                                            </div>
                                            <?php if ($this->session->getFlashdata('error')) { ?>
                                                <div class="col-sm-6 offset-2">
                                                    <p id="error_msg" class="alert alert-danger">
                                                        <?php echo $this->session->getFlashdata('error'); ?>
                                                    </p>
                                                </div>
                                            <?php } ?>
                                            <?php if ($this->session->getFlashdata('success')) { ?>
                                                <div class="col-sm-6 offset-2">
                                                    <p id="success_msg" class="alert alert-success">
                                                        <?php echo $this->session->getFlashdata('success'); ?>
                                                    </p>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="card-footer">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="card card-secondary">
                                    <div class="card-header">
                                        <h3 class="card-title">Pages <small> View / Update / Delete</small></h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <select id="filter_language" name="language" class="form-control">
                                                <option value="">Select Language</option>
                                                    <?php foreach ($languages as $row) : ?>
                                                        <option value="<?php echo $row->id; ?>">
                                                            <?php echo $row->language; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-2">
                                                <select id="filter_policy_type" name="policy_type" class="form-control">
                                                    <option value="">Select Policy Type</option>
                                                    <option value="terms_policy">Terms Policy</option>
                                                    <option value="privacy_policy">Privacy Policy</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-2">
                                                <select id="filter_status" name="status" class="form-control">
                                                    <option value="">Status</option>
                                                    <option value="1">Enable</option>
                                                    <option value="0">Disable</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-2">
                                                <button class='btn btn-primary btn-block' id='filter_btn'>Filter Data</button>
                                            </div>
                                        </div>
                                        <table aria-describedby="mydesc" class='table-striped' id='pages_list'
                                            data-toggle="table" data-url="<?= APP_URL . 'Table/pages' ?>"
                                            data-click-to-select="true" data-side-pagination="server"
                                            data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                                            data-search="true" data-toolbar="#toolbar" data-show-columns="true"
                                            data-show-refresh="true" data-fixed-columns="true" data-fixed-number="1"
                                            data-fixed-right-number="1" data-trim-on-search="false"
                                            data-mobile-responsive="true" data-sort-name="id" data-sort-order="desc"
                                            data-maintain-selected="true" data-export-types='["txt","excel"]'
                                            data-export-options='{ "fileName": "pages-list-<?= date('d-m-y') ?>" }'
                                            data-query-params="queryParams">
                                            <thead>
                                                <tr>
                                                    <th scope="col" data-field="id" data-sortable="true">ID</th>
                                                    <th scope="col" data-field="title" data-sortable="true">Title</th>
                                                    <th scope="col" data-field="slug" data-sortable="false">Slug</th>
                                                    <th scope="col" data-field="language" data-sortable="false">Language</th>
                                                    <th scope="col" data-field="image" data-sortable="false">Image</th>
                                                    <th scope="col" data-field="page_type" data-sortable="false">Page Type</th>
                                                    <th scope="col" data-field="is_policy" data-sortable="true">Policy</th>
                                                    <th scope="col" data-field="status" data-sortable="true">Status</th>
                                                    <th scope="col" data-field="operate" data-sortable="false" data-events="actionEvents">Operate</th>
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
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Edit Page</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="<?= APP_URL ?>update_pages" role="form" id="update_form" method="POST"
                            enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <input type='hidden' name="edit_id" id="edit_id" value='' />
                            <input type='hidden' name="image_url" id="image_url" value='' />
                            <div class="card-body">
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Title</label>
                                        <input type="text" name="title" id="edit_title" class="form-control"
                                            placeholder="page title" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label>Slug</label>
                                        <input type="text" name="slug" id="edit_slug" class="form-control"
                                            placeholder="page slug" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Meta Description</label>
                                        <input type="text" name="meta_description" id="edit_meta_description"
                                            class="form-control" placeholder="Meta Description">
                                    </div>
                                    <div class="col-sm-6">
                                        <label>Meta Keywords</label>
                                        <input type="text" name="meta_keywords" id="edit_meta_keywords"
                                            class="form-control" placeholder="Meta Keywords">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-4">
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
                                    <div class="col-md-4">
                                    <label>Page Icon</label>
                                        <div class="custom-file">
                                            <input name="file" type="file" accept="image/*" class="custom-file-input"
                                                id="exampleInputFile11">
                                            <label class="custom-file-label" id="image1" for="customFile">Choose
                                                file</label>
                                                <small class="text-danger">Only png, jpg, webp, gif and jpeg image allow</small>
                                        </div>
                                        <p style="display:none" id="img_error_msg1" class="alert alert-danger"></p>
                                    </div>
                                    <div class="col-sm-2">
                                        <label>Is Terms Policy</label>
                                        <div>
                                            <input type="checkbox" id="edit_is_termspolicy" name="is_termspolicy"
                                                class="is_termspolicy" data-off-color="danger" data-bootstrap-switch
                                                data-on-color="success" value="on" checked>
                                            <input type="hidden" id="edit_termspolicy_mode" class="termspolicy_mode"
                                                name="termspolicy_mode" value="0">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <label>Is Privacy Policy</label>
                                        <div>
                                            <input type="checkbox" id="edit_is_privacypolicy" class="is_privacypolicy"
                                                name="is_privacypolicy" data-bootstrap-switch data-off-color="danger"
                                                data-on-color="success" value="on">
                                            <input type="hidden" id="edit_privacypolicy_mode" class="privacypolicy_mode"
                                                name="privacypolicy_mode" value="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <label>Page Content</label>
                                        <textarea id="edit_page_content" name="page_content" class="form-control"
                                            required></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12">Status</label>
                                        <div id="status1" class="btn-group ">
                                            <label class="btn btn-success" data-toggle-class="btn-primary"
                                                data-toggle-passive-class="btn-default">
                                                <input class="status" type="radio" name="status" value="1" checked>
                                                Enabled
                                            </label>
                                            <label class="btn btn-danger" data-toggle-class="btn-primary"
                                                data-toggle-passive-class="btn-default">
                                                <input class="status" type="radio" name="status" value="0"> Disabled
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
        
        <!-- /.modal -->
        <?php base_url() . include 'footer.php'; ?>
        </div>
        <!-- ./wrapper -->
        <script type="text/javascript">
            $('#filter_btn').on('click', function (e) {
                $('#pages_list').bootstrapTable('refresh');
            });
            function queryParams(p) {
                return {
                    limit: p.limit,
                    order: p.order,
                    offset: p.offset,
                    search: p.search,
                    "language": $('#filter_language').val(),
                    "policy_type": $('#filter_policy_type').val(),
                    "status": $('#filter_status').val(),
                };
            }
        </script>
        <script type="text/javascript">
            window.actionEvents = {
                'click .edit-data': function (e, value, row, index) {
                    console.log(row);
                    $('#edit_id').val(row.id);
                    $("#edit_title").val(row.title);
                    $("#edit_slug").val(row.slug);
                    $("#image_url").val(row.image_url);
                    $("#edit_meta_description").val(row.meta_description);
                    $("#edit_meta_keywords").val(row.meta_keywords);
                    $("#edit_language").val(row.language_id);
                    //edit terms policy
                    if (row.is_termspolicy == '1') {
                        setTimeout(function() { 
                            $("#edit_is_termspolicy").bootstrapSwitch('state', true);
                        }, 600);
                    }
                    else {
                        $("#edit_is_termspolicy").bootstrapSwitch('state', false);
                    }
                    //edit privacy_policy
                    if (row.is_privacypolicy == '1') {
                        setTimeout(function() { 
                            $("#edit_is_privacypolicy").bootstrapSwitch('state', true);
                        }, 600);
                    }
                    else {
                        $("#edit_is_privacypolicy").bootstrapSwitch('state', false);
                    }
                    //edit page content
                    if (row.page_content) {
                        tinyMCE.get('edit_page_content').setContent(row.page_content);
                    } else {
                        tinyMCE.get('edit_page_content').setContent('');
                    }
                    //edit status
                    $("input[name=status][value=1]").prop('checked', true);
                    if ($(row.status).text() == 'Disable')
                        $("input[name=status][value=0]").prop('checked', true);
                }
            };
        </script>
        <script>
            $(document).on('click', '.delete-data', function () {
                if (confirm('Are you sure? Want to delete pages?')) {
                    var base_url = "<?= APP_URL ?>";
                    var id = $(this).data("id");
                    var is_custom = $(this).data("iscustom");
                    $.ajax({
                        url: base_url + 'delete_pages',
                        type: "POST",
                        dataType: "json",
                        data: { id: id, is_custom: is_custom },
                        success: function (result) {
                            if (result) {
                                setTimeout(function () {
                                    window.location.reload();
                                }, 1000);
                            }
                        },
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
        <script type="text/javascript">
            $(document).ready(function () {
                $('#update_form').validate({
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
        <script type="text/javascript">
            $(document).ready(function () {
                $(document).on('focusin', function (e) {
                    if ($(e.target).closest(".tox-tinymce-aux, .moxman-window, .tam-assetmanager-root").length) {
                        e.stopImmediatePropagation();
                    }
                });
                var base_url = "<?= APP_URL ?>";
                tinymce.init({
                    selector: "#page_content, #edit_page_content",
                    height: 300,
                    plugins: [
                        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                        'insertdatetime', 'media', 'table', 'wordcount'
                    ],
                    toolbar: 'undo redo | blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify bullist numlist outdent indent removeformat link image media',
                    image_uploadtab: false,
                    images_upload_url: base_url + "upload_pages_img",
                    relative_urls: false,
                    remove_script_host: false,
                    file_picker_types: 'image media',
                    media_poster: false,
                    media_alt_source: false,
                    file_picker_callback: function (callback, value, meta) {
                        if (meta.filetype == "media" || meta.filetype == "image") {
                            const input = document.createElement('input');
                            input.setAttribute('type', 'file');
                            input.setAttribute('accept', 'image/* audio/* video/*');
                            input.addEventListener('change', (e) => {
                                const file = e.target.files[0];
                                var reader = new FileReader();
                                var fd = new FormData();
                                var files = file;
                                fd.append("file", files);
                                fd.append('filetype', meta.filetype);
                                var filename = "";
                                // AJAX
                                jQuery.ajax({
                                    url: base_url + "upload_pages_img",
                                    type: "post",
                                    data: fd,
                                    contentType: false,
                                    processData: false,
                                    async: false,
                                    success: function (response) {
                                        filename = response;
                                    }
                                });
                                reader.onload = function (e) {
                                    callback("public/images/pages/" + filename);
                                };
                                console.log(reader);
                                reader.readAsDataURL(file);
                            });
                            input.click();
                        }
                    },
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
        <script type="text/javascript">
            /* on change of termspolicy_mode mode btn - switchery js */
            var is_termspolicy = document.querySelector('#is_termspolicy');
            is_termspolicy.onchange = function () {
                if (is_termspolicy.checked) {
                    $('#termspolicy_mode').val(1);
                    $('#privacypolicy_mode').val(0);
                    $('#is_privacypolicy').bootstrapSwitch('state', false);
                } else {
                    $('#termspolicy_mode').val(0);
                }
            };
            var is_privacypolicy = document.querySelector('#is_privacypolicy');
            is_privacypolicy.onchange = function () {
                if (is_privacypolicy.checked) {
                    $('#privacypolicy_mode').val(1);
                    $('#termspolicy_mode').val(0);
                    $('#is_termspolicy').bootstrapSwitch('state', false);
                } else {
                    $('#privacypolicy_mode').val(0);
                }
            };
        </script>
        <script type="text/javascript">
            /* on change of termspolicy_mode mode btn - switchery js */
            var edit_is_termspolicy = document.querySelector('#edit_is_termspolicy');
            edit_is_termspolicy.onchange = function () {
                if (edit_is_termspolicy.checked) {
                    $('#edit_termspolicy_mode').val(1);
                    $('#edit_privacypolicy_mode').val(0);
                    $('#edit_is_privacypolicy').bootstrapSwitch('state', false);
                } else {
                    $('#edit_termspolicy_mode').val(0);
                }
            };
            var edit_is_privacypolicy = document.querySelector('#edit_is_privacypolicy');
            edit_is_privacypolicy.onchange = function () {
                if (edit_is_privacypolicy.checked) {
                    $('#edit_privacypolicy_mode').val(1);
                    $('#edit_termspolicy_mode').val(0);
                    $('#edit_is_termspolicy').bootstrapSwitch('state', false);
                } else {
                    $('#edit_privacypolicy_mode').val(0);
                }
            };
        </script>
        <script type="text/javascript">
            $("input[data-bootstrap-switch]").each(function () {
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
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
    </body>

</html>