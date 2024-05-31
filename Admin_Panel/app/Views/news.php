<!DOCTYPE html>
<html lang="en">

    <head>
        <title>News | <?=($app_name) ? $app_name[0]->message : '' ?>
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
                                <h1>Create and Manage News</h1>
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
                                        <h3 class="card-title">Add News</h3>
                                    </div>
                                    <form action="<?= APP_URL ?>store_news" role="form" id="insert_form" method="POST"
                                        enctype="multipart/form-data">
                                        <?= csrf_field() ?>
                                        <div class="card-body">
                                            <div class="form-group row">
                                                <?php if (is_category_enabled() == 1) { ?>
                                                    <?php if (is_subcategory_enabled() == 1) { ?>
                                                        <div class="col-sm-6 col-md-3">
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
                                                        <div class="col-sm-6 col-md-3">
                                                            <label>Category</label>
                                                            <select id="category_id" name="category_id" class="form-control category_id"
                                                                required>
                                                                <option value="">Select Category</option>
                                                                <?php foreach ($cate as $cate1): ?>
                                                                    <option value="<?= $cate1['id']; ?>">
                                                                        <?= $cate1['category_name']; ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-6 col-md-3">
                                                            <label>Subcategory</label>
                                                            <select id="subcategory_id" name="subcategory_id"
                                                                class="form-control subcategory_id">
                                                                <option value="">Select Subcategory</option>
                                                            </select>
                                                        </div>
                                                        <?php
                                                        //expiry date min date set tomorrow //optional
                                                        $datetime = new DateTime('tomorrow');
                                                        $tomorrow =  $datetime->format('Y-m-d');
                                                        ?> 
                                                        <div class="col-sm-6 col-md-3">
                                                            <label>Show Till (Expiry Date)</label>
                                                            <input type="date" name="show_till" class="form-control"
                                                                placeholder="" min="<?php echo $tomorrow; ?>">
                                                        </div>
                                                    <?php } else { ?>
                                                        <div class="col-sm-6 col-md-3">
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
                                                        <div class="col-sm-6 col-md-3">
                                                            <label>Category</label>
                                                            <select name="category_id" class="form-control category_id" required>
                                                                <option value="">Select Category</option>
                                                                <?php foreach ($cate as $cate1): ?>
                                                                    <option value="<?= $cate1['id']; ?>">
                                                                        <?= $cate1['category_name']; ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-6 col-md-3">
                                                            <label>Show Till (Expiry Date)</label>
                                                            <input type="date" name="show_till" class="form-control"
                                                                placeholder="" min="<?php echo date("Y-m-d"); ?>">
                                                        </div>
                                                    <?php } ?>
                                                <?php } ?>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <label>Title</label>
                                                    <input type="text" name="title" class="form-control"
                                                        placeholder="news title" required >
                                                </div>
                                                <div class="col-sm-6">
                                                    <label>Tag</label>
                                                    <select name="tag_id[]" class="form-control select2 select_tag_id"
                                                        multiple="multiple" id="select_tag_id">
                                                        <?php foreach ($tag as $tag1): ?>
                                                            <option value="<?= $tag1['id']; ?>">
                                                                <?= $tag1['tag_name']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                <label>Featured Image <small class="text-danger">Only png, jpg, webp, gif and jpeg image allow</small></label>
                                                    <div class="custom-file">
                                                        <input name="file" type="file" accept="image/*"
                                                            class="custom-file-input" id="exampleInputFile1" required>
                                                        <label class="custom-file-label" id="image"
                                                            for="customFile">Choose file</label>
                                                    </div>
                                                    <p style="display:none" id="img_error_msg"
                                                        class="alert alert-danger"></p>
                                                </div>
                                                <div class="col-sm-6">
                                                <label>Gallery Images <small class="text-danger">Only png, jpg, webp, gif and jpeg image allow</small></label>
                                                    <div class="custom-file">
                                                        <input name="ofile[]" type="file" accept="image/*" multiple
                                                            class="custom-file-input" id="exampleInputFile2">
                                                        <label class="custom-file-label" id="image2" for="customFile">Choose
                                                            file</label>
                                                    </div>
                                                     <p style="display:none" id="img_error_msg2"
                                                        class="alert alert-danger"></p>
                                                </div>
                                               
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <label>Content Type</label>
                                                    <select name="content_type" id="content_type" class="form-control" required>
                                                        <option value="standard_post" selected>Standard Post</option>
                                                        <option value="video_youtube">Video (YouTube)</option>
                                                        <option value="video_other">Video (Other Url)</option>
                                                        <option value="video_upload">Video (Upload)</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-6 video_youtube">
                                                    <label>Youtube URL</label>
                                                    <input type="url" name="youtube_url" class="form-control youtube_url" required>
                                                    <span class="error invalid-feedback youtube_url_error"></span>
                                                </div>
                                                <div class="col-sm-6 video_other">
                                                    <label>Other URL</label>
                                                    <input type="url" name="other_url" class="form-control other_url" required>
                                                    <span class="error invalid-feedback other_url_error"></span>
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
                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <label>Description</label>
                                                    <textarea id="des" name="des" class="form-control"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Notify Users</label>
                                                <div>
                                                    <input type="checkbox" id="is_notification" class="is_notification" name="is_notification" data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                                    <input type="hidden" id="notification" class="notification" name="notification" value="0">
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
                                        <h3 class="card-title">News <small> View / Update / Delete</small></h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-1">
                                            <p id="delete_msg" style="display:none;" class="alert alert-success"></p>
                                        </div>
                                        <?php if (is_category_enabled() == 1) { ?>
                                            <div class="row">
                                                <div class="col-sm-3 col-md-3 col-lg-2">
                                                    <select id="filter_language" name="language" class="form-control">
                                                    <option value="">Select Language</option>
                                                        <?php foreach ($languages as $row) : ?>
                                                            <option value="<?php echo $row->id; ?>">
                                                                <?php echo $row->language; ?>
                                                        </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-sm-3 col-md-3 col-lg-2">
                                                    <select id="filter_category" name="filter_category" class="form-control">
                                                        <option value="">Select Category</option>
                                                        <?php foreach ($cate as $cate1): ?>
                                                            <option value="<?= $cate1['id']; ?>">
                                                                <?= $cate1['category_name']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <?php if (is_subcategory_enabled() == 1) { ?>
                                                    <div class="col-sm-3 col-md-3 col-lg-2">
                                                        <select id="filter_subcategory" name="subcategory_id"
                                                            class="form-control">
                                                            <option value="">Select Subcategory</option>
                                                        </select>
                                                    </div>
                                                <?php } ?>
                                                
                                                <div class="col-sm-3 col-md-3 col-lg-2">
                                                    <select id="filter_user" name="user_id" class="form-control">
                                                        <option value="">Select User</option>
                                                        <?php foreach ($user as $u): ?>
                                                            <option value="<?= $u->id; ?>">
                                                                <?= $u->name; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-sm-3 col-md-3 col-lg-2">
                                                    <select id="filter_role" name="role_id" class="form-control">
                                                        <option value="">Select Role</option>
                                                        <?php foreach ($role as $r): ?>
                                                            <option value="<?= $r['id']; ?>">
                                                                <?= $r['role']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-sm-3 col-md-3 col-lg-1">
                                                    <select id="filter_status" name="status" class="form-control">
                                                        <option value="">Status</option>
                                                        <option value="1">Active</option>
                                                        <option value="0">Deactive</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-3 col-md-3 col-lg-1">
                                                    <button class='btn btn-primary btn-block' id='filter_btn'>Filter</button>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <table aria-describedby="mydesc" class='table-striped' id='news_list'
                                            data-toggle="table" data-url="<?= APP_URL . 'Table/news' ?>"
                                            data-click-to-select="true" data-side-pagination="server"
                                            data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                                            data-search="true" data-toolbar="#toolbar" data-show-columns="true"
                                            data-show-refresh="true" data-fixed-columns="true" data-fixed-number="1"
                                            data-fixed-right-number="1" data-trim-on-search="false"
                                            data-mobile-responsive="true" data-sort-name="id" data-sort-order="desc"
                                            data-maintain-selected="true" data-export-types='["txt","excel"]'
                                            data-export-options='{ "fileName": "news-list-<?= date('d-m-y') ?>" }'
                                            data-query-params="queryParams">
                                            <thead>
                                                <tr>
                                                    <th scope="col" data-field="id" data-sortable="true">ID</th>
                                                    <?php if (is_category_enabled() == 1) { ?>
                                                        <th scope="col" data-field="category_name" data-sortable="true">
                                                            Category</th>
                                                        <?php if (is_subcategory_enabled() == 1) { ?>
                                                            <th scope="col" data-field="subcategory_name" data-sortable="true">
                                                                Sub<br/>category</th>
                                                        <?php } ?>
                                                    <?php } ?>
                                                    <th scope="col" data-field="short_title" data-sortable="false">Title
                                                    </th>
                                                    <th scope="col" data-field="content_type" data-sortable="false">
                                                        Content <br/>Type</th>
                                                    <th scope="col" data-field="image" data-sortable="false">Image</th>
                                                    <th scope="col" data-field="date" data-sortable="false">Date</th>
                                                    <th scope="col" data-field="tag_name" data-visible="false" data-sortable="false">Tag</th>
                                                    <th scope="col" data-field="total_image" data-sortable="false">More <br/>
                                                        Image</th>
                                                    <th scope="col" data-field="user" data-sortable="false">User</th>
                                                    <th scope="col" data-field="language" data-sortable="false">Language
                                                    </th>
                                                    <th scope="col" data-field="views" data-visible="false" data-sortable="false">Views
                                                    </th>
                                                    <th scope="col" data-field="likes" data-visible="false" data-sortable="false">Likes
                                                    </th>
                                                    <th scope="col" data-field="is_expire" data-sortable="false">Is <br/>Expire?
                                                    </th>
                                                    <th scope="col" data-field="status" data-sortable="true">Status</th>
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
                            <h4 class="modal-title">Edit News</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="<?= APP_URL ?>update_news" role="form" id="update_form" method="POST"
                            enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <input type='hidden' name="edit_id" id="edit_id" value='' />
                            <input type='hidden' name="image_url" id="image_url" value='' />
                            <input type='hidden' name="video_url" id="video_url" value='' />
                            <div class="modal-body">
                                <div class="form-group row">
                                    <?php if (is_category_enabled() == 1) { ?>
                                        <?php if (is_subcategory_enabled() == 1) { ?>
                                            <div class="col-sm-4">
                                        <label>Language</label>
                                        <select id="edit_language" name="edit_language" class="form-control language" required>
                                            <option value="">Select language</option>
                                            <?php foreach ($languages as $row) { ?>
                                                <option value="<?php echo $row->id; ?>">
                                                    <?php echo $row->language; ?>
                                                </option>
                                            <?php }
                                            ?>
                                        </select>
                                    </div>
                                            <div class="col-sm-4">
                                                <label>Category</label>
                                                <select id="edit_category" name="category_id" class="form-control category_id" required>
                                                    <option value="">Select Category</option>
                                                    <?php foreach ($cate as $cate1): ?>
                                                        <option value="<?= $cate1['id']; ?>">
                                                            <?= $cate1['category_name']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-4">
                                                <label>Subcategory</label>
                                                <select id="edit_subcategory" name="subcategory_id" class="form-control subcategory_id">
                                                    <option value="">Select Subcategory</option>
                                                </select>
                                            </div>
                                        <?php } else { ?>
                                            <div class="col-sm-6">
                                        <label>Language</label>
                                        <select id="edit_language" name="edit_language" class="form-control language" required>
                                            <option value="">Select language</option>
                                            <?php foreach ($languages as $row) { ?>
                                                <option value="<?php echo $row->id; ?>">
                                                    <?php echo $row->language; ?>
                                                </option>
                                            <?php }
                                            ?>
                                        </select>
                                    </div>
                                            <div class="col-sm-12">
                                                <label>Category</label>
                                                <select id="edit_category" name="category_id" class="form-control category_id" required>
                                                    <option value="">Select Category</option>
                                                    <?php foreach ($cate as $cate1): ?>
                                                        <option value="<?= $cate1['id']; ?>">
                                                            <?= $cate1['category_name']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Title</label>
                                        <input type="text" name="title" id="edit_title" class="form-control"
                                            placeholder="news title" required >
                                    </div>
                                    <div class="col-md-6">
                                        <label>Tag</label>
                                        <select id="tag_id" name="tag_id[]" class="form-control select2 select_tag_id edit_select_tag_id"
                                            multiple="multiple">
                                            <?php foreach ($tag as $tag1): ?>
                                                <option value="<?= $tag1['id']; ?>">
                                                    <?= $tag1['tag_name']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
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
                                        <input type="url" name="youtube_url" id="youtube_url" class="form-control youtube_url"
                                            required>
                                        <span class="error invalid-feedback youtube_url_error"></span>
                                    </div>
                                    <div class="col-sm-6 evideo_other">
                                        <label>Other URL</label>
                                        <input type="url" name="other_url" id="other_url" class="form-control other_url" required>
                                        <span class="error invalid-feedback other_url_error"></span>
                                    </div>
                                    <div class="col-sm-6 evideo_upload">
                                        <label>Video Upload</label>
                                        <div class="custom-file">
                                            <input name="video_file" type="file" class="custom-file-input"
                                                id="exampleVideoInputFile1_edit">
                                            <label class="custom-file-label" id="video_edit" for="customFile">Choose
                                                file</label>
                                        </div>
                                        <p style="display:none" id="video_error_msg_edit" class="alert alert-danger"></p>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                    <label>Featured Image <small class="text-danger">Only png, jpg, webp, gif and jpeg image allow</small></label>
                                        <div class="custom-file">
                                            <input name="file" type="file" accept="image/*" class="custom-file-input"
                                                id="exampleInputFile11">
                                            <label class="custom-file-label" id="image1" for="customFile">Choose
                                                file</label>
                                        </div>
                                        <p style="display:none" id="img_error_msg1" class="alert alert-danger"></p>
                                    </div>
                                    <?php
                                        //expiry date min date set tomorrow //optional
                                        $datetime = new DateTime('tomorrow');
                                        $tomorrow =  $datetime->format('Y-m-d');
                                    ?>
                                    <div class="col-sm-6">
                                        <label>Show Till (Expiry Date)</label>
                                        <div class="custom-file">
                                            <input id="edit_show_till" type="date" name="show_till" class="form-control"
                                                placeholder="" min="<?php echo $tomorrow; ?>">
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
                                    <div class="col-md-6">
                                        <label>Notify Users</label>
                                        <div>
                                            <input type="checkbox" id="edit_is_notification" class="edit_is_notification" name="edit_is_notification" data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                            <input type="hidden" id="edit_notification" class="edit_notification" name="notification" value="0">
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
            <div class="modal fade" id="editDataDesModal">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Edit News Description</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="<?= APP_URL ?>update_news_des" role="form" method="POST"
                            enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <input type='hidden' name="edit_des_id" id="edit_des_id" value='' />
                            <div class="modal-body">
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <label>Description</label>
                                        <textarea id="edit_des" name="des" class="form-control"></textarea>
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
            <?php base_url() . include 'footer.php'; ?>
        </div>
        <!-- ./wrapper -->
        <script>
            $(function () {
                $('.select2').select2({
                    placeholder: 'Select Tag'
                });
                $("input[data-bootstrap-switch]").bootstrapSwitch();
                var is_notification = document.querySelector('#is_notification');
                is_notification.onchange = function () {
                    if (is_notification.checked) {
                        $('#notification').val(1);
                    } else {
                        $('#notification').val(0);
                    }
                };
                //edit_notification
                var edit_is_notification = document.querySelector('#edit_is_notification');
                edit_is_notification.onchange = function () {
                    if (edit_is_notification.checked) {
                        $('#edit_notification').val(1);
                    } else {
                        $('#edit_notification').val(0);
                    }
                };
                
            });
        </script>
        <script>
            var base_url = "<?= APP_URL ?>";
            $('.language').on('change', function (e) {
                var language_id = $(this).val();
                $.ajax({
                    url: base_url + 'get_category_by_language',
                    type: "POST",
                    data: { language_id: language_id },
                    beforeSend: function () {
                        $('.category_id').html('Please wait..');
                    },
                    success: function (result) {
                        $('.category_id').html(result);
                    }
                });
            });
            $('.category_id').on('change', function (e) {
                var category_id = $(this).val();
                $.ajax({
                    url: base_url + 'get_subcategory_by_category',
                    type: "POST",
                    data: { category_id: category_id },
                    beforeSend: function () {
                        $('.subcategory_id').html('Please wait..');
                    },
                    success: function (result) {
                        $('.subcategory_id').html(result);
                    }
                });
            });
            $('.language').on('change', function (e) {
                var language_id = $(this).val();
                $.ajax({
                    url: base_url + 'get_tag_by_language',
                    type: "POST",
                    data: { language_id: language_id },
                    beforeSend: function () {
                      $('.select_tag_id').html('Please wait..');
                    },
                    success: function (result) {
                        $('.select_tag_id').html(result);
                    }
                });
            });
            $('.youtube_url').on('change', function (e) {
                var url = $(this).val();
                if (url != undefined || url != '') {        
                    var regExp =  /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
                    var match = url.match(regExp);
                    console.log(match);
                    if (match && match[1].length == 11) {
                        $(this).closest(".video_youtube").find(".youtube_url_error").hide();
                        $(this).closest(".video_youtube").find(".youtube_url_error").text("");
                        // for edit form
                        $(this).closest(".evideo_youtube").find(".youtube_url_error").hide();
                        $(this).closest(".evideo_youtube").find(".youtube_url_error").text("");
                    } else {
                        $(this).closest(".video_youtube").find(".youtube_url_error").show();
                        $(this).closest(".video_youtube").find(".youtube_url_error").text("Please enter youtube url.");
                        $(this).val("");
                        // for edit form
                        $(this).closest(".evideo_youtube").find(".youtube_url_error").show();
                        $(this).closest(".evideo_youtube").find(".youtube_url_error").text("Please enter youtube url.");
                        
                    }
                }
            });
            $('.other_url').on('change', function (e) {
                var url = $(this).val();
                if (url != undefined || url != '') {        
                    var regExp =  /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
                    var match = url.match(regExp);
                    if (match && match[1].length == 11) {
                        $(this).closest(".video_other").find(".other_url_error").show();
                        $(this).closest(".video_other").find(".other_url_error").text("Youtube url is not valid for other url.");
                        $(this).val("");
                        // for edit form
                        $(this).closest(".evideo_other").find(".other_url_error").show();
                        $(this).closest(".evideo_other").find(".other_url_error").text("Youtube url is not valid for other url.");           
                    } else {
                        $(this).closest(".video_other").find(".other_url_error").hide();
                        $(this).closest(".video_other").find(".other_url_error").text("");
                        // for edit form
                        $(this).closest(".evideo_other").find(".other_url_error").hide();
                        $(this).closest(".evideo_other").find(".other_url_error").text("");
                        // Do anything for not being valid
                    }
                }
            });
            $('#filter_language').on('change', function (e) {
                var filter_language = $('#filter_language').val();
                console.log(filter_language);
                $.ajax({
                    url: base_url + 'get_category_by_language',
                    type: "POST",
                    data: { language_id: filter_language },
                    beforeSend: function () {
                        $('#filter_category').html('Please wait..');
                    },
                    success: function (result) {
                        $('#filter_category').html(result);
                    }
                });
            });
            $('#filter_category').on('change', function (e) {
                var filter_category = $('#filter_category').val();
                $.ajax({
                    url: base_url + 'get_subcategory_by_category',
                    type: "POST",
                    data: { category_id: filter_category },
                    beforeSend: function () {
                        $('#filter_subcategory').html('Please wait..');
                    },
                    success: function (result) {
                        $('#filter_subcategory').html(result);
                    }
                });
            });
        </script>
        <script type="text/javascript">
            $('#filter_btn').on('click', function (e) {
                $('#news_list').bootstrapTable('refresh');
            });
            function queryParams(p) {
                return {
                    "category": $('#filter_category').val(),
                    "subcategory": $('#filter_subcategory').val(),
                    "language": $('#filter_language').val(),
                    "user": $('#filter_user').val(),
                    "role": $('#filter_role').val(),
                    "status": $('#filter_status').val(),
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
                    $("#image_url").val(row.image_url);
                    $("#edit_date").val(row.date1);
                    $("#edit_language").val(row.language_id);
                    <?php if (is_category_enabled() == 1) { ?>
                            $("#edit_category").val(row.category_id).trigger("change", [row.category_id, row.subcategory_id]);
                            setTimeout(function() { 
                            $("#edit_subcategory").val(row.subcategory_id);
                        }, 600);
                    <?php } ?>
                    
                    var valueArray = row.tag_id;
                    var arrayArea = valueArray.split(',');
                    var exampleMulti = $("#tag_id").select2({
                        placeholder: 'Select Tag'
                    });
                    exampleMulti.val(arrayArea).trigger("change");
                    $("#edit_title").val(row.title);
                    $("#edit_show_till").val(row.show_till);
                    var con_value = row.content_value;
                    $("#edit_content_type").val(row.content);
                    if (row.content == "standard_post") {
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
                    console.log(row);
                    $("input[name=status][value=1]").prop('checked', true);
                    if ($(row.status).text() == 'Deactive'){
                        $("input[name=status][value=0]").prop('checked', true);
                         $("#edit_is_notification").bootstrapSwitch('disabled',true);
                    }
                        
                        
                    $('.status').on('change', function (e) {
                        var status = $(this).val();
                        if(status == 0){
                             $("#edit_is_notification").bootstrapSwitch('disabled',true);
                        }else{
                             $('#edit_is_notification').bootstrapSwitch('disabled', false);
                        }
                    });
                    
                
                     $.ajax({
                    url: base_url + 'get_tag_by_language',
                    type: "POST",
                    data: { language_id: row.language_id },
                    beforeSend: function () {
                      $('.edit_select_tag_id').html('Please wait..');
                    },
                    success: function (result) {
                        $('.edit_select_tag_id').html(result);
                    }
                });
                },
                'click .edit-data-des': function (e, value, row, index) {
                    $('#edit_des_id').val(row.id);
                    var des1 = tinyMCE.get('edit_des').setContent(row.description);
                    $('#edit_des').val(des1);
                }
            };
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
        <script type="text/javascript">
            $(document).ready(function () {
                var date = new Date();
                var day = date.getDate();
                var month = date.getMonth() + 1;
                var year = date.getFullYear();
                if (month < 10)
                    month = "0" + month;
                if (day < 10)
                    day = "0" + day;
                var today = year + "-" + month + "-" + day;
            });
        </script>
        <script>
            $(document).on('click', '.delete-data', function () {
                if (confirm('Are you sure? Want to delete news?')) {
                    var base_url = "<?= APP_URL ?>";
                    var id = $(this).data("id");
                    var image = $(this).data("image");
                    var con_value = $(this).data("cvalue");
                    $.ajax({
                        url: base_url + 'delete_news',
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
            //Clone News
            $(document).on('click', '.clone-data', function () {
                var base_url = "<?= APP_URL ?>";
                var id = $(this).data("id");
                var image = $(this).data("image");
                var con_value = $(this).data("cvalue");
                $.ajax({
                    url: base_url + 'clone_news',
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
            $("#exampleInputFile2").change(function (e) {
                var file, img;
                if ((file = this.files[0])) {
                    img = new Image();
                    img.onerror = function () {
                        $('#exampleInputFile2').val('');
                        $('#image2').html('');
                        $('#img_error_msg2').html("Invalid Image Type");
                        $('#img_error_msg2').show().delay(3000).fadeOut();
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
            $("#exampleVideoInputFile1_edit").change(function (e) {
                var fileInput = document.getElementById('exampleVideoInputFile1_edit');
                var filePath = fileInput.value;
                var allowedExtensions = /(\.mp4|\.m4a|\.mkv|\.avi)$/i;
                if (!allowedExtensions.exec(filePath)) {
                    $('#exampleVideoInputFile1_edit').val('');
                    $('#video_edit').html('');
                    $('#video_error_msg_edit').html("Invalid Type");
                    $('#video_error_msg_edit').show().delay(3000).fadeOut();
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
                    selector: "#des, #edit_des",
                    height: 300,
                    plugins: [
                        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                        'insertdatetime', 'media', 'table', 'wordcount'
                    ],
                    toolbar: 'undo redo | blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify bullist numlist outdent indent removeformat link image media',
                    image_uploadtab: false,
                    images_upload_url: base_url + "upload_img",
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
                                    url: base_url + "upload_img",
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
                                    callback("public/images/news/" + filename);
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
    </body>

</html>