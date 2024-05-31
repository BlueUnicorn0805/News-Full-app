<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\Category_Model;
use App\Models\News_Model;
use App\Models\UserRoles_Model;
use App\Models\Notification_Model;
use App\Models\Setting_Model;
use App\Models\WebSettings_Model;
use \Firebase\JWT\JWT;

class Dashboard extends Controller
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        $this->session = \Config\Services::session();
        $this->session->start();
        $this->helpers = helper('SystemSettings');
        date_default_timezone_set(get_system_timezone());
        $this->Category_Model = new Category_Model();
        $this->News_Model = new News_Model();
        $this->Notification_Model = new Notification_Model();
        $this->Setting_Model = new Setting_Model();
        $this->WebSettings_Model = new WebSettings_Model();
        $this->UserRoles_Model = new UserRoles_Model();
        $this->db = \Config\Database::connect();
        $this->today = date('Y-m-d');
        $this->data['app_name'] = $this->db->table('tbl_settings')->where('type', 'app_name')->get()->getResult();
        $this->data['app_logo'] = $this->db->table('tbl_settings')->where('type', 'app_logo')->get()->getResult();
        $this->data['app_logo_full'] = $this->db->table('tbl_settings')->where('type', 'app_logo_full')->get()->getResult();
        $res = $this->db->table('tbl_settings')->where('type', 'jwt_key')->get()->getResult();
        $this->JWT_KEY = (!empty($res)) ? $res[0]->message : "";
    }
    public function index()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            $this->data['count_breaking_news'] = $this->db->table('tbl_breaking_news')->countAll();
            $this->data['count_category'] = $this->db->table('tbl_category')->countAll();
            $this->data['count_news'] = $this->db->table('tbl_news')->countAll();
            $this->data['count_users'] = $this->db->table('tbl_users')->countAll();
            $this->data['count_active_language'] = $this->db->table('tbl_languages')->select('COUNT("id") as id')->where('status','1')->get()->getRow()->id;
            $this->data['count_featured_sections'] = $this->db->table('tbl_featured_sections')->select('COUNT("id") as id')->where('status','1')->get()->getRow()->id;
            $this->data['count_pages'] = $this->db->table('tbl_pages')->select('COUNT("id") as id')->where('status','1')->get()->getRow()->id;
            $this->data['count_ad_spaces'] = $this->db->table('tbl_ad_spaces')->select('COUNT("id") as id')->where('status','1')->get()->getRow()->id;
            $this->data['count_user_roles'] = $this->db->table('tbl_user_roles')->countAll();
            // AutoDelete Expire News
            if (is_auto_news_expire_news_enabled() == 1) {
                $this->db->table('tbl_news')->where('show_till <', $this->today)->where('show_till >', '0')->delete();
            }
            // Auto update News status based on show_till
            $news = $this->News_Model->orderBy('id', 'DESC')->findAll();
            foreach ($news as $n) {
                $today = date('Y-m-d');
                if ($n['is_clone'] == 0) {
                    if ($n['show_till'] = '0000-00-00') {
                    } elseif ($today > $n['show_till']) {
                        $data = ['status' => 0];
                        $this->News_Model->update($n['id'], $data);
                    } else {
                        $data = ['status' => 1];
                        $this->News_Model->update($n['id'], $data);
                    }
                }
            }
           
            //Category-News Pie chart
            $count_news_per_category = $this->db->table('tbl_news n')->select('COUNT(n.id) as news_count, c.category_name')
            ->join('tbl_category c', 'c.id = n.category_id')
            ->where('n.status', 1)
            ->where("n.show_till >= '" . $this->today . "' OR CAST(n.show_till AS CHAR(20)) = '0000-00-00'")
            ->where('n.category_id GROUP BY n.category_id,c.category_name')->get()->getResult();
            $news_per_category = [];
            foreach($count_news_per_category as $row) {
                $news_per_category[] = array(
                    'category' => $row->category_name,
                    'news' => floatval($row->news_count)
                );
            }
            $this->data['news_per_category'] = ($news_per_category); 
            
            //Category-Language Column chart
            $count_news_per_language = $this->db->table('tbl_news n')->select('COUNT(n.id) as news_count, l.language')
            ->join('tbl_languages l', 'l.id = n.language_id')
            ->where('n.status', 1)
            ->where("n.show_till >= '" . $this->today . "' OR CAST(n.show_till AS CHAR(20)) = '0000-00-00'")
            ->where('n.language_id GROUP BY n.language_id,l.language')->get()->getResult();
            $news_per_language = [];
            foreach($count_news_per_language as $row) {
                $news_per_language[] = array(
                    'language' => $row->language,
                    'news' => floatval($row->news_count)
                );
            }
            $this->data['news_per_language'] = ($news_per_language); 

            //Surveys-Language Column chart
            $count_surveys_per_language = $this->db->table('tbl_survey_question s')->select('COUNT(s.id) as surveys_count, l.language')
            ->join('tbl_languages l', 'l.id = s.language_id')
            ->where('s.status', 1)
            ->where('s.language_id GROUP BY s.language_id,l.language')->get()->getResult();
            $surveys_per_language = [];
            foreach($count_surveys_per_language as $row) {
                $surveys_per_language[] = array(
                    'language' => $row->language,
                    'surveys' => floatval($row->surveys_count)
                );
            }
            $this->data['surveys_per_language'] = ($surveys_per_language); 
            return view('dashboard', $this->data);
        }
    }
    public function comments_flag()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            return view('comments_flag', $this->data);
        }
    }
    public function delete_comment_flag()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            if (ALLOW_MODIFICATION) {
                return $this->response->setJSON(FALSE);
            } else {
                $id = $this->request->getVar('id');
                $data = ['status' => '0'];
                $this->db->table('tbl_comment_flag')->where('id', $id)->update($data);
                return $this->response->setJSON(TRUE);
            }
        }
    }
    public function comments()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            return view('comments', $this->data);
        }
    }
    public function delete_comment()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            if (is_modification_allowed()) {
                return $this->response->setJSON(FALSE);
            } else {
                $id = $this->request->getVar('id');
                $this->db->table('tbl_comment')->where('id', $id)->delete();
                $this->db->table('tbl_comment_flag')->where('comment_id', $id)->delete();
                return $this->response->setJSON(TRUE);
            }
        }
    }
    public function users()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            $this->data['user_roles'] = $this->UserRoles_Model->orderBy('id', 'DESC')->findAll();
            return view('users', $this->data);
        }
    }
    public function update_users()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            if (is_modification_allowed()) {
                $this->session->setFlashdata('error', DEMO_VERSION_MSG);
            } else {
                $id = $this->request->getVar('edit_id');
                $data = ['status' => $this->request->getVar('edit_status'), 'role' => $this->request->getVar('role')];
                $this->db->table('tbl_users')->where('id', $id)->update($data);
                $this->session->setFlashdata('success', 'User Update successfully..');
            }
            return redirect('users');
        }
    }
    public function system_configurations()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            $this->data['system_timezone'] = $this->db->table('tbl_settings')->where('type', 'system_timezone')->get()->getResult();
            $this->data['jwt_key'] = $this->db->table('tbl_settings')->where('type', 'jwt_key')->get()->getResult();
            $this->data['is_category'] = $this->db->table('tbl_settings')->where('type', 'category_mode')->get()->getResult();
            $this->data['is_subcategory'] = $this->db->table('tbl_settings')->where('type', 'subcategory_mode')->get()->getResult();
            $this->data['is_news'] = $this->db->table('tbl_settings')->where('type', 'breaking_news_mode')->get()->getResult();
            $this->data['is_comments'] = $this->db->table('tbl_settings')->where('type', 'comments_mode')->get()->getResult();
            $this->data['is_live_streaming'] = $this->db->table('tbl_settings')->where('type', 'live_streaming_mode')->get()->getResult();
            $settings = [
                'ads_type',
                'in_app_ads_mode',
                'google_rewarded_video_id',
                'google_interstitial_id',
                'google_banner_id',
                'google_native_unit_id',
                'fb_rewarded_video_id',
                'fb_interstitial_id',
                'fb_banner_id',
                'fb_native_unit_id',
                'ios_ads_type',
                'ios_in_app_ads_mode',
                'ios_google_rewarded_video_id',
                'ios_google_interstitial_id',
                'ios_google_banner_id',
                'ios_google_native_unit_id',
                'ios_fb_rewarded_video_id',
                'ios_fb_interstitial_id',
                'ios_fb_banner_id',
                'ios_fb_native_unit_id',
                'unity_rewarded_video_id',
                'unity_interstitial_id',
                'unity_banner_id',
                'android_game_id',
                'ios_unity_rewarded_video_id',
                'ios_unity_interstitial_id',
                'ios_unity_banner_id',
                'ios_game_id',
                'default_language',
                'auto_delete_expire_news_mode',
                'smtp_host',
                'smtp_user',
                'smtp_password',
                'smtp_port',
                'smtp_crypto',
                'from_name'
            ];
            foreach ($settings as $row) {
                $this->data[$row] = $this->db->table('tbl_settings')->where('type', $row)->get()->getResult();
            }
            
            return view('system_configurations', $this->data);
        }
    }
    public function store_system_setting()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            if (is_modification_allowed()) {
                $this->session->setFlashdata('error', DEMO_VERSION_MSG);
            } else {
                $image_full = $this->request->getFile('file1');
                if ($image_full->getClientName() != '') {
                    $newName = microtime(TRUE) . '.' . $image_full->getClientExtension();
                    if ($image_full->isValid() && !$image_full->hasMoved()) {
                        $image_full->move('public/images', $newName);
                    }
                    if ($image_full->hasMoved()) {
                        $app_logo = ['message' => $newName];
                        $this->Setting_Model->where('type', 'app_logo_full')->set($app_logo)->update();
                    } else {
                        $this->session->setFlashdata('error', 'Something went wrong, please try again.!');
                    }
                }
                $image = $this->request->getFile('file');
                if ($image->getClientName() != '') {
                    $newName = microtime(TRUE) . '.' . $image->getClientExtension();
                    if ($image->isValid() && !$image->hasMoved()) {
                        $image->move('public/images', $newName);
                    }
                    if ($image->hasMoved()) {
                        $app_logo = ['message' => $newName];
                        $this->Setting_Model->where('type', 'app_logo')->set($app_logo)->update();
                    } else {
                        $this->session->setFlashdata('error', 'Something went wrong, please try again.!');
                    }
                }
                $settings = [
                    'jwt_key',
                    'system_timezone',
                    'app_name',
                    'category_mode',
                    'subcategory_mode',
                    'breaking_news_mode',
                    'comments_mode',
                    'live_streaming_mode',
                    'ads_type',
                    'in_app_ads_mode',
                    'google_rewarded_video_id',
                    'google_interstitial_id',
                    'google_banner_id',
                    'google_native_unit_id',
                    'fb_rewarded_video_id',
                    'fb_interstitial_id',
                    'fb_banner_id',
                    'fb_native_unit_id',
                    'ios_ads_type',
                    'ios_in_app_ads_mode',
                    'ios_google_rewarded_video_id',
                    'ios_google_interstitial_id',
                    'ios_google_banner_id',
                    'ios_google_native_unit_id',
                    'ios_fb_rewarded_video_id',
                    'ios_fb_interstitial_id',
                    'ios_fb_banner_id',
                    'ios_fb_native_unit_id',
                    'unity_rewarded_video_id',
                    'unity_interstitial_id',
                    'unity_banner_id',
                    'android_game_id',
                    'ios_unity_rewarded_video_id',
                    'ios_unity_interstitial_id',
                    'ios_unity_banner_id',
                    'ios_game_id',
                    'default_language',
                    'auto_delete_expire_news_mode',
                    'smtp_host',
                    'smtp_user',
                    'smtp_password',
                    'smtp_port',
                    'smtp_crypto',
                    'from_name'
                ];
                foreach ($settings as $type) {
                    $message = $this->request->getVar($type);
                    $res = $this->db->table('tbl_settings')->where('type', $type)->get()->getResult();
                    if (!empty($res)) {
                        $data = ['message' => $message];
                        $this->Setting_Model->where('type', $type)->set($data)->update();
                    } else {
                        $data = [
                            'type'    => $type,
                            'message' => $message
                        ];
                        $this->Setting_Model->insert($data);
                    }
                }
                $this->session->setFlashdata('success', 'Settings Update successfully..');
            }
            return redirect('system_configurations');
        }
    }
    public function notification_settings()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            $this->data['setting'] = $this->db->table('tbl_settings')->where('type', 'fcm_sever_key')->get()->getResult();
            return view('notification_settings', $this->data);
        }
    }
    public function store_fcm_server_key()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            if (is_modification_allowed()) {
                $this->session->setFlashdata('error', DEMO_VERSION_MSG);
            } else {
                $server_key = $this->db->table('tbl_settings')->where('type', 'fcm_sever_key')->get()->getResult();
                if (empty($server_key)) {
                    $data = [
                        'type'    => 'fcm_sever_key',
                        'message' => $this->request->getVar('message')
                    ];
                    $this->Setting_Model->insert($data);
                } else {
                    $data = ['message' => $this->request->getVar('message')];
                    $this->Setting_Model->update($server_key[0]->id, $data);
                }
                $this->session->setFlashdata('success', 'FCM server key Update successfully..');
            }
            return redirect('notification_settings');
        }
    }
    public function about_us()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            $this->data['setting'] = $this->db->table('tbl_settings')->where('type', 'about_us')->get()->getResult();
            return view('about_us', $this->data);
        }
    }
    public function store_about_us()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            if (is_modification_allowed()) {
                $this->session->setFlashdata('error', DEMO_VERSION_MSG);
            } else {
                $setting = $this->db->table('tbl_settings')->where('type', 'about_us')->get()->getResult();
                if (empty($setting)) {
                    $data = [
                        'type'    => 'about_us',
                        'message' => $this->request->getVar('message')
                    ];
                    $this->Setting_Model->insert($data);
                } else {
                    $data = ['message' => $this->request->getVar('message')];
                    $this->Setting_Model->update($setting[0]->id, $data);
                }
                $this->session->setFlashdata('success', 'About Us Update successfully..!');
            }
            return redirect('about_us');
        }
    }
    public function play_store_about_us()
    {
        $data['setting'] = $this->db->table('tbl_settings')->where('type', 'about_us')->get()->getResult();
        return view('play_store_about_us', $data);
    }
    public function privacy_policy()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            $this->data['setting'] = $this->db->table('tbl_settings')->where('type', 'privacy_policy')->get()->getResult();
            return view('privacy_policy', $this->data);
        }
    }
    public function store_policy()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            if (is_modification_allowed()) {
                $this->session->setFlashdata('error', DEMO_VERSION_MSG);
            } else {
                $setting = $this->db->table('tbl_settings')->where('type', 'privacy_policy')->get()->getResult();
                if (empty($setting)) {
                    $data = [
                        'type'    => 'privacy_policy',
                        'message' => $this->request->getVar('message')
                    ];
                    $this->Setting_Model->insert($data);
                } else {
                    $data = ['message' => $this->request->getVar('message')];
                    $this->Setting_Model->update($setting[0]->id, $data);
                }
                $this->session->setFlashdata('success', 'Privacy Policy Update successfully..!');
            }
            return redirect('privacy_policy');
        }
    }
    public function play_store_privacy_policy()
    {
		$default_language = $this->db->table('tbl_settings')->where('type', 'default_language')->get()->getRow()->message;
        $this->data['play_store_privacy_policy'] = $this->db->table('tbl_pages')->where('is_privacypolicy', 1)->where('language_id', $default_language)->get()->getRow()->page_content;
		
        return view('play_store_privacy_policy', $this->data);
    }
    public function terms_conditions()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            $this->data['setting'] = $this->db->table('tbl_settings')->where('type', 'terms_conditions')->get()->getResult();
            return view('terms_conditions', $this->data);
        }
    }
    public function store_terms_conditions()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            if (is_modification_allowed()) {
                $this->session->setFlashdata('error', DEMO_VERSION_MSG);
            } else {
                $setting = $this->db->table('tbl_settings')->where('type', 'terms_conditions')->get()->getResult();
                if (empty($setting)) {
                    $data = [
                        'type'    => 'terms_conditions',
                        'message' => $this->request->getVar('message')
                    ];
                    $this->Setting_Model->insert($data);
                } else {
                    $data = ['message' => $this->request->getVar('message')];
                    $this->Setting_Model->update($setting[0]->id, $data);
                }
                $this->session->setFlashdata('success', 'Terms Condition Update successfully..!');
            }
            return redirect('terms_conditions');
        }
    }
    public function play_store_terms_conditions()
    {
        $this->data['setting'] = $this->db->table('tbl_settings')->where('type', 'terms_conditions')->get()->getResult();
        return view('play_store_terms_conditions', $this->data);
    }
    public function contact_us()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            $this->data['setting'] = $this->db->table('tbl_settings')->where('type', 'contact_us')->get()->getResult();
            return view('contact_us', $this->data);
        }
    }
    public function store_contact_us()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            if (is_modification_allowed()) {
                $this->session->setFlashdata('error', DEMO_VERSION_MSG);
            } else {
                $setting = $this->db->table('tbl_settings')->where('type', 'contact_us')->get()->getResult();
                if (empty($setting)) {
                    $data = [
                        'type'    => 'contact_us',
                        'message' => $this->request->getVar('message')
                    ];
                    $this->Setting_Model->insert($data);
                } else {
                    $data = ['message' => $this->request->getVar('message')];
                    $this->Setting_Model->update($setting[0]->id, $data);
                }
                $this->session->setFlashdata('success', 'Contact us Update successfully..!');
            }
            return redirect('contact_us');
        }
    }
    public function play_store_contact_us()
    {
        $this->data['setting'] = $this->db->table('tbl_settings')->where('type', 'contact_us')->get()->getResult();
        return view('play_store_contact_us', $this->data);
    }
    public function store_default_language()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            if (is_modification_allowed()) {
                $this->session->setFlashdata('error', DEMO_VERSION_MSG);
            } else {
                $setting = $this->db->table('tbl_settings')->where('type', 'default_language')->get()->getResult();
                if (empty($setting)) {
                    $data = [
                        'type'    => 'default_language',
                        'message' => $this->request->getVar('id')
                    ];
                    $this->Setting_Model->insert($data);
                } else {
                    $data = ['message' => $this->request->getVar('id')];
                    $this->Setting_Model->update($setting[0]->id, $data);
                }
                $this->session->setFlashdata('success', 'Default Language Set Successfully..!');
                return $this->response->setJSON(TRUE);
            }
        }
    }
    public function database_backup()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            if (is_modification_allowed()) {
                $this->session->setFlashdata('error', DEMO_VERSION_MSG);
                return redirect('dashboard');
            } else {
                $response = \Config\Services::response();
                $data = $this->Setting_Model->downloadBackup();
                $name = 'db_backup-' . date('Y-m-d H-i-s') . '.sql';
                return $response->download($name, $data);
            }
        }
    }

    public function nekot()
    {
        $payload = [
            'iat' => time(), /* issued at time */
            'iss' => 'WRTEAM',
            'exp' => time() + (30 * 60 * 60 * 24), /* expires after 1 minute */
            'sub' => 'WRTEAM Authentication'
        ];
        return JWT::encode($payload, $this->JWT_KEY, 'HS256');
    }

    public function web_settings()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            $settings = [
                'web_name',
                'web_header_logo',
                'web_footer_logo',
                'web_color_code',
                'web_footer_description'
            ];
            foreach ($settings as $row) {
                $this->data[$row] = $this->db->table('tbl_web_settings')->where('type', $row)->get()->getResult();
            }
            
            return view('web_settings', $this->data);
        }
    }

    public function store_web_settings()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            if (is_modification_allowed()) {
                $this->session->setFlashdata('error', DEMO_VERSION_MSG);
            } else {
                $web_header_logo = $this->request->getFile('web_header_logo');
                if ($web_header_logo->getClientName() != '') {
                    $newName = microtime(TRUE) . '.' . $web_header_logo->getClientExtension();
                    if ($web_header_logo->isValid() && !$web_header_logo->hasMoved()) {
                        $web_header_logo->move('public/images', $newName);
                    }
                    if ($web_header_logo->hasMoved()) {
                        $old_image = $this->db->table('tbl_web_settings')->select('message')->where('type', 'web_header_logo')->get()->getRow();
                        if($old_image != null && $old_image->message != null) {
                            $old_image_url = "public/images/".$old_image->message;
                            file_exists($old_image_url) ? unlink($old_image_url) : '';
                            $app_logo = ['message' => $newName];
                            $this->WebSettings_Model->where('type', 'web_header_logo')->set($app_logo)->update();
                        }else{
                            $data = [
                                'type'    => 'web_header_logo',
                                'message' => $newName
                            ];
                            $this->WebSettings_Model->insert($data);
                        }
                    } else {
                        $this->session->setFlashdata('error', 'Something went wrong, please try again.!');
                    }
                }
                $web_footer_logo = $this->request->getFile('web_footer_logo');
                if ($web_footer_logo->getClientName() != '') {
                    $newName = microtime(TRUE) . '.' . $web_footer_logo->getClientExtension();
                    if ($web_footer_logo->isValid() && !$web_footer_logo->hasMoved()) {
                        $web_footer_logo->move('public/images', $newName);
                    }
                    if ($web_footer_logo->hasMoved()) {
                        $old_image = $this->WebSettings_Model->select('message')->where('type', 'web_footer_logo')->get()->getRow();
                        if($old_image != null && $old_image->message != null) {
                            $old_image_url = "public/images/".$old_image->message;
                            file_exists($old_image_url) ? unlink($old_image_url) : '';
                            $app_logo = ['message' => $newName];
                            $this->WebSettings_Model->where('type', 'web_footer_logo')->set($app_logo)->update();
                        }else{
                            $data = [
                                'type'    => 'web_footer_logo',
                                'message' => $newName
                            ];
                            $this->WebSettings_Model->insert($data);
                        }
                    } else {
                        $this->session->setFlashdata('error', 'Something went wrong, please try again.!');
                    }
                }
                $settings = [
                    'web_name',
                    'web_color_code',
                    'web_footer_description'
                ];
                foreach ($settings as $type) {
                    $message = $this->request->getVar($type);
                    $res = $this->db->table('tbl_web_settings')->where('type', $type)->get()->getResult();
                    if (!empty($res)) {
                        $data = ['message' => $message];
                        $this->WebSettings_Model->where('type', $type)->set($data)->update();
                    } else {
                        $data = [
                            'type'    => $type,
                            'message' => $message
                        ];
                        $this->WebSettings_Model->insert($data);
                    }
                }
                $this->session->setFlashdata('success', 'Settings Update successfully..');
            }
            return redirect('web_settings');
        }
    }
}