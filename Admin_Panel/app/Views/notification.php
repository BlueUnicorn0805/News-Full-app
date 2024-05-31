<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Notification | <?= ($app_name) ? $app_name[0]->message : '' ?></title>        
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
                                <h1>Create and Manage Notification</h1>
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
                                        <h3 class="card-title">Add Notification</h3>
                                    </div>
                                    <form action="<?= APP_URL ?>store_notification" role="form" id="insert_form" method="POST" enctype="multipart/form-data">
                                        <?= csrf_field() ?>                                        
                                        <div class="card-body">
                                            <div class="form-group row"> 
                                            <div class="col-sm-6">
                                                    <label>Language</label>
                                                    <select id="language" name="language" class="form-control language" required>
                                                                <option value="1" selected disabled>Select Language</option>
                                                                <?php foreach ($languages as $row) { ?>
                                                                    <option value="<?php echo $row->id; ?>">
                                                                        <?php echo $row->language; ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                </div>                                               
                                                <div class="col-sm-6">
                                                    <label>Type</label>
                                                    <select name="type" id="type" class="form-control" required>
                                                        <option value="default">Default</option>
                                                         <?php if (is_category_enabled() == 1) { ?>
                                                        <option value="category">Category</option>
                                                         <?php } ?>
                                                    </select>
                                                </div>                                               
                                            </div>
                                            <div class="form-group row" id="cate_type">
                                                <?php if (is_category_enabled() == 1) { ?>
                                                    <?php if (is_subcategory_enabled() == 1) { ?>
                                                        <div class="col-sm-4">
                                                            <label>Category</label>
                                                            <select id="category_id" name="category_id" class="form-control" required>
                                                                <option value="">Select Category</option>
                                                                <?php foreach ($cate as $cate1): ?>
                                                                    <option value="<?= $cate1['id']; ?>"><?= $cate1['category_name']; ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div> 
                                                        <div class="col-sm-4">
                                                            <label>Subcategory</label>
                                                            <select id="subcategory_id" name="subcategory_id" class="form-control">
                                                                <option value="">Select Subcategory</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <label>News</label>
                                                            <select name="news_id" id="news_id" class="form-control" required>
                                                                <option value="">Select News</option>
                                                            </select>
                                                        </div>
                                                    <?php } else { ?>
                                                        <div class="col-sm-6">
                                                            <label>Category</label>
                                                            <select name="category_id" id="category_id" class="form-control" required>
                                                                <option value="">Select Category</option>
                                                                <?php foreach ($cate as $cate1): ?>
                                                                    <option value="<?= $cate1['id']; ?>"><?= $cate1['category_name']; ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div> 
                                                        <div class="col-sm-6">
                                                            <label>News</label>
                                                            <select name="news_id" id="news_id" class="form-control" required>
                                                                <option value="">Select News</option>
                                                            </select>
                                                        </div>
                                                    <?php } ?>
                                                <?php } ?>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <label>Title</label>
                                                    <input type="text" name="title" class="form-control" placeholder="title" required>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label>Image <small class="text-danger">Only png, jpg and jpeg image allow</small></label>
                                                    <div class="custom-file">
                                                        <input name="file" type="file" accept="image/*" class="custom-file-input" id="exampleInputFile1">
                                                        <label class="custom-file-label" id="image" for="customFile">Choose file</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">   
                                                <div class="col-sm-12">
                                                    <label>Message</label>
                                                    <textarea name="message" class="form-control" placeholder="Message" required></textarea>
                                                </div>                                               
                                            </div>
                                            <div class="form-group col-sm-6 offset-2">
                                                <p style="display:none" id="img_error_msg" class="alert alert-danger"></p>
                                            </div>
                                            <?php if ($this->session->getFlashdata('error')) { ?>
                                                <div class="col-sm-6 offset-2">
                                                    <p id="error_msg" class="alert alert-danger"><?php echo $this->session->getFlashdata('error'); ?></p>
                                                </div>
                                            <?php } ?> 
                                            <?php if ($this->session->getFlashdata('success')) { ?>
                                                <div class="col-sm-6 offset-2">
                                                    <p id="success_msg" class="alert alert-success"><?php echo $this->session->getFlashdata('success'); ?></p>
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
                                        <h3 class="card-title">Notification <small> View / Delete</small></h3>
                                    </div>
                                    <div class="card-body">
                                        <table aria-describedby="mydesc" class='table-striped' id='notification_list' 
                                               data-toggle="table" 
                                               data-url="<?= APP_URL . 'Table/notification' ?>"
                                               data-click-to-select="true" 
                                               data-side-pagination="server" 
                                               data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" 
                                               data-search="true" 
                                               data-show-columns="true" data-show-refresh="true" 
                                               data-fixed-columns="true" data-fixed-number="1" data-fixed-right-number="1"
                                               data-trim-on-search="false" 
                                               data-sort-name="id" data-sort-order="desc" 
                                               data-mobile-responsive="true"
                                               data-maintain-selected="true" data-export-types='["txt","excel"]' 
                                               data-export-options='{ "fileName": "notification-list-<?= date('d-m-y') ?>" }'
                                               data-query-params="queryParams">
                                            <thead>
                                                <tr>
                                                    <th scope="col" data-field="id" data-sortable="true">ID</th>
                                                    <th scope="col" data-field="category_name" data-sortable="false">Category</th>
                                                    <th scope="col" data-field="subcategory_name" data-sortable="false">Sub Category</th>
                                                    <th scope="col" data-field="news_title" data-sortable="true">News</th>
                                                    <th scope="col" data-field="title" data-sortable="true">Title</th>  
                                                    <th scope="col" data-field="message" data-sortable="false">Message</th>
                                                    <th scope="col" data-field="image" data-sortable="false">Image</th>
                                                    <th scope="col" data-field="date" data-sortable="true">Date</th>
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
            <?php base_url() . include 'footer.php'; ?>  
        </div>
        <!-- ./wrapper -->
        <script type="text/javascript">
            window.actionEvents = {
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
            $('#cate_type').hide();
            $("#type").change(function () {
                type = $("#type").val();
                if (type == "default") {
                    $("#cate_type").hide();
                }
                if (type == "category") {
                    $("#cate_type").show();
                }
            });
        </script>
        <script type="text/javascript">
            var base_url = "<?= APP_URL ?>";
<?php if (is_category_enabled() == 1) { ?>

    $('#type, #language').on('change', function (e) {
                        var language = $('#language').val();
                        $.ajax({
                            url: base_url + 'get_category_by_language',
                            type: "POST",
                            data: {language_id: language},
                            beforeSend: function () {
                                $('#category_id').html('Please wait..');
                            },
                            success: function (result) {
                                $('#category_id').html(result);
                            }
                        });
                    });

    <?php if (is_subcategory_enabled() == 1) { ?>
                    $('#category_id').on('change', function (e) {
                        var category_id = $('#category_id').val();
                        $.ajax({
                            url: base_url + 'get_subcategory_by_category',
                            type: "POST",
                            data: {category_id: category_id},
                            beforeSend: function () {
                                $('#subcategory_id').html('Please wait..');
                            },
                            success: function (result) {
                                $('#subcategory_id').html(result);
                            }
                        });
                    });
                    $('#subcategory_id').on('change', function (e) {
                        var subcategory_id = $('#subcategory_id').val();
                        $.ajax({
                            type: 'GET',
                            url: base_url + 'get_news_by_subcategory/' + subcategory_id,
                            beforeSend: function () {
                                $('#news_id').html('Please wait..');
                            },
                            success: function (result) {
                                $('#news_id').html(result);
                            }
                        });
                    });
					//get news from catergory,,, if subcategory not assigne in news,only category assign to news
					$('#category_id').on('change', function (e) {
                        var category_id = $('#category_id').val();
                        $.ajax({
                            url: base_url + 'get_news_by_category/' + category_id,
                            type: "GET",
                            beforeSend: function () {
                                $('#news_id').html('Please wait..');
                            },
                            success: function (result) {
                                $('#news_id').html(result);
                            }
                        });
                    });
    <?php } else { ?>
                    $('#category_id').on('change', function (e) {
                        var category_id = $('#category_id').val();
                        $.ajax({
                            type: 'GET',
                            url: base_url + 'get_news_by_category/' + category_id,
                            beforeSend: function () {
                                $('#news_id').html('Please wait..');
                            },
                            success: function (result) {
                                $('#news_id').html(result);
                            }
                        });
                    });
    <?php } ?>
<?php } ?>
        </script>
        <script>
            $(document).on('click', '.delete-data', function () {
                if (confirm('Are you sure? Want to delete notification?')) {
                    var base_url = "<?= APP_URL ?>";
                    var id = $(this).data("id");
                    var image = $(this).data("image");
                    $.ajax({
                        url: base_url + 'delete_notification',
                        type: "POST",
                        dataType: "json",
                        data: {id: id, image_url: image},
                        success: function (result) {
                            if (result) {
                                $('#notification_list').bootstrapTable('refresh');
                            } else {
                                alert('Notification could not be deleted');
                            }
                        }
                    });
                }
            });
        </script>
        <script>
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
    </body>
</html>