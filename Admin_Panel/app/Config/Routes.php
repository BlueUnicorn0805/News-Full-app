<?php
namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();
// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}
/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Login');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);
/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */
// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Login::index');
/* * ********* USER DEFINED ROUTES FOR ADMIN PANEL ****************** */
$routes->post('checklogin', 'Login::checklogin');
$routes->get('logout', 'Login::logout');
$routes->post('checkOldPass', 'Login::checkOldPass');
$routes->get('edit_profile', 'Login::edit_profile');
$routes->get('dashboard', 'Dashboard::index');
$routes->get('breaking_news', 'BreakingNews::index');
$routes->post('store_breaking_news', 'BreakingNews::store');
$routes->post('update_breaking_news', 'BreakingNews::update');
$routes->post('delete_breaking_news', 'BreakingNews::delete');
$routes->get('live_streaming', 'LiveStreaming::index');
$routes->post('store_live_streaming', 'LiveStreaming::store');
$routes->post('update_live_streaming', 'LiveStreaming::update');
$routes->post('delete_live_streaming', 'LiveStreaming::delete');
$routes->get('tag', 'Tag::index');
$routes->post('store_tag', 'Tag::store');
$routes->post('update_tag', 'Tag::update');
$routes->post('delete_tag', 'Tag::delete');
$routes->get('category', 'Category::index');
$routes->post('store_category', 'Category::store');
$routes->post('update_category', 'Category::update');
$routes->post('delete_category', 'Category::delete');
$routes->get('subcategory', 'Subcategory::index');
$routes->post('store_subcategory', 'Subcategory::store');
$routes->post('update_subcategory', 'Subcategory::update');
$routes->post('delete_subcategory', 'Subcategory::delete');
$routes->post('get_subcategory_by_category', 'News::get_subcategory_by_category');
$routes->post('get_category_by_language', 'News::get_category_by_language');
$routes->post('get_tag_by_language', 'News::get_tag_by_language');
$routes->get('news', 'News::index');
$routes->post('upload_img', 'News::upload_img');
$routes->post('store_news', 'News::store');
$routes->post('update_news', 'News::update');
$routes->post('update_news_des', 'News::update_des');
$routes->post('delete_news', 'News::delete');
$routes->post('clone_news', 'News::clone');
$routes->get('news-image/(:num)', 'News::indexImage/$1');
$routes->post('store_news_image', 'News::storeImage');
$routes->post('delete_news_image', 'News::deleteImage');
$routes->get('notification', 'Notification::index');
$routes->get('get_news_by_category/(:num)', 'Notification::get_news_by_category/$1');
$routes->get('get_news_by_subcategory/(:num)', 'Notification::get_news_by_subcategory/$1');
$routes->post('store_notification', 'Notification::store');
$routes->post('delete_notification', 'Notification::delete');
$routes->get('user_roles', 'UserRoles::index');
$routes->post('store_user_roles', 'UserRoles::store');
$routes->post('update_user_roles', 'UserRoles::update');
$routes->post('delete_user_roles', 'UserRoles::delete');
$routes->get('users', 'Dashboard::users');
$routes->post('update_users', 'Dashboard::update_users');
$routes->get('comments', 'Dashboard::comments');
$routes->post('delete_comment', 'Dashboard::delete_comment');
$routes->get('comments_flag', 'Dashboard::comments_flag');
$routes->post('delete_comment_flag', 'Dashboard::delete_comment_flag');
$routes->get('system_configurations', 'Dashboard::system_configurations');
$routes->post('store_system_setting', 'Dashboard::store_system_setting');
$routes->get('notification_settings', 'Dashboard::notification_settings');
$routes->post('store_fcm_server_key', 'Dashboard::store_fcm_server_key');
$routes->get('play_store_about_us', 'Dashboard::play_store_about_us');
$routes->get('about_us', 'Dashboard::about_us');
$routes->post('store_about_us', 'Dashboard::store_about_us');
$routes->get('play_store_privacy_policy', 'Dashboard::play_store_privacy_policy');
$routes->get('privacy_policy', 'Dashboard::privacy_policy');
$routes->post('store_policy', 'Dashboard::store_policy');
$routes->get('play_store_terms_conditions', 'Dashboard::play_store_terms_conditions');
$routes->get('terms_conditions', 'Dashboard::terms_conditions');
$routes->post('store_terms_conditions', 'Dashboard::store_terms_conditions');
$routes->get('play_store_contact_us', 'Dashboard::play_store_contact_us');
$routes->get('contact_us', 'Dashboard::contact_us');
$routes->post('store_contact_us', 'Dashboard::store_contact_us');
$routes->get('system_updates', 'Settings::index');
$routes->post('store_system_update', 'Settings::store_system_update');
$routes->get('survey', 'Survey::index');
$routes->post('store_question', 'Survey::store_question');
$routes->post('update_question', 'Survey::update_question');
$routes->post('delete_question', 'Survey::delete_question');
$routes->get('survey_option/(:num)', 'Survey::get_survey_option/$1');
$routes->post('store_option', 'Survey::store_option');
$routes->post('update_option', 'Survey::update_option');
$routes->post('delete_option', 'Survey::delete_option');
$routes->get('language', 'Language::index');
$routes->post('delete_language', 'Language::delete');
$routes->post('update_language_staus', 'Language::update');
$routes->post('store_language', 'Language::store');
$routes->post('get_categories_of_language', 'Dashboard::get_categories_of_language');
$routes->get('download_sample_file', 'Language::language_sample');
$routes->post('store_default_language', 'Dashboard::store_default_language');
$routes->get('pages', 'Pages::index');
$routes->post('store_pages', 'Pages::store');
$routes->post('update_pages', 'Pages::update');
$routes->post('delete_pages', 'Pages::delete');
$routes->post('upload_pages_img', 'Pages::upload_pages_img');
$routes->get('database_backup', 'Dashboard::database_backup');
$routes->get('featured_sections', 'FeaturedSections::index');
$routes->post('get_categories_tree', 'FeaturedSections::get_categories_tree');
$routes->post('get_custom_news', 'FeaturedSections::get_custom_news');
$routes->post('store_featured_sections', 'FeaturedSections::store');
$routes->post('update_featured_sections', 'FeaturedSections::update');
$routes->post('delete_featured_sections', 'FeaturedSections::delete');
$routes->get('featured_sections_order', 'FeaturedSections::featured_sections_order');
$routes->post('update_featured_sections_order', 'FeaturedSections::update_featured_sections_order');
$routes->get('nekot', 'Dashboard::nekot');
$routes->get('ad_spaces', 'AdSpaces::index');
$routes->post('get_featured_sections_by_language', 'FeaturedSections::get_featured_sections_by_language');
$routes->post('store_ad_spaces', 'AdSpaces::store');
$routes->post('update_ad_spaces', 'AdSpaces::update');
$routes->post('delete_ad_spaces', 'AdSpaces::delete');
$routes->get('web_settings', 'Dashboard::web_settings');
$routes->post('store_web_settings', 'Dashboard::store_web_settings');
$routes->post('update_profile', 'Login::update_profile');
$routes->post('check_email', 'Login::check_email');
$routes->get('reset_password', 'Login::reset_password');
$routes->post('update_password', 'Login::update_password');
/**
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}