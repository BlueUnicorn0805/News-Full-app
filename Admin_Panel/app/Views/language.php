    <!DOCTYPE html>
    <html lang="en">

        <head>
            <title>Language | <?=($app_name) ? $app_name[0]->message : '' ?>
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
                                    <h1 class="d-inline-block">Language</h1>
                                    <?php if (is_category_enabled() != 1) { ?>
                                        <label class="badge badge-danger">Disabled</label>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section class="content">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card card-secondary">
                                        <div class="card-header">
                                            <h3 class="card-title">Add Language</h3>
                                        </div>
                                        <form action="<?= APP_URL ?>store_language" role="form" id="insert_form"
                                            method="POST" enctype="multipart/form-data">
                                            <?= csrf_field() ?>
                                            <div class="card-body">
                                                <div class="form-group row">
                                                    <div class="col-sm-12">
                                                        <label>Languages</label>
                                                        <input id="language" name="language" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-sm-12">
                                                        <label>Display Name (Display in app/web)</label>
                                                        <input id="display_name" name="display_name" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-sm-12">
                                                        <label>Code</label>
                                                        <input id="code" name="code" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-sm-12">
                                                        <label>Language JSON File</label>
                                                        <div class="custom-file">
                                                            <input name="file" type="file" accept="application/json"
                                                                class="custom-file-input json_file" required>
                                                            <label class="custom-file-label" id="image1"
                                                                for="customFile">Choose file</label>
                                                                
                                                        </div>
                                                        <p style="display:none" class="alert alert-danger json_error_msg"></p>
                                                    </div>
                                                    <div class="col-sm-5 mt-1">
                                                        <a class="btn btn-info form-control"
                                                            href="<?= APP_URL ?>download_sample_file">Download Sample
                                                            File</a>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-sm-12">
                                                        <label>Flag Image</label><small class="text-danger"> Only png, jpg, webp, gif and jpeg image allow</small>
                                                        <div class="custom-file">
                                                            <input name="flag" type="file" accept="image/*"
                                                                class="custom-file-input" id="exampleInputFile" required>
                                                            <label class="custom-file-label" id="flag"
                                                                for="customFile">Choose file</label>
                                                        </div>
                                                        <p style="display:none" id="img_error_msg"
                                                            class="alert alert-danger"></p>
                                                    </div>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="isRTL" id="isRTL">
                                                    <label class="form-check-label" for="isRTL1">
                                                        Is RTL
                                                    </label>
                                                </div>
                                                <div class="form-group row">
                                                    
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
                                <div class="col-md-8">
                                    <div class="card card-secondary">
                                        <div class="card-header">
                                            <h3 class="card-title">Languages <small>View / Update / Delete</small></h3>
                                        </div>
                                        <div class="card-body">
                                            <table aria-describedby="mydesc" class='table-striped' id='language_list'
                                                data-toggle="table" data-url="<?= APP_URL . 'Table/get_all_language' ?>"
                                                data-click-to-select="true" data-side-pagination="server"
                                                data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                                                data-search="true" data-show-columns="true" data-show-refresh="true"
                                                data-fixed-columns="true" data-fixed-number="1" data-fixed-right-number="1"
                                                data-trim-on-search="false" data-sort-name="id" data-sort-order="desc"
                                                data-mobile-responsive="true" data-maintain-selected="true"
                                                data-export-types='["txt","excel"]'
                                                data-export-options='{ "fileName": "language-list-<?= date('d-m-y') ?>" }'
                                                data-query-params="queryParams">
                                                <thead>
                                                    <tr>
                                                        <th scope="col" data-field="id" data-sortable="true">ID</th>
                                                        <th scope="col" data-field="language" data-sortable="true">Name
                                                        </th>
                                                        <th scope="col" data-field="display_name" data-sortable="false">Display Name</th>
                                                        <th scope="col" data-field="code" data-sortable="false">Code</th>
                                                        <th scope="col" data-field="image" data-sortable="false">Flag</th>
                                                        <th scope="col" data-field="default" data-sortable="false">Default
                                                        </th>
                                                        <th scope="col" data-field="is_RTL" data-sortable="false">RTL
                                                        </th>
                                                        <th scope="col" data-field="status" data-sortable="false">Status
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
                </div>
                <div class="modal fade" id="editDataModal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Language Status</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="<?= APP_URL ?>update_language_staus" role="form" id="update_form" method="POST"
                                enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <input type='hidden' name="language_id" id="language_id" value='' />
                                <input type='hidden' name="languagestatus" id="language_status" value='' />
                                <div class="modal-body">
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <label>Languages</label>
                                            <input id="edit_language" name="language" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <label>Display Name (Display in app/web)</label>
                                            <input id="edit_display_name" name="display_name" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <label>Code</label>
                                            <input id="edit_code" name="code" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        
                                        <div class="col-md-10 col-sm-10 col-xs-12">
                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <label>Language JSON File</label>
                                                    <div class="custom-file">
                                                        <input name="file" type="file" accept="application/json"
                                                            class="custom-file-input json_file">
                                                        <label class="custom-file-label" id="image1" for="customFile">Choose
                                                            file</label>
                                                    </div>
                                                    <p style="display:none" id="" class="alert alert-danger json_error_msg">
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <label>Flag Image</label> <small class="text-danger">Only png, jpg, webp, gif and jpeg image allow</small>
                                                    <div class="custom-file">
                                                        <input name="flag" type="file" accept="image/*"
                                                            class="custom-file-input" id="exampleInputFile_edit">
                                                        <label class="custom-file-label" id="flag" for="customFile">Choose
                                                            file</label>
                                                    </div>
                                                    <p style="display:none" id="img_error_msg_edit" class="alert alert-danger">
                                                    </p>
                                                </div>
                                            </div>
                                            
                                    
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="isRTL" id="edit_isRTL" value="1">
                                                <label class="form-check-label" for="isRTL1">
                                                    Is RTL
                                                </label>
                                            </div>
                                            
                                        </div>
                                        <div class="col-sm-12">
                                        <label>Status</label>
                                            <div id="status1" class="btn-group">
                                                <label class="btn btn-success" data-toggle-class="btn-primary"
                                                    data-toggle-passive-class="btn-default">
                                                    <input class="language_status" type="radio" name="language_status"
                                                        value="1"> Active
                                                </label>
                                                <label class="btn btn-danger" data-toggle-class="btn-primary"
                                                    data-toggle-passive-class="btn-default">
                                                    <input class=" language_status" type="radio" name="language_status"
                                                        value="0"> Deactive
                                                </label>
                                            </div>
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
                        console.log(row);
                        $('#language_id').val(row.id);
                        $('#edit_language').val(row.language);
                        $('#edit_display_name').val(row.display_name);
                        $('#edit_code').val(row.code);
                    
                        $("input[name=language_status][value=1]").prop('checked', true);
                        if ($(row.status).text() == 'Deactive')
                            $("input[name=language_status][value=0]").prop('checked', true);

                        if (row.isRTL == '1'){
                            $("#edit_isRTL").prop('checked', true);
                        }else{
                            $("#edit_isRTL").prop('checked', false);
                        }
                    }
                }
            </script>
            <script type="text/javascript">
                var data = {}; // Globally scoped object
            </script>
            <script type="text/javascript">
                function queryParams(p) {
                    return {
                        limit: p.limit,
                        order: p.order,
                        offset: p.offset,
                        search: p.search,
                        status_click: data.status,
                    };
                }
            </script>
            <script type="text/javascript">
                var _URL = window.URL || window.webkitURL;
                var isRTL = document.querySelector('#isRTL');
                isRTL.onchange = function () {
                    if (isRTL.checked)
                        $('#isRTL').val(1);
                    else
                        $('#isRTL').val(0);
                };
                $(document).on('click', '.delete-data', function () {
                    if (confirm('Are you sure? Want to delete Language? All related data(Categories,Subcategories,News,Breaking news,Tags,Surveys) will also be deleted')) {
                        var base_url = "<?= APP_URL ?>";
                        var id = $(this).data("id");
                        $.ajax({
                            url: base_url + 'delete_language',
                            type: "POST",
                            dataType: "json",
                            data: {
                                id: id
                            },
                            success: function (result) {
                                if (result) {
                                    $('#language_list').bootstrapTable('refresh');
                                } else {
                                    alert('Language could not be deleted');
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
            <script type="text/javascript">
                $(document).on('click', '.store_default_language', function () {
                    var id = $(this).data("id");
                    var base_url = "<?= APP_URL ?>";
                    $.ajax({
                        url: base_url + 'store_default_language',
                        type: "POST",
                        dataType: "json",
                        data: {
                            id: id
                        },
                        success: function (result) {
                            if (result) {
                                setTimeout(function () {
                                    window.location.reload();
                                }, 200);
                            }
                        }
                    });
                });
            </script>
            <script type="text/javascript">
            var _URL = window.URL || window.webkitURL;
            $("#exampleInputFile").change(function (e) {
                var file, img;
                if ((file = this.files[0])) {
                    img = new Image();
                    img.onerror = function () {
                        $("#exampleInputFile").val('');
                        $('.custom-file-label').html('');
                        $('#img_error_msg').html("Invalid Image Type");
                        $('#img_error_msg').show().delay(3000).fadeOut();
                    };
                    img.src = _URL.createObjectURL(file);
                }
            });
            $("#exampleInputFile_edit").change(function (e) {
                var file, img;
                if ((file = this.files[0])) {
                    img = new Image();
                    img.onerror = function () {
                        $("#exampleInputFile_edit").val('')
                        $('.custom-file-label').html('');
                        $('#img_error_msg_edit').html("Invalid Image Type");
                        $('#img_error_msg_edit').show().delay(3000).fadeOut();
                    };
                    img.src = _URL.createObjectURL(file);
                }
            });
            $(".json_file").change(function (e) {
                var ext = $(this).val().split('.').pop().toLowerCase();
                if($.inArray(ext, ['json']) == -1) {
                    $(this).val('');
                    $(this).closest('.col-sm-12').find('.json_error_msg').html("Invalid Json Type");
                    $(this).closest('.col-sm-12').find('.json_error_msg').show().delay(3000).fadeOut();
                }
            });
        </script>
        </body>

    </html>