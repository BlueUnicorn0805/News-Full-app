<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Comments Flag | <?= ($app_name) ? $app_name[0]->message : '' ?></title>        
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
                                        <h3 class="card-title">Comments Flag <small> View </small></h3>
                                    </div>
                                    <div class="card-body">
                                        <?php if ($this->session->getFlashdata('error')) { ?>
                                            <div class="col-sm-6">
                                                <p id="error_msg" class="alert alert-danger"><?php echo $this->session->getFlashdata('error'); ?></p>
                                            </div>
                                        <?php } ?> 
                                        <?php if ($this->session->getFlashdata('success')) { ?>
                                            <div class="col-sm-6">
                                                <p id="success_msg" class="alert alert-success"><?php echo $this->session->getFlashdata('success'); ?></p>
                                            </div>
                                        <?php } ?>
                                        <table aria-describedby="mydesc" class='table-striped' id='comments_flag_list' 
                                               data-toggle="table" 
                                               data-url="<?= APP_URL . 'Table/comments_flag' ?>"
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
                                               data-export-options='{ "fileName": "comments-flag-list-<?= date('d-m-y') ?>" }'
                                               data-query-params="queryParams">
                                            <thead>
                                                <tr>
                                                    <th scope="col" data-field="id" data-sortable="true">ID</th>
                                                    <th scope="col" data-field="comment_id" data-sortable="true">Comment ID</th>
                                                    <th scope="col" data-field="user_id" data-sortable="true" data-visible="false">User ID</th>
                                                    <th scope="col" data-field="news_id" data-sortable="true" data-visible="false">News ID</th>
                                                    <th scope="col" data-field="name" data-sortable="true">User By</th>
                                                    <th scope="col" data-field="title" data-sortable="true">News</th>
                                                    <th scope="col" data-field="comment" data-sortable="true">Comment</th> 
                                                    <th scope="col" data-field="message" data-sortable="true">Message</th> 
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

        <script>
            $(document).on('click', '.delete-comment', function () {
                if (confirm('Are you sure? Want to delete Comment?')) {
                    var base_url = "<?= APP_URL ?>";
                    var id = $(this).data("id");

                    $.ajax({
                        url: base_url + 'delete_comment',
                        type: "POST",
                        dataType: "json",
                        data: {id: id},
                        success: function (result) {
                            if (result) {
                                $('#comments_flag_list').bootstrapTable('refresh');
                            } else {
                                alert('Comment could not be deleted');
                            }
                        }
                    });
                }
            });
            $(document).on('click', '.delete-flag', function () {
                if (confirm('Are you sure? Want to delete Flag?')) {
                    var base_url = "<?= APP_URL ?>";
                    var id = $(this).data("id");

                    $.ajax({
                        url: base_url + 'delete_comment_flag',
                        type: "POST",
                        dataType: "json",
                        data: {id: id},
                        success: function (result) {
                            if (result) {
                                $('#comments_flag_list').bootstrapTable('refresh');
                            } else {
                                alert('Flag could not be deleted');
                            }
                        }
                    });
                }
            });
        </script>        

    </body>
</html>