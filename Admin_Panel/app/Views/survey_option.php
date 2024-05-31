<!DOCTYPE html>
<html lang="en">

    <head>
        <title>News | <?=($app_name) ? $app_name[0]->message : '' ?></title>
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
                                <h1>Create and Manage Survey Options</h1>
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
                                        <h3 class="card-title">Add Survey options</h3>
                                    </div>
                                    <form action="<?= APP_URL ?>store_option" role="form" id="insert_form"
                                        method="POST">
                                        <?= csrf_field() ?>
                                        <?php
                                        $this->uri = new \CodeIgniter\HTTP\URI();
                                        $this->request = \Config\Services::request();
                                        ?>
                                        <input type='hidden' name="question_id" id="question_id"
                                            value='<?= $this->request->uri->getSegment(2); ?>' />
                                        <div class="card-body">
                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <label>Question</label>
                                                    <textarea name="question" id="question" class="form-control"
                                                        placeholder="Question"
                                                        readonly><?= $question[0]->question ?></textarea>
                                                </div>
                                            </div>
                                            <div class="addmore_block" id="dynamic_field">
                                                <div class="form-group row">
                                                    <div class="col-sm-6">
                                                        <label>Option </label>
                                                        <input type="text" name="option[]" id="option"
                                                            class="form-control" placeholder="Option" required>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label>&nbsp;</label><br>
                                                        <button type="button" name="add" id="add"
                                                            class="btn btn-secondary"><i
                                                                class="fa fa-plus"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="addmore_block1"></div>
                                            <?php if ($this->session->getFlashdata('error')) { ?>
                                                <div class="col-sm-6 offset-2">
                                                    <p id="error_msg" class="alert alert-danger"><?php echo $this->session->getFlashdata('error'); ?></p>
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
                                        <h3 class="card-title">Options <small> View / Update / Delete</small></h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-1">
                                            <p id="delete_msg" style="display:none;" class="alert alert-success"></p>
                                        </div>
                                        <table aria-describedby="mydesc" class='table-striped' id='survey_option_list'
                                            data-toggle="table" data-url="<?= APP_URL . 'Table/surveyoption' ?>"
                                            data-click-to-select="true" data-side-pagination="server"
                                            data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                                            data-search="true" data-toolbar="#toolbar" data-show-columns="true"
                                            data-show-refresh="true" data-fixed-columns="true" data-fixed-number="1"
                                            data-fixed-right-number="1" data-trim-on-search="false"
                                            data-mobile-responsive="true" data-sort-name="id" data-sort-order="desc"
                                            data-maintain-selected="true" data-export-types='["txt","excel"]'
                                            data-export-options='{ "fileName": "survey_option_list-<?= date('d-m-y') ?>" }'
                                            data-query-params="queryParams">
                                            <thead>
                                                <tr>
                                                    <th scope="col" data-field="id" data-sortable="true">ID</th>
                                                    <th scope="col" data-field="question" data-sortable="true"
                                                        data-visible="false">Question</th>
                                                    <th scope="col" data-field="options" data-sortable="false">Options
                                                    </th>
                                                    <th scope="col" data-field="counter" data-sortable="false">Counter
                                                    </th>
                                                    <th scope="col" data-field="percentage" data-sortable="false">
                                                        Percentage</th>
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
                            <h4 class="modal-title">Edit Survey Option</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="<?= APP_URL ?>update_option" role="form" id="update_form" method="POST">
                            <?= csrf_field() ?>
                            <input type='hidden' name="edit_id" id="edit_id" value='' />
                            <input type='hidden' name="edit_question_id" id="edit_question_id" value='' />
                            <div class="modal-body">
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Option</label>
                                        <input type="text" name="option" id="edit_option" class="form-control"
                                            placeholder="Option" required>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
            var i = 1;
            $('#add').click(function () {
                i++;
                $('#dynamic_field').append('<div class="form-group row" id="row' + i + '"><div class="col-sm-6"><label>Option </label><input type="text" name="option[]" id="option" class="form-control" placeholder="Option" required></div><div class="col-sm-6"><label>&nbsp;</label><br><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_remove"><i class="fas fa-window-close"></i></button></div></div>');
            });
            $(document).on('click', '.btn_remove', function () {
                var button_id = $(this).attr("id");
                $('#row' + button_id + '').remove();
            });
        </script>
        <script type="text/javascript">
            function queryParams(p) {
                return {
                    'question_id': $('#question_id').val(),
                    limit: p.limit,
                    order: p.order,
                    offset: p.offset,
                    search: p.search
                };
            }
        </script>
        <script type="text/javascript">
            window.actionEvents = {
                'click .edit-data': function (e, value, row, index) {
                    $('#edit_id').val(row.id);
                    $("#edit_option").val(row.options);
                    $("#edit_question_id").val(row.question_id);
                }
            };
        </script>
        <script>
            $(document).on('click', '.delete-data', function () {
                if (confirm('Are you sure? Want to delete Option?')) {
                    var base_url = "<?= APP_URL ?>";
                    var id = $(this).data("id");
                    var question_id = $('#question_id').val();
                    $.ajax({
                        url: base_url + 'delete_option',
                        type: "POST",
                        dataType: "json",
                        data: { id: id, question_id: question_id },
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
    </body>

</html>