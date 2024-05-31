<!DOCTYPE html>
<html lang="en">
<head>
    <title>System Configurations | <?=($app_name) ? $app_name[0]->message : '' ?></title>
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
                                    <h3 class="card-title">System Settings for App <small> Note that this will directly reflect the changes in App</small></h3>
                                </div>
                                <form action="<?= base_url(); ?>/store_system_setting" role="form" id="insert_form" method="POST" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <div class="card-body">
                                        <div class="form-group row">
                                            <div class="col-sm-12">
                                                <label>System Timezone</label>
                                                <?php $options = getTimezoneOptions(); ?>
                                                <select id="system_timezone" name="system_timezone" required class="form-control">
                                                    <?php foreach ($options as $option) { ?>
                                                        <option value="<?= $option[2] ?>" data-gmt="<?= $option['1']; ?>" <?=($system_timezone) ? (($system_timezone[0]->message == $option[2]) ? 'selected' : '') : ''; ?>><?= $option[2] ?> - GMT <?= $option[1] ?> - <?= $option[0] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>App Name</label>
                                                <input type="text" name="app_name" value="<?=($app_name) ? $app_name[0]->message : '' ?>" class="form-control" placeholder="App Name" required />
                                            </div>
                                            <div class="col-sm-6">
                                                <label>JWT KEY</label>
                                                <input type="text" name="jwt_key" value="<?=($jwt_key) ? $jwt_key[0]->message : '' ?>" class="form-control" placeholder="JWT KEY" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>App Full Logo <small class="text-danger">( size 460 * 115 )</small></label>
                                                <div class="custom-file">
                                                    <input name="file1" type="file" class="custom-file-input" id="exampleInputFile2">
                                                    <label class="custom-file-label" id="custom-file-label1" for="customFile">Choose file</label>
                                                    <small class="text-danger">Only png, jpg, webp, gif and jpeg image allow</small>
                                                    <p style="display: none" id="img_error_msg1" class="alert alert-danger"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label>App Half Logo <small class="text-danger">( size 128 * 128 )</small></label>
                                                <div class="custom-file">
                                                    <input name="file" type="file" class="custom-file-input" id="exampleInputFile1">
                                                    <label class="custom-file-label" id="custom-file-label" for="customFile">Choose file</label>
                                                    <small class="text-danger">Only png, jpg, webp, gif and jpeg image allow</small>
                                                    <p style="display: none" id="img_error_msg" class="alert alert-danger"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <?php if ($app_logo_full) { ?>
                                                    <img src="<?= APP_URL ?>public/images/<?= $app_logo_full[0]->message ?>" width="300" />
                                                <?php } ?>
                                            </div>
                                            <div class="col-sm-6">
                                                <?php if ($app_logo) { ?>
                                                    <img src="<?= APP_URL ?>public/images/<?= $app_logo[0]->message ?>" height="100" />
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-2">
                                                <label>Category</label>
                                                <div>
                                                    <input type="checkbox" id="is_category" name="is_category" <?php
                                                    if ($is_category) {
                                                        echo ($is_category[0]->message == '1') ? 'checked' : '';
                                                    }
                                                    ?> data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                                    <input type="hidden" id="category_mode" name="category_mode" value="<?=($is_category) ? $is_category[0]->message : 0; ?>">
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <label>Subcategory</label>
                                                <div>
                                                    <input type="checkbox" id="is_subcategory" name="is_subcategory" <?php
                                                    if ($is_subcategory) {
                                                        echo ($is_subcategory[0]->message == '1') ? 'checked' : '';
                                                    }
                                                    ?> data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                                    <input type="hidden" id="subcategory_mode" name="subcategory_mode" value="<?=($is_subcategory) ? $is_subcategory[0]->message : 0; ?>">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <label>Breaking News</label>
                                                <div>
                                                    <input type="checkbox" id="breaking_news" name="breaking_news" <?php
                                                    if ($is_news) {
                                                        echo ($is_news[0]->message == '1') ? 'checked' : '';
                                                    }
                                                    ?> data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                                    <input type="hidden" id="breaking_news_mode" name="breaking_news_mode" value="<?=($is_news) ? $is_news[0]->message : 0; ?>">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <label>Live Streaming</label>
                                                <div>
                                                    <input type="checkbox" id="is_live_streaming" name="is_live_streaming" <?php
                                                    if ($is_live_streaming) {
                                                        echo ($is_live_streaming[0]->message == '1') ? 'checked' : '';
                                                    }
                                                    ?> data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                                    <input type="hidden" id="live_streaming_mode" name="live_streaming_mode" value="<?=($is_live_streaming) ? $is_live_streaming[0]->message : 0; ?>">
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <label>Comments</label>
                                                <div>
                                                    <input type="checkbox" id="is_comments" name="is_comments" <?php
                                                    if ($is_comments) {
                                                        echo ($is_comments[0]->message == '1') ? 'checked' : '';
                                                    }
                                                    ?> data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                                    <input type="hidden" id="comments_mode" name="comments_mode" value="<?=($is_comments) ? $is_comments[0]->message : 0; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="form-group row">
                                            <h5 class="text-bold">System Settings for Android Ads. </h5>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-2 col-sm-6">
                                                <label>In App Ads.</label>
                                                <div>
                                                    <input type="checkbox" id="in_app_ads" name="in_app_ads" <?php
                                                    if ($in_app_ads_mode) {
                                                        echo ($in_app_ads_mode[0]->message == '1') ? 'checked' : '';
                                                    }
                                                    ?> data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                                    <input type="hidden" id="in_app_ads_mode" name="in_app_ads_mode" value="<?=($in_app_ads_mode) ? $in_app_ads_mode[0]->message : 0; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12 adsHide">
                                                <label class="control-label">&nbsp;</label>
                                                <div>
                                                    <div class="form-check">
                                                        <input type="radio" class="form-check-input" name="ads_type" value="1" required <?=(!empty($ads_type) && $ads_type[0]->message == "1") ? "checked" : '' ?>>
                                                        <label class="form-check-label mr-4">
                                                            Google AdMob
                                                        </label>
                                                        <input type="radio" class="form-check-input" name="ads_type" value="2" required <?=(!empty($ads_type) && $ads_type[0]->message == "2") ? "checked" : '' ?>>
                                                        <label class="form-check-label mr-4">
                                                            Facebook Ads.
                                                        </label>
                                                        <input type="radio" class="form-check-input" name="ads_type" value="3" required <?=(!empty($ads_type) && $ads_type[0]->message == "3") ? "checked" : '' ?>>
                                                        <label class="form-check-label">
                                                            Unity Ads.
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="adsgoogle adsHide">
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <label>google Rewarded Video Id</label>
                                                    <input type="text" name="google_rewarded_video_id" value="<?=($google_rewarded_video_id) ? $google_rewarded_video_id[0]->message : '' ?>" class="form-control googleAtt" placeholder="google Rewarded Video Id" required />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label>google Interstitial Id</label>
                                                    <input type="text" name="google_interstitial_id" value="<?=($google_interstitial_id) ? $google_interstitial_id[0]->message : '' ?>" class="form-control googleAtt" placeholder="google Interstitial Id" required />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <label>google Banner Id</label>
                                                    <input type="text" name="google_banner_id" value="<?=($google_banner_id) ? $google_banner_id[0]->message : '' ?>" class="form-control googleAtt" placeholder="google Banner Id" required />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label>google Native Unit Id</label>
                                                    <input type="text" name="google_native_unit_id" value="<?=($google_native_unit_id) ? $google_native_unit_id[0]->message : '' ?>" class="form-control googleAtt" placeholder="google Native Unit Id" required />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="adsfacebook adsHide">
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <label>fb Rewarded Video Id</label>
                                                    <input type="text" name="fb_rewarded_video_id" value="<?=($fb_rewarded_video_id) ? $fb_rewarded_video_id[0]->message : '' ?>" class="form-control facebookAtt" placeholder="fb Rewarded Video Id" required />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label>fb Interstitial Id</label>
                                                    <input type="text" name="fb_interstitial_id" value="<?=($fb_interstitial_id) ? $fb_interstitial_id[0]->message : '' ?>" class="form-control facebookAtt" placeholder="fb Interstitial Id" required />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <label>fb Banner Id</label>
                                                    <input type="text" name="fb_banner_id" value="<?=($fb_banner_id) ? $fb_banner_id[0]->message : '' ?>" class="form-control facebookAtt" placeholder="fb Banner Id" required />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label>fb Native Unit Id</label>
                                                    <input type="text" name="fb_native_unit_id" value="<?=($fb_native_unit_id) ? $fb_native_unit_id[0]->message : '' ?>" class="form-control facebookAtt" placeholder="fb Native Unit Id" required />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="adsunity adsHide">
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <label>Unity Rewarded Video Id</label>
                                                    <input type="text" name="unity_rewarded_video_id" value="<?=($unity_rewarded_video_id) ? $unity_rewarded_video_id[0]->message : '' ?>" class="form-control unityAtt" placeholder="Unity Rewarded Video Id" required />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label>Unity Interstitial Id</label>
                                                    <input type="text" name="unity_interstitial_id" value="<?=($unity_interstitial_id) ? $unity_interstitial_id[0]->message : '' ?>" class="form-control unityAtt" placeholder="Unity Interstitial Id" required />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <label>Unity Banner Id</label>
                                                    <input type="text" name="unity_banner_id" value="<?=($unity_banner_id) ? $unity_banner_id[0]->message : '' ?>" class="form-control unityAtt" placeholder="Unity Banner Id" required />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label>Unity Game Id</label>
                                                    <input type="text" name="android_game_id" value="<?=($android_game_id) ? $android_game_id[0]->message : '' ?>" class="form-control unityAtt" placeholder="Unity Game Id" required />
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="form-group row">
                                            <h5 class="text-bold">System Settings for IOS Ads. </h5>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-2 col-sm-6">
                                                <label>In App Ads.</label>
                                                <div>
                                                    <input type="checkbox" id="ios_in_app_ads" name="ios_in_app_ads" <?php
                                                    if ($ios_in_app_ads_mode) {
                                                        echo ($ios_in_app_ads_mode[0]->message == '1') ? 'checked' : '';
                                                    }
                                                    ?> data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                                    <input type="hidden" id="ios_in_app_ads_mode" name="ios_in_app_ads_mode" value="<?=($ios_in_app_ads_mode) ? $ios_in_app_ads_mode[0]->message : 0; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12 iOSadsHide">
                                                <label class="control-label">&nbsp;</label>
                                                <div>
                                                    <div class="form-check">
                                                        <input type="radio" class="form-check-input" name="ios_ads_type" value="1" required <?=(!empty($ios_ads_type) && $ios_ads_type[0]->message == '1') ? 'checked' : '' ?>>
                                                        <label class="form-check-label mr-4">
                                                            Google AdMob
                                                        </label>
                                                        <input type="radio" class="form-check-input" name="ios_ads_type" value="2" required <?=(!empty($ios_ads_type) && $ios_ads_type[0]->message == '2') ? 'checked' : '' ?>>
                                                        <label class="form-check-label mr-4">
                                                            Facebook Ads.
                                                        </label>
                                                        <input type="radio" class="form-check-input" name="ios_ads_type" value="3" required <?=(!empty($ios_ads_type) && $ios_ads_type[0]->message == '3') ? 'checked' : '' ?>>
                                                        <label class="form-check-label">
                                                            Unity Ads.
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="iOSadsgoogle iOSadsHide">
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <label>google Rewarded Video Id</label>
                                                    <input type="text" name="ios_google_rewarded_video_id" value="<?=($ios_google_rewarded_video_id) ? $ios_google_rewarded_video_id[0]->message : '' ?>" class="form-control iOSgoogleAtt" placeholder="google Rewarded Video Id" required />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label>google Interstitial Id</label>
                                                    <input type="text" name="ios_google_interstitial_id" value="<?=($ios_google_interstitial_id) ? $ios_google_interstitial_id[0]->message : '' ?>" class="form-control iOSgoogleAtt" placeholder="google Interstitial Id" required />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <label>google Banner Id</label>
                                                    <input type="text" name="ios_google_banner_id" value="<?=($ios_google_banner_id) ? $ios_google_banner_id[0]->message : '' ?>" class="form-control iOSgoogleAtt" placeholder="google Banner Id" required />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label>google Native Unit Id</label>
                                                    <input type="text" name="ios_google_native_unit_id" value="<?=($ios_google_native_unit_id) ? $ios_google_native_unit_id[0]->message : '' ?>" class="form-control iOSgoogleAtt" placeholder="google Native Unit Id" required />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="iOSadsfacebook iOSadsHide">
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <label>fb Rewarded Video Id</label>
                                                    <input type="text" name="ios_fb_rewarded_video_id" value="<?=($ios_fb_rewarded_video_id) ? $ios_fb_rewarded_video_id[0]->message : '' ?>" class="form-control iOSfacebookAtt" placeholder="fb Rewarded Video Id" required />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label>fb Interstitial Id</label>
                                                    <input type="text" name="ios_fb_interstitial_id" value="<?=($ios_fb_interstitial_id) ? $ios_fb_interstitial_id[0]->message : '' ?>" class="form-control iOSfacebookAtt" placeholder="fb Interstitial Id" required />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <label>fb Banner Id</label>
                                                    <input type="text" name="ios_fb_banner_id" value="<?=($ios_fb_banner_id) ? $ios_fb_banner_id[0]->message : '' ?>" class="form-control iOSfacebookAtt" placeholder="fb Banner Id" required />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label>fb Native Unit Id</label>
                                                    <input type="text" name="ios_fb_native_unit_id" value="<?=($ios_fb_native_unit_id) ? $ios_fb_native_unit_id[0]->message : '' ?>" class="form-control iOSfacebookAtt" placeholder="fb Native Unit Id" required />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="iOSadsunity iOSadsHide">
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <label>Unity Rewarded Video Id</label>
                                                    <input type="text" name="ios_unity_rewarded_video_id" value="<?=($ios_unity_rewarded_video_id) ? $ios_unity_rewarded_video_id[0]->message : '' ?>" class="form-control iOSunityAtt" placeholder="Unity Rewarded Video Id" required />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label>Unity Interstitial Id</label>
                                                    <input type="text" name="ios_unity_interstitial_id" value="<?=($ios_unity_interstitial_id) ? $ios_unity_interstitial_id[0]->message : '' ?>" class="form-control iOSunityAtt" placeholder="Unity Interstitial Id" required />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <label>Unity Banner Id</label>
                                                    <input type="text" name="ios_unity_banner_id" value="<?=($ios_unity_banner_id) ? $ios_unity_banner_id[0]->message : '' ?>" class="form-control iOSunityAtt" placeholder="Unity Banner Id" required />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label>Unity Game Id</label>
                                                    <input type="text" name="ios_game_id" value="<?=($ios_game_id) ? $ios_game_id[0]->message : '' ?>" class="form-control iOSunityAtt" placeholder="Unity Game Id" required />
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <!-- Default language -->
                                        <input type="hidden" id="default_language" name="default_language" value="<?=($default_language) ? $default_language[0]->message : 0; ?>">
                                        <!-- Auto News Deletion -->
                                        <div class="form-group row">
                                            <h5 class="text-bold">Auto News Deletion</h5>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-2 col-sm-6">
                                                <label>Auto delete expire news</label>
                                                <div>
                                                    <input type="checkbox" id="auto_delete_expire_news" name="auto_delete_expire_news" <?php
                                                    if ($auto_delete_expire_news_mode) {
                                                        echo ($auto_delete_expire_news_mode[0]->message == '1') ? 'checked' : '';
                                                    }
                                                    ?> data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                                    <input type="hidden" id="auto_delete_expire_news_mode" name="auto_delete_expire_news_mode" value="<?=($auto_delete_expire_news_mode) ? $auto_delete_expire_news_mode[0]->message : 0; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <!-- Email Setting -->
                                        <div class="form-group row">
                                            <h5 class="text-bold">Email SMTP Setting <small>(Email setting for forgot password of admin)</small></h5>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-4">
                                                <label>SMTP Host</label>
                                                <input type="text" name="smtp_host" value="<?=($smtp_host) ? $smtp_host[0]->message : '' ?>" class="form-control" placeholder="SMTP Host" required />
                                            </div>
                                            <div class="col-sm-4">
                                                <label>SMTP User</label>
                                                <input type="text" name="smtp_user" value="<?=($smtp_user) ? $smtp_user[0]->message : '' ?>" class="form-control" placeholder="SMTP User" required />
                                            </div>
                                            <div class="col-sm-4">
                                                <label>SMTP Password</label>
                                                <input type="text" name="smtp_password" value="<?=($smtp_password) ? $smtp_password[0]->message : '' ?>" class="form-control" placeholder="SMTP Password" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-4">
                                                <label>SMTP Port</label>
                                                <input type="text" name="smtp_port" value="<?=($smtp_port) ? $smtp_port[0]->message : '' ?>" class="form-control" placeholder="SMTP Port" required />
                                            </div>
                                            <div class="col-sm-4">
                                                <label>SMTP Encryption</label>
                                                <select name="smtp_crypto" class="form-control" >
                                                    <option <?=($smtp_crypto) ? (($smtp_crypto[0]->message == 'ssl') ? 'selected' : '') : ''; ?>>ssl</option>
                                                    <option <?=($smtp_crypto) ? (($smtp_crypto[0]->message == 'tls') ? 'selected' : '') : ''; ?>>tls</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-4">
                                                <label>From Name</label>
                                                <input type="text" name="from_name" value="<?=($from_name) ? $from_name[0]->message : '' ?>" class="form-control" placeholder="From Name" required />
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
                    </div>
                </div>
            </section>
            <!-- /.content -->
        </div>
        <!-- /.modal -->
        <?php base_url() . include 'footer.php'; ?>
    </div>
    <!-- ./wrapper -->
    <?php
    function getTimezoneOptions()
    {
        $list = DateTimeZone::listAbbreviations();
        $idents = DateTimeZone::listIdentifiers();
        $data = $offset = $added = array();
        foreach ($list as $abbr => $info) {
            foreach ($info as $zone) {
                if (
                    !empty($zone['timezone_id'])
                    and !in_array($zone['timezone_id'], $added)
                    and
                    in_array($zone['timezone_id'], $idents)
                ) {
                    $z = new DateTimeZone($zone['timezone_id']);
                    $c = new DateTime($n = '', $z); // replace NULL by ($n='')
                    $zone['time'] = $c->format('H:i a');
                    $offset[] = $zone['offset'] = $z->getOffset($c);
                    $data[] = $zone;
                    $added[] = $zone['timezone_id'];
                }
            }
        }
        array_multisort($offset, SORT_ASC, $data);
        $i = 0;
        $temp = array();
        foreach ($data as $key => $row) {
            $temp[0] = $row['time'];
            $temp[1] = formatOffset($row['offset']);
            $temp[2] = $row['timezone_id'];
            $options[$i++] = $temp;
        }
        return $options;
    }
    function formatOffset($offset)
    {
        $hours = $offset / 3600;
        $remainder = $offset % 3600;
        $sign = $hours > 0 ? '+' : '-';
        $hour = (int) abs($hours);
        $minutes = (int) abs($remainder / 60);
        if ($hour == 0 and $minutes == 0) {
            $sign = ' ';
        }
        return $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minutes, 2, '0');
    }
    ?>
    <script type="text/javascript">
        /* on change of category mode btn - switchery js */
        var is_category = document.querySelector('#is_category');
        is_category.onchange = function() {
            if (is_category.checked) {
                $('#category_mode').val(1);
            } else {
                $('#category_mode').val(0);
                $('#subcategory_mode').val(0);
            }
        };
        /* on change of category mode btn - switchery js */
        var is_subcategory = document.querySelector('#is_subcategory');
        is_subcategory.onchange = function() {
            if (is_subcategory.checked) {
                if ($('#category_mode').val() == '1') {
                    $('#subcategory_mode').val(1);
                } else if ($('#category_mode').val() == '0') {
                    alert('Please enable category');
                    $("#is_subcategory").bootstrapSwitch('state', false);
                }
            } else {
                $('#subcategory_mode').val(0);
            }
        };
        /* on change of breaking_news mode btn - switchery js */
        var breaking_news = document.querySelector('#breaking_news');
        breaking_news.onchange = function() {
            if (breaking_news.checked)
                $('#breaking_news_mode').val(1);
            else
                $('#breaking_news_mode').val(0);
        };
        /* on change of comments mode btn - switchery js */
        var is_comments = document.querySelector('#is_comments');
        is_comments.onchange = function() {
            if (is_comments.checked)
                $('#comments_mode').val(1);
            else
                $('#comments_mode').val(0);
        };
        /* on change of live streaming mode btn - switchery js */
        var is_live_streaming = document.querySelector('#is_live_streaming');
        is_live_streaming.onchange = function() {
            if (is_live_streaming.checked)
                $('#live_streaming_mode').val(1);
            else
                $('#live_streaming_mode').val(0);
        };
        /* on change of google ads mode btn - switchery js */
        var in_app_ads = document.querySelector('#in_app_ads');
        in_app_ads.onchange = function() {
            if (in_app_ads.checked) {
                $('#in_app_ads_mode').val(1);
                $('.adsHide').show();
                var ads_type = $("input:radio[name=ads_type]:checked").val();
                ads_type_manage(ads_type);
            } else {
                $('#in_app_ads_mode').val(0);
                $('.adsHide').hide();
                ads_type_manage(0);
            }
        };
        /* on change of ios ads mode btn - switchery js */
        var ios_in_app_ads = document.querySelector('#ios_in_app_ads');
        ios_in_app_ads.onchange = function() {
            if (ios_in_app_ads.checked) {
                $('#ios_in_app_ads_mode').val(1);
                $('.iOSadsHide').show();
                var ios_ads_type = $("input:radio[name=ios_ads_type]:checked").val();
                ios_ads_type_manage(ios_ads_type);
            } else {
                $('#ios_in_app_ads_mode').val(0);
                $('.iOSadsHide').hide();
                ios_ads_type_manage(0);
            }
        };
        /* on change of Auto delete expire news mode btn - switchery js */
        var auto_delete_expire_news = document.querySelector('#auto_delete_expire_news');
        auto_delete_expire_news.onchange = function() {
            if (auto_delete_expire_news.checked) {
                $('#auto_delete_expire_news_mode').val(1);
            } else {
                $('#auto_delete_expire_news_mode').val(0);
            }
        };
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            //google ads
            $('.adsHide').hide();
            $('.adsgoogle').hide();
            $('.adsfacebook').hide();
            $('.adsunity').hide();
            var ads = $('#in_app_ads_mode').val();
            if (ads === '1' || ads === 1) {
                $('.adsHide').show();
                var ads_type = $("input:radio[name=ads_type]:checked").val();
                if (ads_type == undefined) {
                    $("input[name=ads_type][value=1]").prop('checked', true);
                }
            } else {
                $('.adsHide').hide();
                $('.adsfacebook').hide();
                $('.facebookAtt').removeAttr('required');
                $('.adsgoogle').hide();
                $('.googleAtt').removeAttr('required');
                $('.adsunity').hide();
                $('.unityAtt').removeAttr('required');
            }
            var ads_type = $("input:radio[name=ads_type]:checked").val();
            ads_type_manage(ads_type);
            //ios ads
            $('.iOSadsHide').hide();
            $('.iOSadsgoogle').hide();
            $('.iOSadsfacebook').hide();
            $('.iOSadsunity').hide();
            var ios_ads = $('#ios_in_app_ads_mode').val();
            if (ios_ads === '1' || ios_ads === 1) {
                $('.iOSadsHide').show();
                var ios_ads_type = $("input:radio[name=ios_ads_type]:checked").val();
                if (ios_ads_type == undefined) {
                    $("input[name=ios_ads_type][value=1]").prop('checked', true);
                }
            } else {
                $('.iOSadsHide').hide();
                $('.iOSadsgoogle').hide();
                $('.iOSgoogleAtt').removeAttr('required');
                $('.iOSadsfacebook').hide();
                $('.iOSfacebookAtt').removeAttr('required');
                $('.iOSadsunity').hide();
                $('.iOSunityAtt').removeAttr('required');
            }
            var ios_ads_type = $("input:radio[name=ios_ads_type]:checked").val();
            ios_ads_type_manage(ios_ads_type);
        });
        function ads_type_manage(ads_type) {
            var ads = $('#in_app_ads_mode').val();
            if (ads == 1 || ads == '1') {
                if (ads_type === '1' || ads_type === 1) {
                    $('.adsgoogle').show();
                    $('.googleAtt').attr('required', 'required');
                    $('.adsfacebook').hide();
                    $('.facebookAtt').removeAttr('required');
                    $('.adsunity').hide();
                    $('.unityAtt').removeAttr('required');
                } else if (ads_type === '2' || ads_type === 2) {
                    $('.adsgoogle').hide();
                    $('.googleAtt').removeAttr('required');
                    $('.adsunity').hide();
                    $('.unityAtt').removeAttr('required');
                    $('.adsfacebook').show();
                    $('.facebookAtt').attr('required', 'required');
                } else if (ads_type === '3' || ads_type === 3) {
                    $('.adsgoogle').hide();
                    $('.adsfacebook').hide();
                    $('.googleAtt').removeAttr('required');
                    $('.facebookAtt').removeAttr('required');
                    $('.adsunity').show();
                    $('.unityAtt').attr('required', 'required');
                } else {
                    $('.adsHide').hide();
                    $('.adsfacebook').hide();
                    $('.facebookAtt').removeAttr('required');
                    $('.adsgoogle').hide();
                    $('.googleAtt').removeAttr('required');
                    $('.adsunity').hide();
                    $('.unityAtt').removeAttr('required');
                }
            }
        }
        function ios_ads_type_manage(ios_ads_type) {
            var ios_ads = $('#ios_in_app_ads_mode').val();
            if (ios_ads === '1' || ios_ads === 1) {
                if (ios_ads_type === '1' || ios_ads_type === 1) {
                    $('.iOSadsgoogle').show();
                    $('.iOSgoogleAtt').attr('required', 'required');
                    $('.iOSadsfacebook').hide();
                    $('.iOSfacebookAtt').removeAttr('required');
                    $('.iOSadsunity').hide();
                    $('.iOSunityAtt').removeAttr('required');
                } else if (ios_ads_type === '2' || ios_ads_type === 2) {
                    $('.iOSadsgoogle').hide();
                    $('.iOSgoogleAtt').removeAttr('required');
                    $('.iOSadsunity').hide();
                    $('.iOSunityAtt').removeAttr('required');
                    $('.iOSadsfacebook').show();
                    $('.iOSfacebookAtt').attr('required', 'required');
                } else if (ios_ads_type === '3' || ios_ads_type === 3) {
                    $('.iOSadsgoogle').hide();
                    $('.iOSgoogleAtt').removeAttr('required');
                    $('.iOSadsfacebook').hide();
                    $('.iOSfacebookAtt').removeAttr('required');
                    $('.iOSadsunity').show();
                    $('.iOSunityAtt').attr('required', 'required');
                } else {
                    $('.iOSadsHide').hide();
                    $('.iOSadsfacebook').hide();
                    $('.iOSfacebookAtt').removeAttr('required');
                    $('.iOSadsgoogle').hide();
                    $('.iOSgoogleAtt').removeAttr('required');
                    $('.iOSadsunity').hide();
                    $('.iOSunityAtt').removeAttr('required');
                }
            }
        }
        $(document).on('click', 'input[name="ios_ads_type"]', function() {
            var ios_ads_type = $(this).val();
            ios_ads_type_manage(ios_ads_type);
        });
        $(document).on('click', 'input[name="ads_type"]', function() {
            var ads_type = $(this).val();
            ads_type_manage(ads_type);
        });
    </script>
    <script type="text/javascript">
        var _URL = window.URL || window.webkitURL;
        $("#exampleInputFile1").change(function(e) {
            var file, img;
            if ((file = this.files[0])) {
                img = new Image();
                img.onerror = function() {
                    $("#exampleInputFile1").val('');
                    $('#custom-file-label').html('');
                    $('#img_error_msg').html("Invalid Image Type");
                    $('#img_error_msg').show().delay(3000).fadeOut();
                };
                img.src = _URL.createObjectURL(file);
            }
        });
        $("#exampleInputFile2").change(function(e) {
            var file, img;
            if ((file = this.files[0])) {
                img = new Image();
                img.onerror = function() {
                    $("#exampleInputFile2").val('');
                    $('#custom-file-label1').html('');
                    $('#img_error_msg1').html("Invalid Image Type");
                    $('#img_error_msg1').show().delay(3000).fadeOut();
                };
                img.src = _URL.createObjectURL(file);
            }
        });
    </script>
    <script type="text/javascript">
        $('#insert_form').validate({
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group div').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
    </script>
    <script type="text/javascript">
        $("input[data-bootstrap-switch]").each(function() {
            $(this).bootstrapSwitch('state', $(this).prop('checked'));
        });
    </script>
</body>
</html>