<!DOCTYPE html>
<html lang="en">

    <head>
        <title>Featured Sections |
            <?=($app_name) ? $app_name[0]->message : '' ?>
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
                                <h1>Create and Manage Featured Sections</h1>
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
                                        <h3 class="card-title">Add Featured Sections</h3>
                                    </div>
                                    <form action="<?= APP_URL ?>store_featured_sections" role="form" id="insert_form"
                                        method="POST" enctype="multipart/form-data">
                                        <?= csrf_field() ?>
                                        <div class="card-body">
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <label>Language</label>
                                                    <select id="language_id" name="language_id"
                                                        class="form-control language_id" required>
                                                        <option value="1" selected disabled>Select Language</option>
                                                        <?php foreach ($languages as $row) { ?>
                                                            <option value="<?php echo $row->id; ?>">
                                                                <?php echo $row->language; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Section Title</label>
                                                    <input type="text" name="title" class="form-control"
                                                        placeholder="Section title" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-6">
                                                    <label>Short Description</label>
                                                    <input type="text" name="short_description" class="form-control"
                                                        placeholder="Short Description" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Based on choice (Display news based on user's Preference)</label>
                                                    <div>
                                                        <input type="checkbox" id="is_based_on_user_choice" class="is_based_on_user_choice" name="is_based_on_user_choice" data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                                        <input type="hidden" id="based_on_user_choice_mode" class="based_on_user_choice_mode" name="based_on_user_choice_mode" value="0">
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="filter_section">
                                                <div class="form-group row">
                                                    <div class="col-sm-3">
                                                        <label>Type of News</label>
                                                        <select id="news_type" name="news_type"
                                                            class="form-control news_type" required>
                                                            <option value="" selected disabled>Select News Type</option>
                                                            <option value="news">News</option>
                                                            <?php if (is_breaking_news_enabled() == 1) { ?>
                                                                <option value="breaking_news">Breaking News</option>
                                                            <?php } ?>
                                                            <option value="videos">Videos</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <span class="videos">
                                                            <label>Which videos want to show?</label>
                                                            <select id="videos_type" name="videos_type"
                                                                class="form-control videos_type" required>
                                                                <option value="" selected disabled>Select Video Type
                                                                </option>
                                                                <option value="news">News</option>
                                                                <?php if (is_breaking_news_enabled() == 1) { ?>
                                                                    <option value="breaking_news">Breaking News</option>
                                                                <?php } ?>
                                                            </select>
                                                        </span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label>Type of Filter</label>
                                                        <select id="filter_type" name="filter_type"
                                                            class="form-control filter_type" required>
                                                            <option value="" selected disabled>Select Filter Type</option>
                                                            <option value="most_commented" class="most_commented">Most Commented</option>
                                                            <option value="recently_added">Recently Added</option>
                                                            <option value="most_viewed">Most Viewed</option>
                                                            <option value="most_favorite" class="most_favorite">Most Favorite</option>
                                                            <option value="most_like" class="most_like">Most Like</option>
                                                            <option value="custom">Custom</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <?php if (is_category_enabled() == 1) { ?>
                                                        <?php if (is_subcategory_enabled() == 1) { ?>
                                                            <div class="col-sm-12 filter_news">
                                                                <label>Category</label>
                                                                <select id="category_ids" name="category_ids[]"
                                                                    class="form-control select2 category_ids" multiple="multiple">
                                                                </select>
                                                            </div>
                                                        <?php } ?>
                                                    <?php } ?>
                                                    <div class="col-sm-12 custom">
                                                        <label>News</label>
                                                        <select id="news_ids" name="news_ids[]"
                                                            class="form-control select2 news_ids" required
                                                            multiple="multiple">
                                                            <option value="">Select News</option>
                                                            <?php foreach ($news as $news1): ?>
                                                                <option value="<?= $news1['id']; ?>">
                                                                    <?= $news1['title']; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-6 row">
                                                    <div class="col-sm-12">
                                                        <label>Select Style for APP Section</label>
                                                    </div>
                                                    <div class="col-md-2 col-sm-2">
                                                        <label class="radio-img">
                                                            <input type="radio" name="style_app" value="style_1"
                                                                class="form-control" required />
                                                            <img src="<?= APP_URL ?>public/images/app_style/App_Style_1.png"
                                                                alt="style_1" class="style_image">
                                                        </label>
                                                    </div>
                                                    <div class="col-md-2 col-sm-2">
                                                        <label class="radio-img">
                                                            <input type="radio" name="style_app" value="style_2" />
                                                            <img src="<?= APP_URL ?>public/images/app_style/App_Style_2.png"
                                                                alt="style_2" class="style_image">
                                                        </label>
                                                    </div>
                                                    <div class="col-md-2 col-sm-2">
                                                        <label class="radio-img">
                                                            <input type="radio" name="style_app" value="style_3" />
                                                            <img src="<?= APP_URL ?>public/images/app_style/App_Style_3.png"
                                                                alt="style_3" class="style_image">
                                                        </label>
                                                    </div>
                                                    <div class="col-md-2 col-sm-2">
                                                        <label class="radio-img">
                                                            <input type="radio" name="style_app" value="style_4" />
                                                            <img src="<?= APP_URL ?>public/images/app_style/App_Style_4.png"
                                                                alt="style_4" class="style_image">
                                                        </label>
                                                    </div>
                                                    <div class="col-md-2 col-sm-2">
                                                        <label class="radio-img">
                                                            <input type="radio" name="style_app" value="style_5" />
                                                            <img src="<?= APP_URL ?>public/images/app_style/App_Style_5.png"
                                                                alt="style_5" class="style_image">
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 row">
                                                    <div class="col-sm-12">
                                                        <label>Select Style for Web Section</label>
                                                    </div>
                                                    <div class="col-md-2 col-sm-2">
                                                        <label class="radio-img-web">
                                                            <input type="radio" name="style_web" value="style_1"
                                                                class="form-control" required />
                                                            <img src="<?= APP_URL ?>public/images/app_style/Web_Style_1.png"
                                                                alt="style_1" class="style_image">
                                                        </label>
                                                    </div>
                                                    <div class="col-md-2 col-sm-2">
                                                        <label class="radio-img-web">
                                                            <input type="radio" name="style_web" value="style_2" />
                                                            <img src="<?= APP_URL ?>public/images/app_style/Web_Style_2.png"
                                                                alt="style_2" class="style_image">
                                                        </label>
                                                    </div>
                                                    <div class="col-md-2 col-sm-2">
                                                        <label class="radio-img-web">
                                                            <input type="radio" name="style_web" value="style_3" />
                                                            <img src="<?= APP_URL ?>public/images/app_style/Web_Style_3.png"
                                                                alt="style_3" class="style_image">
                                                        </label>
                                                    </div>
                                                    <div class="col-md-2 col-sm-2">
                                                        <label class="radio-img-web">
                                                            <input type="radio" name="style_web" value="style_4" />
                                                            <img src="<?= APP_URL ?>public/images/app_style/Web_Style_4.png"
                                                                alt="style_4" class="style_image">
                                                        </label>
                                                    </div>
                                                    <div class="col-md-2 col-sm-2">
                                                        <label class="radio-img-web">
                                                            <input type="radio" name="style_web" value="style_5" />
                                                            <img src="<?= APP_URL ?>public/images/app_style/Web_Style_5.png"
                                                                alt="style_5" class="style_image">
                                                        </label>
                                                    </div>
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
                                        <h3 class="card-title">Featured Sections <small> View / Update / Delete</small>
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-1">
                                            <p id="delete_msg" style="display:none;" class="alert alert-success"></p>
                                        </div>
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
                                                <select id="filter_status" name="status" class="form-control">
                                                    <option value="">Status</option>
                                                    <option value="1">Active</option>
                                                    <option value="0">Deactive</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-2">
                                                <button class='btn btn-primary btn-block' id='filter_btn'>Filter Data</button>
                                            </div>
                                        </div>
                                        <table aria-describedby="mydesc" class='table-striped'
                                            id='featured_sections_list' data-toggle="table"
                                            data-url="<?= APP_URL . 'Table/featured_sections' ?>"
                                            data-click-to-select="true" data-side-pagination="server"
                                            data-pagination="false" data-page-list="[5, 10, 20, 50, 100, 200]"
                                            data-search="true" data-toolbar="#toolbar" data-show-columns="true"
                                            data-show-refresh="true" data-fixed-columns="true" data-fixed-number="1"
                                            data-fixed-right-number="1" data-trim-on-search="false"
                                            data-mobile-responsive="true" data-sort-name="row_order"
                                            data-sort-order="asc" data-maintain-selected="true"
                                            data-export-types='["txt","excel"]'
                                            data-export-options='{ "fileName": "featured-sections-list-<?= date('d-m-y') ?>" }'
                                            data-query-params="queryParams">
                                            <thead>
                                                <tr>
                                                    <th scope="col" data-field="id" data-sortable="true">ID</th>
                                                    <th scope="col" data-field="title" data-sortable="false">Title</th>
                                                    <th scope="col" data-field="short_description" data-visible="false" data-sortable="false">Short Description</th>
                                                    <th scope="col" data-field="news_type" data-sortable="false">News Type</th>
                                                    <th scope="col" data-field="videos_type" data-sortable="false">VideosType </th>
                                                    <th scope="col" data-field="filter_type" data-sortable="false">Filter Type</th>
                                                    <th scope="col" data-field="style_app" data-sortable="false">App Style</th>
                                                    <th scope="col" data-field="style_web" data-sortable="false">Web Style</th>
                                                    <th scope="col" data-field="date" data-sortable="false">Date</th>
                                                    <th scope="col" data-field="language" data-sortable="false">Language</th>
                                                    <th scope="col" data-field="status" data-sortable="false">Status</th>
                                                    <th scope="col" data-field="operate" data-sortable="false" data-events="actionEvents">Operate</th>
                                                    <th scope="col" data-field="row_order" data-sortable="true">Order</th>
                                                </tr>
                                            </thead>
                                            <tbody class="rearrange">
                                            </tbody>
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
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Edit Featured Sections</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="<?= APP_URL ?>update_featured_sections" role="form" id="update_form" method="POST"
                            enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <input type='hidden' name="edit_id" id="edit_id" value='' />
                            <div class="modal-body">
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Language</label>
                                        <select id="edit_language_id" name="language_id"
                                            class="form-control language_id" required>
                                            <option value="1" selected disabled>Select Language</option>
                                            <?php foreach ($languages as $row) { ?>
                                                <option value="<?php echo $row->id; ?>">
                                                    <?php echo $row->language; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Section Title</label>
                                        <input type="text" id="edit_title" name="title" class="form-control"
                                            placeholder="Section title" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label>Short Description</label>
                                        <input type="text" id="edit_short_description" name="short_description"
                                            class="form-control" placeholder="Short Description" required>
                                    </div>
                                    <div class="col-md-6">
                                    <label>Based on choice (Display news based on user's Preference)</label>
                                        <div>
                                            <input type="checkbox" id="edit_is_based_on_user_choice" class="edit_is_based_on_user_choice" name="edit_is_based_on_user_choice" data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                            <input type="hidden" id="edit_based_on_user_choice_mode" class="edit_based_on_user_choice_mode" name="edit_based_on_user_choice_mode" value="1">
                                        </div>
                                    </div>
                                </div>
                                <div id="edit_filter_section">
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label>Type of News</label>
                                        <select id="edit_news_type" name="news_type" class="form-control news_type"
                                            required>
                                            <option value="news">News</option>
                                            <?php if (is_breaking_news_enabled() == 1) { ?>
                                                <option value="breaking_news">Breaking News</option>
                                            <?php } ?>
                                            <option value="videos">Videos</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <span class="videos">
                                            <label>Which videos want to show?</label>
                                            <select id="edit_videos_type" name="videos_type"
                                                class="form-control videos_type" required>
                                                <option value="">Select</option>
                                                <option value="news">News</option>
                                                <?php if (is_breaking_news_enabled() == 1) { ?>
                                                    <option value="breaking_news">Breaking News</option>
                                                <?php } ?>
                                            </select>
                                        </span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label>Type of Filter</label>
                                        <select id="edit_filter_type" name="filter_type"
                                            class="form-control filter_type" required>
                                            <option value="" selected disabled>Select Filter Type</option>
                                            <?php if (is_breaking_news_enabled() == 1) { ?>
                                                <option value="most_commented" class="most_commented">Most Commented</option>
                                            <?php } ?>
                                            <option value="recently_added">Recently Added</option>
                                            <option value="most_viewed">Most Viewed</option>
                                            <option value="most_favorite" class="most_favorite">Most Favorite</option>
                                            <option value="most_like" class="most_like">Most Like</option>
                                            <option value="custom">Custom</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <?php if (is_category_enabled() == 1) { ?>
                                        <?php if (is_subcategory_enabled() == 1) { ?>
                                            <div class="col-sm-12 filter_news">
                                                <label>Category</label>
                                                <select id="edit_category_ids" name="category_ids[]"
                                                    class="form-control select2 category_ids" multiple="multiple">
                                                    <?php foreach ($cats as $cat): ?>
                                                        <option value="cat-<?= $cat['id']; ?>">
                                                            <?= $cat['category_name']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                    <div class="col-sm-12 custom">
                                        <label>News</label>
                                        <select id="edit_news_ids" name="news_ids[]"
                                            class="form-control select2 news_ids" required multiple="multiple">
                                            <option value="">Select News</option>
                                            <?php foreach ($news as $news1): ?>
                                                <option value="<?= $news1['id']; ?>">
                                                    <?= $news1['title']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6 row">
                                        <div class="col-lg-12">
                                            <label>Select Style for APP Section</label>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="radio-img edit-radio-img">
                                                <input type="radio" name="style_app" value="style_1" required />
                                                <img src="<?= APP_URL ?>public/images/app_style/App_Style_1.png"
                                                    alt="style_1" class="style_image">
                                            </label>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="radio-img edit-radio-img">
                                                <input type="radio" name="style_app" value="style_2" />
                                                <img src="<?= APP_URL ?>public/images/app_style/App_Style_2.png"
                                                    alt="style_2" class="style_image">
                                            </label>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="radio-img edit-radio-img">
                                                <input type="radio" name="style_app" value="style_3" />
                                                <img src="<?= APP_URL ?>public/images/app_style/App_Style_3.png"
                                                    alt="style_3" class="style_image">
                                            </label>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="radio-img edit-radio-img">
                                                <input type="radio" name="style_app" value="style_4" />
                                                <img src="<?= APP_URL ?>public/images/app_style/App_Style_4.png"
                                                    alt="style_4" class="style_image">
                                            </label>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="radio-img edit-radio-img">
                                                <input type="radio" name="style_app" value="style_5" />
                                                <img src="<?= APP_URL ?>public/images/app_style/App_Style_5.png"
                                                    alt="style_5" class="style_image">
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 row">
                                        <div class="col-lg-12">
                                            <label>Select Style for APP Section</label>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="radio-img edit-radio-img-web">
                                                <input type="radio" name="style_web" value="style_1" required />
                                                <img src="<?= APP_URL ?>public/images/app_style/Web_Style_1.png"
                                                    alt="style_1" class="style_image">
                                            </label>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="radio-img edit-radio-img-web">
                                                <input type="radio" name="style_web" value="style_2" />
                                                <img src="<?= APP_URL ?>public/images/app_style/Web_Style_2.png"
                                                    alt="style_2" class="style_image">
                                            </label>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="radio-img edit-radio-img-web">
                                                <input type="radio" name="style_web" value="style_3" />
                                                <img src="<?= APP_URL ?>public/images/app_style/Web_Style_3.png"
                                                    alt="style_3" class="style_image">
                                            </label>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="radio-img edit-radio-img-web">
                                                <input type="radio" name="style_web" value="style_4" />
                                                <img src="<?= APP_URL ?>public/images/app_style/Web_Style_4.png"
                                                    alt="style_4" class="style_image">
                                            </label>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="radio-img edit-radio-img-web">
                                                <input type="radio" name="style_web" value="style_5" />
                                                <img src="<?= APP_URL ?>public/images/app_style/Web_Style_5.png"
                                                    alt="style_5" class="style_image">
                                            </label>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <label class="control-label">Status</label>
                                    <div id="status1" class="btn-group">
                                        <label class="btn btn-success" data-toggle-class="btn-primary"
                                            data-toggle-passive-class="btn-default">
                                            <input class="status" type="radio" name="status"
                                                value="1"> Active
                                        </label>
                                        <label class="btn btn-danger" data-toggle-class="btn-primary"
                                            data-toggle-passive-class="btn-default">
                                            <input class="status" type="radio" name="status"
                                                value="0"> Deactive
                                        </label>
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
        <script>
            $(function () {
                $('.category_ids').select2({
                    placeholder: 'Select Category'
                });
                $('.news_ids').select2({
                    placeholder: 'Select News'
                });
            });
        </script>
        <script type="text/javascript">
            $('#filter_btn').on('click', function (e) {
                $('#featured_sections_list').bootstrapTable('refresh');
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
                    $('#edit_id').val(row.id);
                    $("#edit_title").val(row.title);
                    $("#edit_short_description").val(row.short_description);
                    $("#edit_language_id").val(row.language_id);
                    $("#edit_news_type").val(row.news_type);
                    if (row.news_type == "videos") {
                        $('.videos').show();
                        $("#edit_videos_type").val(row.videos_type);
                    } else {
                        $('.videos').hide();
                    }
                    if (row.news_type == "breaking_news" || row.videos_type == "breaking_news") {
                        $('.most_commented').hide();
                        $('.most_like').hide();
                        $('.most_favorite').hide();
                    } else {
                        $('.filter_news').show();
                        $('.most_commented').show();
                        $('.most_like').show();
                        $('.most_favorite').show();
                    }

                    $("input[name=status][value=1]").prop('checked', true);
                    if ($(row.status).text() == 'Deactive')
                        $("input[name=status][value=0]").prop('checked', true);

                
                    $("#edit_is_based_on_user_choice").bootstrapSwitch();
                    var edit_is_based_on_user_choice = document.querySelector('#edit_is_based_on_user_choice');
                    if (row.is_based_on_user_choice == '1') {
                        setTimeout(function() { 
                            $("#edit_is_based_on_user_choice").bootstrapSwitch('state', true);
                        }, 600);
                        $('#edit_based_on_user_choice_mode').val(1);
                    }else{
                        $("#edit_is_based_on_user_choice").bootstrapSwitch('state', false);
                    }
                
                    var app_style = row.style_app_edit;
                    $("input[name=style_app][value=" + app_style + "]").prop('checked', true);
                    
                    var language_id = row.language_id;
                    $.ajax({
                        url: base_url + 'get_categories_tree',
                        type: "POST",
                        data: { language_id: language_id },
                        
                        success: function (result) {
                            $('#category_ids').html(result);
                            $('#edit_category_ids').html(result);
                            $("#edit_filter_type").val(row.filter_type);
                            if (row.filter_type == "custom") {
                                $('.filter_news').hide();
                                $('.custom').show();
                                var valueArray = row.news_ids;
                                if(valueArray){
                                    var arrayArea = valueArray.split(',');
                                }
                                var exampleMulti = $("#edit_news_ids").select2({
                                    placeholder: 'Select News'
                                });
                                exampleMulti.val(arrayArea).trigger("change");
                            } else {
                                $('.filter_news').show();
                                $('.custom').hide();
                                var category_idArray = row.category_ids;
                                var subcategory_idArray = row.subcategory_ids;
                                if(category_idArray || subcategory_idArray){
                                    if(category_idArray){
                                        var category_idArea = category_idArray.split(',');
                                        var prefix = 'cat-';
                                        var category_idAreaprefix = category_idArea.map(el => prefix + el);
                                        var category_idMulti = $("#edit_category_ids").select2({
                                            placeholder: 'Select Category',
                                        });
                                    }
                                
                                    if(subcategory_idArray){
                                        var subcategory_idArea = subcategory_idArray.split(',');
                                        var prefix = 'subcat-';
                                        var subcategory_idAreaprefix = subcategory_idArea.map(el => prefix + el);
                                        var merge = $.merge(category_idAreaprefix,subcategory_idAreaprefix);
                                        console.log(merge);
                                        var subcategory_idMulti = $("#edit_category_ids").select2({
                                            placeholder: 'Select Category',
                                        });
                                    }
                                category_idMulti.val(category_idAreaprefix).trigger("change");
                                }
                            }
                        }
                    });
                    var web_style = row.style_web_edit;
                    $("input[name=style_web][value=" + web_style + "]").prop('checked', true);

                    
               
                },
            };
        </script>
        <script type="text/javascript">
            $('.videos').hide();
            $('.custom').hide();
            $(document).ready(function () {
                $('.news_type').on('change', function (e) {
                    var news_type = $(this).val();
                    console.log(news_type);
                    if (news_type == "videos") {
                        $('.videos').show();
                        $('.videos_type').on('change', function (e) {
                            var videos = $(this).val();
                            if (videos == "breaking_news") {
                                $('.most_commented').hide();
                                $('.most_like').hide();
                                $('.most_favorite').hide();
                            }
                            else{
                                $('.most_commented').show();
                                $('.most_like').show(); 
                                $('.most_favorite').show();
                            }
                        });

                    } else {
                        $('.videos').hide();
                    }
                    if (news_type == "breaking_news") {
                        $('.most_commented').hide();
                        $('.most_like').hide();
                        $('.most_favorite').hide();
                    } else {
                        $('.filter_news').show();
                        $('.most_commented').show();
                        $('.most_like').show();
                        $('.most_favorite').show();
                    }

                });
            });
        </script>
        <script type="text/javascript">
            //display categories, news based on language
            var base_url = "<?= APP_URL ?>";
            $('.language_id').on('change', function (e) {
                var language_id = $(this).val();
                console.log(language_id);
                $.ajax({
                    url: base_url + 'get_categories_tree',
                    type: "POST",
                    data: { language_id: language_id },
                    beforeSend: function () {
                        $('#category_ids').html('Please wait..');
                    },
                    success: function (result) {
                        $('#category_ids').html(result);
                        $('#edit_category_ids').html(result);
                    }
                });
            });
           
            $('.filter_type').on('change', function (e) {
                var filter_type = $(this).val();
                var news_type = $(this).closest('form').find('.news_type').val();

                if (news_type == 'news' || news_type == 'videos') {
                    if (filter_type == "custom") {
                        $('.custom').show();
                        $('.filter_news').hide();
                    } else {
                        $('.filter_news').show();
                        $('.custom').hide();
                    }
                }
                else {
                    if (filter_type == "custom") {
                        $('.custom').show();
                        $('.filter_news').hide();
                    } else {
                        $('.filter_news').hide();
                        $('.custom').hide();
                    }
                }
                var filter_type = $('.filter_type').val();
                var news_type = $('.news_type').val();
                var videos_type = $('.videos_type').val();
                var language_id = $('.language_id').val();
                if (filter_type == 'custom') {
                    $.ajax({
                        url: base_url + 'get_custom_news',
                        type: "POST",
                        data: { language_id: language_id, news_type: news_type, videos_type: videos_type },
                        beforeSend: function () {
                            $('#news_ids').html('Please wait..');
                        },
                        success: function (result) {
                            console.log(result);
                            $('#news_ids').html(result);
                            $('#edit_news_ids').html(result);
                        }
                    });
                }
            });

            $('.language_id, .news_type, .videos_type').on('change', function (e) {
                $('.filter_type').prop('selected', false).find('option:first').prop('selected', true);
            });
        </script>
         <script type="text/javascript">
             /* switchery js */
            $("input[data-bootstrap-switch]").bootstrapSwitch(); 
        </script>
        <script type="text/javascript">
            /* on change of based_on_choice_mode mode btn - switchery js */
            var is_based_on_user_choice = document.querySelector('#is_based_on_user_choice');
            is_based_on_user_choice.onchange = function () {
                if (is_based_on_user_choice.checked) {
                    $('#based_on_user_choice_mode').val(1);
                    $('#filter_section').hide();
                } else {
                    $('#based_on_user_choice_mode').val(0);
                    $('#filter_section').show();
                }
            };

            /* on change of edit_based_on_choice_mode mode btn - switchery js */
            var edit_is_based_on_user_choice = document.querySelector('#edit_is_based_on_user_choice');
            edit_is_based_on_user_choice.onchange = function () {
                if (edit_is_based_on_user_choice.checked) {
                    $('#edit_based_on_user_choice_mode').val(1);
                   
                    $('#edit_filter_section').hide();
                    $('#edit_news_type').val('');
                    $('#edit_videos_type').val('');
                    $('#edit_filter_type').val('');
                    $('#edit_category_ids').val('');
                    $('#edit_news_ids').val('');
                } else {
                    $('#edit_based_on_user_choice_mode').val(0);
                    $('#edit_filter_section').show();
                }
            };
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
                        url: base_url + 'delete_featured_sections',
                        type: "POST",
                        dataType: "json",
                        data: { id: id },
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
        <script type="text/javascript">
            $(".rearrange").sortable({
                delay: 150,
                stop: function () {
                    var selectedData = new Array();
                    $(".rearrange>tr").each(function () {
                        selectedData.push($(this).find("td:first").text());
                    });
                    updateOrder(selectedData);
                }
            });
            function updateOrder(aData) {
                $.ajax({
                    url: base_url + 'update_featured_sections_order',
                    type: 'POST',
                    data: {
                        allData: aData
                    },
                    success: function () {
                        $('#featured_sections_list').bootstrapTable('refresh');
                    }
                });
            }
        </script>
    </body>

</html>