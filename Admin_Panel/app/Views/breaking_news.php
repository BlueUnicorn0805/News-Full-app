<!DOCTYPE html>
<html lang="en">

    <head>
        <title>Breaking News | <?=($app_name) ? $app_name[0]->message : '' ?>
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
                                <h1 class="d-inline-block">Create and Manage Breaking News</h1>
                                <?php if (is_breaking_news_enabled() != 1) { ?>
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
                            <div class="col-md-12">
                                <div class="card card-secondary">
                                    <div class="card-header">
                                        <h3 class="card-title">Add Breaking News</h3>
                                    </div>
                                    <form action="<?= APP_URL ?>store_breaking_news" role="form" id="insert_form"
                                        method="POST" enctype="multipart/form-data">
                                        <?= csrf_field() ?>
                                        <div class="card-body">
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <label>Title</label>
                                                    <input type="text" name="title" class="form-control"
                                                        placeholder="breaking news" required>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label>Image <small class="text-danger">Only png, jpg, webp, gif and jpeg image
                                                            allow</small></label>
                                                    <div class="custom-file">
                                                        <input name="file" type="file" accept="image/*"
                                                            class="custom-file-input" id="exampleInputFile1" required>
                                                        <label class="custom-file-label" id="image"
                                                            for="customFile">Choose file</label>
                                                    </div>
                                                    <p style="display:none" id="img_error_msg"
                                                        class="alert alert-danger"></p>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <label>Content Type</label>
                                                    <select name="content_type" id="content_type" class="form-control"
                                                        required>
                                                        <option value="standard_post" selected>Standard Post</option>
                                                        <option value="video_youtube">Video (YouTube)</option>
                                                        <option value="video_other">Video (Other Url)</option>
                                                        <option value="video_upload">Video (Upload)</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-6 video_youtube">
                                                    <label>Youtube URL</label>
                                                    <input type="url" name="youtube_url" class="form-control" required>
                                                </div>
                                                <div class="col-sm-6 video_other">
                                                    <label>Other URL</label>
                                                    <input type="url" name="other_url" class="form-control" required>
                                                </div>
                                                <div class="col-sm-6 video_upload">
                                                    <label>Video Upload</label>
                                                    <div class="custom-file">
                                                        <input name="video_file" type="file" class="custom-file-input"
                                                            id="exampleVideoInputFile1" required>
                                                        <label class="custom-file-label" for="customFile">Choose
                                                            file</label>
                                                    </div>
                                                    <p style="display:none" id="video_error_msg"
                                                        class="alert alert-danger"></p>
                                                </div>
                                            </div>
                                            <!-- //-------- Language Added---------- -->
                                            <div class="form-group row">
                                                <div class="col-sm-6">
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
                                                <label>Description</label>
                                                <textarea id="des" name="des" class="form-control"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <?php if ($this->session->getFlashdata('error')) { ?>
                                                    <p id="error_msg" class="alert alert-danger">
                                                        <?php echo $this->session->getFlashdata('error'); ?>
                                                    </p>
                                                <?php } ?>
                                                <?php if ($this->session->getFlashdata('success')) { ?>
                                                    <p id="success_msg" class="alert alert-success">
                                                        <?php echo $this->session->getFlashdata('success'); ?>
                                                    </p>
                                                <?php } ?>
                                            </div>
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
                                        <h3 class="card-title">Breaking News <small> View / Update / Delete</small></h3>
                                    </div>
                                    <div class="card-body">
                                        <table aria-describedby="mydesc" class='table-striped' id='breaking_news_list'
                                            data-toggle="table" data-url="<?= APP_URL . 'Table/breaking_news' ?>"
                                            data-click-to-select="true" data-side-pagination="server"
                                            data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                                            data-search="true" data-show-columns="true" data-show-refresh="true"
                                            data-fixed-columns="true" data-fixed-number="1" data-fixed-right-number="1"
                                            data-trim-on-search="false" data-sort-name="id" data-sort-order="desc"
                                            data-mobile-responsive="true" data-maintain-selected="true"
                                            data-export-types='["txt","excel"]'
                                            data-export-options='{ "fileName": "breaking-news-list-<?= date('d-m-y') ?>" }'
                                            data-query-params="queryParams">
                                            <thead>
                                                <tr>
                                                    <th scope="col" data-field="id" data-sortable="true">ID</th>
                                                    <th scope="col" data-field="short_title" data-sortable="false">Title</th>
                                                    <th scope="col" data-field="content_type" data-sortable="false">
                                                        Content
                                                        Type</th>
                                                    <th scope="col" data-field="image" data-sortable="false">Image</th>
                                                    <th scope="col" data-field="short_description" data-sortable="false">
                                                        Description</th>
                                                    <th scope="col" data-field="language" data-sortable="false">Language
                                                    </th>
                                                    <th scope="col" data-field="views" data-sortable="false">Views
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
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Edit Breaking News</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="<?= APP_URL ?>update_breaking_news" role="form" id="update_form" method="POST"
                            enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <input type='hidden' name="edit_id" id="edit_id" value='' />
                            <input type='hidden' name="image_url" id="image_url" value='' />
                            <input type='hidden' name="video_url" id="video_url" value='' />
                            <div class="modal-body">
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Title</label>
                                        <input type="text" name="title" class="form-control" id="edit_title"
                                            placeholder="breaking news" required>
                                    </div>
                                    <div class="col-sm-6">
                                    <label>Image <small class="text-danger">Only png, jpg, webp, gif and jpeg image allow</small></label>
                                        <div class="custom-file">
                                            <input name="file" type="file" accept="image/*" class="custom-file-input"
                                                id="exampleInputFile11">
                                            <label class="custom-file-label" id="image1" for="customFile">Choose
                                                file</label>
                                        </div>
                                        <p style="display:none" id="img_error_msg1" class="alert alert-danger"></p>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Content Type</label>
                                        <select name="content_type" id="edit_content_type" class="form-control"
                                            required>
                                            <option value="standard_post" selected>Standard Post</option>
                                            <option value="video_youtube">Video (YouTube)</option>
                                            <option value="video_other">Video (Other Url)</option>
                                            <option value="video_upload">Video (Upload)</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6 evideo_youtube">
                                        <label>Youtube URL</label>
                                        <input type="url" name="youtube_url" id="youtube_url" class="form-control"
                                            required>
                                    </div>
                                    <div class="col-sm-6 evideo_other">
                                        <label>Other URL</label>
                                        <input type="url" name="other_url" id="other_url" class="form-control" required>
                                    </div>
                                    <div class="col-sm-6 evideo_upload">
                                        <label>Video Upload</label>
                                        <div class="custom-file">
                                            <input name="video_file" type="file" class="custom-file-input"
                                                id="exampleVideoInputFile1">
                                            <label class="custom-file-label" id="video" for="customFile">Choose
                                                file</label>
                                        </div>
                                        <p style="display:none" id="video_error_msg" class="alert alert-danger"></p>
                                    </div>
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
                                            <?php }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <!-- //------------Edit language  -->
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea id="edit_des" name="des" class="form-control"></textarea>
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
                    console.log(row);
                    $('#edit_id').val(row.id);
                    $("#image_url").val(row.image_url);
                    $("#edit_title").val(row.title);
                    $("#edit_language").val(row.language_id);
                    var con_value = row.content_value;
                    if (row.content == "") {
                        $("#edit_content_type").val("standard_post");
                    } else {
                        $("#edit_content_type").val(row.content);
                    }
                    if (row.content == "standard_post" || row.content == "") {
                        $('.evideo_youtube').hide();
                        $('.evideo_other').hide();
                        $('.evideo_upload').hide();
                    }
                    if (row.content == "video_youtube") {
                        $('.evideo_youtube').show();
                        $('#youtube_url').val(con_value);
                        $('.evideo_other').hide();
                        $('.evideo_upload').hide();
                    }
                    if (row.content == "video_other") {
                        $('.evideo_youtube').hide();
                        $('#other_url').val(con_value);
                        $('.evideo_other').show();
                        $('.evideo_upload').hide();
                    }
                    if (row.content == "video_upload") {
                        $('.evideo_youtube').hide();
                        $('.evideo_other').hide();
                        $('.evideo_upload').show();
                        $("#video_url").val('public/images/news_video/' + con_value);
                    }
                    var des1 = tinyMCE.get('edit_des').setContent(row.description);
                    $('#edit_des').val(des1);
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
                if (confirm('Are you sure? Want to delete Breaking News? ')) {
                    var base_url = "<?= APP_URL ?>";
                    var id = $(this).data("id");
                    var image = $(this).data("image");
                    var con_value = $(this).data("cvalue");
                    $.ajax({
                        url: base_url + 'delete_breaking_news',
                        type: "POST",
                        dataType: "json",
                        data: { id: id, image_url: image, con_value: con_value },
                        success: function (result) {
                            if (result) {
                                setTimeout(function () {
                                    window.location.reload();
                                }, 1000);
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
                        $('#exampleInputFile1').val('');
                        $('#image').html('');
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
                        $('#exampleInputFile11').val('');
                        $('#image1').html('');
                        $('#img_error_msg1').html("Invalid Image Type");
                        $('#img_error_msg1').show().delay(3000).fadeOut();
                    };
                    img.src = _URL.createObjectURL(file);
                }
            });
            $("#exampleVideoInputFile1").change(function (e) {
                var fileInput = document.getElementById('exampleVideoInputFile1');
                var filePath = fileInput.value;
                var allowedExtensions = /(\.mp4|\.m4a|\.mkv|\.avi)$/i;
                if (!allowedExtensions.exec(filePath)) {
                    $('#exampleVideoInputFile1').val('');
                    $('#video').html('');
                    $('#video_error_msg').html("Invalid Type");
                    $('#video_error_msg').show().delay(3000).fadeOut();
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
        <script>
            $(document).ready(function () {
                $(document).on('focusin', function (e) {
                    if ($(e.target).closest(".tox-tinymce-aux, .moxman-window, .tam-assetmanager-root").length) {
                        e.stopImmediatePropagation();
                    }
                });
                tinymce.init({
                    selector: "#des, #edit_des",
                    height: 250,
                    plugins: [
                        'advlist', 'autolink', 'lists', 'link', 'charmap', 'preview',
                        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                        'insertdatetime', 'table', 'wordcount'
                    ],
                    toolbar: 'undo redo | blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify bullist numlist outdent indent removeformat link',
                    setup: function (editor) {
                        editor.on("change keyup", function (e) {
                            editor.save(); // updates this instance's textarea
                            $(editor.getElement()).trigger('change'); // for garlic to detect change
                        });
                    }
                });
            });
        </script>
        <script type="text/javascript">
            $('.video_youtube').hide();
            $('.video_other').hide();
            $('.video_upload').hide();
            $(document).ready(function (e) {
                $("#content_type").change(function () {
                    var type = $("#content_type").val();
                    if (type == "standard_post") {
                        $('.video_youtube').hide();
                        $('.video_other').hide();
                        $('.video_upload').hide();
                    }
                    if (type == "video_youtube") {
                        $('.video_youtube').show();
                        $('.video_other').hide();
                        $('.video_upload').hide();
                    }
                    if (type == "video_other") {
                        $('.video_youtube').hide();
                        $('.video_other').show();
                        $('.video_upload').hide();
                    }
                    if (type == "video_upload") {
                        $('.video_youtube').hide();
                        $('.video_other').hide();
                        $('.video_upload').show();
                    }
                });
                $("#edit_content_type").change(function () {
                    var type = $("#edit_content_type").val();
                    if (type == "standard_post") {
                        $('.evideo_youtube').hide();
                        $('.evideo_other').hide();
                        $('.evideo_upload').hide();
                    }
                    if (type == "video_youtube") {
                        $('.evideo_youtube').show();
                        $('.evideo_other').hide();
                        $('.evideo_upload').hide();
                    }
                    if (type == "video_other") {
                        $('.evideo_youtube').hide();
                        $('.evideo_other').show();
                        $('.evideo_upload').hide();
                    }
                    if (type == "video_upload") {
                        $('.evideo_youtube').hide();
                        $('.evideo_other').hide();
                        $('.evideo_upload').show();
                    }
                });
            });
        </script>
    </body>

</html>