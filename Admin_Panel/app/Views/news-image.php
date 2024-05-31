<!DOCTYPE html>
<html lang="en">
    <head>
        <title>News Images | <?= ($app_name) ? $app_name[0]->message : '' ?></title>        
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
                                <h1>Create and Manage Images</h1>
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
                                        <h3 class="card-title">Add News Images</h3>
                                    </div>
                                    <form action="<?= APP_URL ?>store_news_image" role="form" id="insert_form" method="POST" enctype="multipart/form-data">
                                        <?= csrf_field() ?>
                                        <?php
                                        $this->uri = new \CodeIgniter\HTTP\URI();
                                        $this->request = \Config\Services::request();
                                        ?>
                                        <input type='hidden' name="news_id" id="news_id" value='<?= $this->request->uri->getSegment(2); ?>'/>
                                        <div class="card-body">
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <label>Title</label>                                           
                                                    <input value="<?= ($news) ? $news[0]->title : '' ?>" type="text" name="title" readonly class="form-control" placeholder="news title" required>
                                                </div> 
                                                <div class="col-sm-6">
                                                <label>Image <small class="text-danger">Only png, jpg, webp, gif and jpeg image allow</small></label>
                                                    <div class="custom-file">
                                                        <input name="file[]" type="file" multiple accept="image/*" class="custom-file-input" id="exampleInputFile1" required>
                                                        <label class="custom-file-label" id="image" for="customFile">Choose file</label>
                                                        
                                                    </div>
                                                    <p style="display:none" id="img_error_msg" class="alert alert-danger"></p>
                                                </div> 
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
                                        <h3 class="card-title">News <small> View / Update / Delete</small></h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-1">
                                            <p id="delete_msg" style="display:none;" class="alert alert-success"></p>
                                        </div>    
                                        <table aria-describedby="mydesc" class='table-striped' id='news_list' 
                                               data-toggle="table" 
                                               data-url="<?= APP_URL . 'Table/newsImage' ?>"
                                               data-click-to-select="true" 
                                               data-side-pagination="server" 
                                               data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" 
                                               data-search="true" data-toolbar="#toolbar"
                                               data-show-columns="true" data-show-refresh="true" 
                                               data-fixed-columns="true" data-fixed-number="1" data-fixed-right-number="1"
                                               data-trim-on-search="false" 
                                               data-sort-name="id" data-sort-order="desc" 
                                               data-mobile-responsive="true"
                                               data-maintain-selected="true" data-export-types='["txt","excel"]' 
                                               data-export-options='{ "fileName": "news-image-list-<?= date('d-m-y') ?>" }'
                                               data-query-params="queryParams">
                                            <thead>
                                                <tr>
                                                    <th scope="col" data-field="id" data-sortable="true">ID</th>
                                                    <th scope="col" data-field="title" data-sortable="true">Title</th>
                                                    <th scope="col" data-field="image" data-sortable="false">Image</th>
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
                    'news_id': $('#news_id').val(),
                    limit: p.limit,
                    order: p.order,
                    offset: p.offset,
                    search: p.search
                };
            }
        </script>
        <script>
            $(document).on('click', '.delete-data', function () {
                if (confirm('Are you sure? Want to delete image?')) {
                    var base_url = "<?= APP_URL ?>";
                    var id = $(this).data("id");
                    var image = $(this).data("image");
                    $.ajax({
                        url: base_url + 'delete_news_image',
                        type: "POST",
                        dataType: "json",
                        data: {id: id, image_url: image},
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