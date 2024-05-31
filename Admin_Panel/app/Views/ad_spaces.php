<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Ad Spaces | <?= ($app_name) ? $app_name[0]->message : '' ?></title>        
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
                    </div><!-- /.container-fluid -->
                </section>
                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-secondary">
                                    <div class="card-header">
                                        <h3 class="card-title">Ad Spaces</h3>                                       
                                    </div>
                                    <form action="<?= APP_URL ?>store_ad_spaces" role="form" id="insert_form"
                                        method="POST" enctype="multipart/form-data">
                                        <?= csrf_field() ?>
                                        <div class="card-body">
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <label>Language</label>
                                                    <select id="language_id" name="language_id" class="form-control language_id" required>
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
                                                <div class="col-sm-6">
                                                    <label>Select Ad Space</label>
                                                    <select id="ad_spaces" name="ad_space" class="form-control ad_spaces" required>
                                                        <option value="">Select Ad Space</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
											<div class="col-sm-4">
                                                <label>Ad Image <small class="text-danger">( For App size 800 * 215 )</small></label>
                                                <div class="custom-file">
                                                    <input name="ad_image" type="file" accept="image/*"
                                                        class="custom-file-input" id="exampleInputFile1" required>
                                                    <label class="custom-file-label" for="customFile">Choose
                                                        file</label>
                                                        <small class="text-danger">Only png, jpg, webp, gif and jpeg image allow</small>
                                                </div>
                                                <div class="form-group">
                                                    <p style="display: none" id="img_error_msg" class="alert alert-danger">
                                                    </p>
                                                </div>
											</div>
                                            <div class="col-sm-4">
                                                <label> Web Ad Image <small class="text-danger">( For Web size 1920 * 160 )</small></label>
                                                <div class="custom-file">
                                                    <input name="web_ad_image" type="file" accept="image/*"
                                                        class="custom-file-input" id="web_exampleInputFile1" required>
                                                    <label class="custom-file-label" for="customFile">Choose
                                                        file</label>
                                                        <small class="text-danger">Only png, jpg, webp, gif and jpeg image allow</small>
                                                </div>
                                                <div class="form-group">
                                                    <p style="display: none" id="web_img_error_msg" class="alert alert-danger">
                                                    </p>
                                                </div>
											</div>
											<div class="col-sm-4">
                                                <label>Url</label>
                                                <div class="custom-file">
                                                    <input name="ad_url" type="url" 
                                                        class="form-control" id="ad_url" required>
                                                </div>
											</div>
												
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
							<div class="col-md-12">
                                <div class="card card-secondary">
                                    <div class="card-header">
                                        <h3 class="card-title">Ad Spaces <small> View / Update / Delete</small></h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-1">
                                            <p id="delete_msg" style="display:none;" class="alert alert-success"></p>
                                        </div>
                                        <?php if (is_category_enabled() == 1) { ?>
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
                                                
                                                <div class="col-sm-1">
                                                    <select id="filter_status" name="status" class="form-control">
                                                        <option value="">Status</option>
                                                        <option value="1">Enable</option>
                                                        <option value="0">Disable</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-2">
                                                    <button class='btn btn-primary btn-block' id='filter_btn'>Filter
                                                        Data</button>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <table aria-describedby="mydesc" class='table-striped' id='ad_spaces_list'
                                            data-toggle="table" data-url="<?= APP_URL . 'Table/ad_spaces' ?>"
                                            data-click-to-select="true" data-side-pagination="server"
                                            data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                                            data-search="true" data-toolbar="#toolbar" data-show-columns="true"
                                            data-show-refresh="true" data-fixed-columns="true" data-fixed-number="1"
                                            data-fixed-right-number="1" data-trim-on-search="false"
                                            data-mobile-responsive="true" data-sort-name="id" data-sort-order="desc"
                                            data-maintain-selected="true" data-export-types='["txt","excel"]'
                                            data-export-options='{ "fileName": "ad-spaces-list-<?= date('d-m-y') ?>" }'
                                            data-query-params="queryParams">
                                            <thead>
                                                <tr>
                                                    <th scope="col" data-field="id" data-sortable="true">ID</th>
                                                    <th scope="col" data-field="date" data-sortable="true">Date</th>
                                                    <th scope="col" data-field="ad_space" data-sortable="false">Ad Space</th>
                                                    <th scope="col" data-field="ad_featured_section" data-sortable="false">Feature Section</th>
                                                    <th scope="col" data-field="ad_language" data-sortable="false">Language</th>
                                                    <th scope="col" data-field="ad_image" data-sortable="false">Image</th>
                                                    <th scope="col" data-field="web_ad_image" data-sortable="false">Web Image</th>
                                                    <th scope="col" data-field="ad_url" data-sortable="true">URL</th>
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
                            <h4 class="modal-title">Edit Ad Space</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="<?= APP_URL ?>update_ad_spaces" role="form" id="update_form" method="POST"
                            enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <input type='hidden' name="edit_id" id="edit_id" value='' />
                            <input type='hidden' name="ad_image_url" id="ad_image_url" value='' />
                            <input type='hidden' name="web_ad_image_url" id="web_ad_image_url" value='' />
                            <div class="card-body">
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Language</label>
                                        <select id="edit_language_id" name="language_id" class="form-control language_id" required>
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
                                    <div class="col-sm-6">
                                        <label>Select Ad Space</label>
                                        <select id="edit_ad_spaces" name="ad_space" class="form-control ad_spaces" required>
                                            <option value="<?php echo $row->id; ?>">Header</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                <div class="col-sm-4">
                                    <label>Ad Image <small class="text-danger">( size 806 * 218 )</small></label>
                                    <div class="custom-file">
                                        <input name="ad_image" type="file" accept="image/*"
                                            class="custom-file-input" id="exampleInputFile11">
                                        <label class="custom-file-label" for="customFile">Choose
                                            file</label>
                                            <small class="text-danger">Only png, jpg, webp, gif and jpeg image
                                            allow</small>
                                    </div>
                                    <p style="display: none" id="img_error_msg1" class="alert alert-danger">
                                        </p>
                                </div>
                                <div class="col-sm-4">
                                    <label>Web Ad Image <small class="text-danger">( size 1920 * 160 )</small></label>
                                    <div class="custom-file">
                                        <input name="web_ad_image" type="file" accept="image/*"
                                            class="custom-file-input" id="exampleInputFile11">
                                        <label class="custom-file-label" for="customFile">Choose
                                            file</label>
                                            <small class="text-danger">Only png, jpg, webp, gif and jpeg image
                                            allow</small>
                                    </div>
                                    <p style="display: none" id="img_error_msg1" class="alert alert-danger">
                                        </p>
                                </div>
                                <div class="col-sm-4">
                                    <label>Url</label>
                                    <div class="custom-file">
                                        <input id="edit_ad_url" type="url" 
                                            class="form-control" name="ad_url" required>
                                    </div>
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
                $('#ad_spaces_list').bootstrapTable('refresh');
            });
            function queryParams(p) {
                return {
                    limit: p.limit,
                    order: p.order,
                    offset: p.offset,
                    search: p.search,
                    "language": $('#filter_language').val(),
                    "status": $('#filter_status').val(),
                };
            }
        </script>
        <script type="text/javascript">
            window.actionEvents = {
                'click .edit-data': function (e, value, row, index) {
                    console.log(row);
                    $('#edit_id').val(row.id);
                    $("#edit_language_id").val(row.language_id);
                    
                    $("#ad_image_url").val(row.ad_image_url);
                    $("#web_ad_image_url").val(row.web_ad_image_url);
                    $("#edit_ad_url").val(row.ad_url);
                   
                    //select ad space based on selected language
                    $.ajax({
                    url: base_url + 'get_featured_sections_by_language',
                    type: "POST",
                    data: { language_id: row.language_id },
                    beforeSend: function () {
                        $('#edit_ad_spaces').html('Please wait..');
                    },
                    success: function (result) {
                        $('#edit_ad_spaces').html(result);
                        $("#edit_ad_spaces").val(row.ad_space);
                    }
                });
                    //edit status
                    $("input[name=status][value=1]").prop('checked', true);
                    if ($(row.status).text() == 'Disable')
                        $("input[name=status][value=0]").prop('checked', true);
                }
            };
        </script>
        <script>
            var base_url = "<?= APP_URL ?>";
            $('.language_id').on('change', function (e) {
                var language_id = $(this).val();
                console.log(language_id);
                $.ajax({
                    url: base_url + 'get_featured_sections_by_language',
                    type: "POST",
                    data: { language_id: language_id },
                    beforeSend: function () {
                        $('.ad_spaces').html('Please wait..');
                    },
                    success: function (result) {
                        $('.ad_spaces').html(result);
                    }
                });
            });
        </script>
        <script>
            $(document).on('click', '.delete-data', function () {
                if (confirm('Are you sure? Want to delete Ad Space?')) {
                    var base_url = "<?= APP_URL ?>";
                    var id = $(this).data("id");
                    var image = $(this).data("image");
                    $.ajax({
                        url: base_url + 'delete_ad_spaces',
                        type: "POST",
                        dataType: "json",
                        data: { id: id, image: image },
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