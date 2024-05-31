<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\Category_Model;
use App\Models\Subcategory_Model;
use App\Models\Tag_Model;
use App\Models\News_Model;
use App\Models\UserRoles_Model;

class News extends Controller
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        $this->session = \Config\Services::session();
        $this->session->start();
        $this->helpers = helper('SystemSettings');
        date_default_timezone_set(get_system_timezone());
        $this->toDate = date('Y-m-d H:i:s');
        $this->Category_Model = new Category_Model();
        $this->Subcategory_Model = new Subcategory_Model();
        $this->Tag_Model = new Tag_Model();
        $this->News_Model = new News_Model();
        $this->UserRoles_Model = new UserRoles_Model();
        $this->db = \Config\Database::connect();
        $this->data['app_name'] = $this->db->table('tbl_settings')->where('type', 'app_name')->get()->getResult();
        $this->data['app_logo'] = $this->db->table('tbl_settings')->where('type', 'app_logo')->get()->getResult();
        $this->data['languages'] = $this->db->table('tbl_languages')->where('status', 1)->get()->getResult();
        $this->target_path = 'public/images/news/';
        $this->target_path_video = 'public/images/news_video';
        
    }
    public function index()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            $this->data['cate'] = $this->Category_Model->orderBy('id', 'DESC')->findAll();
            $this->data['tag'] = $this->Tag_Model->orderBy('id', 'DESC')->findAll();
            $this->data['user'] = $this->db->table('tbl_users')->where('name !=', '')->orderBy('name', 'ASC')->get()->getResult();
            $this->data['role'] = $this->UserRoles_Model->orderBy('id', 'DESC')->findAll();
            return view('news', $this->data);
        }
    }
    public function get_category_by_language()
    {
        $language_id = $this->request->getVar('language_id');
        $res = $this->Category_Model->where('language_id', $language_id)->orderBy('id', 'DESC')->findAll();
        $option = '<option value="">Select category</option>';
        if (!empty($res)) {
            foreach ($res as $value) {
                $option .= '<option value="' . $value['id'] . '">' . $value['category_name'] . '</option>';
            }
        }
        return $option;
    }
    public function get_subcategory_by_category()
    {
        $category_id = $this->request->getVar('category_id');
        $res = $this->Subcategory_Model->where('category_id', $category_id)->orderBy('id', 'DESC')->findAll();
        $option = '<option value="">Select Subcategory</option>';
        if (!empty($res)) {
            foreach ($res as $value) {
                $option .= '<option value="' . $value['id'] . '">' . $value['subcategory_name'] . '</option>';
            }
        }
        return $option;
    }
    public function get_tag_by_language()
    {
        $language_id = $this->request->getVar('language_id');
        $res = $this->Tag_Model->where('language_id', $language_id)->orderBy('id', 'DESC')->findAll();
        $option = '<option value="">Select Tag</option>';
        if (!empty($res)) {
            foreach ($res as $value) {
                $option .= '<option value="' . $value['id'] . '">' . $value['tag_name'] . '</option>';
            }
        }
        return $option;
    }
    public function store()
    {
        if (ALLOW_MODIFICATION) {
            $this->session->setFlashdata('error', DEMO_VERSION_MSG);
        } else {
            if (!is_dir($this->target_path)) {
                mkdir($this->target_path, 0777, TRUE);
            }
            $image = $this->request->getFile('file');
            $newName = microtime(TRUE) . '.' . $image->getClientExtension();
            if ($image->isValid() && !$image->hasMoved()) {
                $image->move($this->target_path, $newName);
            }
            if ($image->hasMoved()) {
                $content_type = $this->request->getVar('content_type');
                if ($content_type == "standard_post") {
                    $content_value = "";
                } else if ($content_type == "video_youtube") {
                    $content_value = $this->request->getVar('youtube_url');
                } else if ($content_type == "video_other") {
                    $content_value = $this->request->getVar('other_url');
                } else if ($content_type == "video_upload") {
                    if (!is_dir($this->target_path_video)) {
                        mkdir($this->target_path_video, 0777, TRUE);
                    }
                    $file = $this->request->getFile('video_file');
                    $fileName = microtime(TRUE) . '.' . $file->getClientExtension();
                    if ($file->isValid() && !$file->hasMoved()) {
                        $file->move($this->target_path_video, $fileName);
                    }
                    $content_value = $fileName;
                }
                $tag_id = $this->request->getVar('tag_id');
                if (!empty($tag_id)) {
                    $tag_id = implode(',', $tag_id);
                } else {
                    $tag_id = '';
                }
                $data = [
                    'category_id'    => ($this->request->getVar('category_id')) ? $this->request->getVar('category_id') : 0,
                    'subcategory_id' => ($this->request->getVar('subcategory_id')) ? $this->request->getVar('subcategory_id') : 0,
                    'tag_id'         => $tag_id,
                    'title'          => $this->request->getVar('title'),
                    'date'           => $this->toDate,
                    'content_type'   => $this->request->getVar('content_type'),
                    'content_value'  => $content_value,
                    'description'    => $this->request->getVar('des'),
                    'image'          => $newName,
                    'admin_id'       => $this->session->get('adminId'),
                    'status'         => '1',
                    'show_till'      => ($this->request->getVar('show_till')) ? $this->request->getVar('show_till') : '',
                    'language_id'    => $this->request->getVar('language'),
                ];
                $this->News_Model->insert($data);
                $insert_id = $this->News_Model->insertID;
                if($insert_id && $this->request->getVar('notification') == '1'){
                    $fcmMsg = array(
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        'message' => $this->request->getVar('title'),
                        'body' => $this->request->getVar('title'),
                        'news_id' => $insert_id,
                        'language_id' => $this->request->getVar('language'),
                        'type' => 'newlyadded',
                        
                    );
                    $this->send_notification($fcmMsg);
                }
                if ($this->request->getFileMultiple('ofile')) {
                    foreach ($this->request->getFileMultiple('ofile') as $file1) {
                        if (!is_dir($this->target_path . $insert_id)) {
                            mkdir($this->target_path . $insert_id, 0777, TRUE);
                        }
                        $fileName1 = microtime(TRUE) . '.' . $file1->getClientExtension();
                        $extension = $file1->getClientExtension();
                        $allowedExts = array("gif", "jpeg", "jpg", "png", "GIF", "JPEG", "JPG", "PNG");
                        if (in_array($extension, $allowedExts)) {
                            if ($file1->isValid() && !$file1->hasMoved()) {
                                $file1->move($this->target_path . $insert_id, $fileName1);
                            }
                            if ($file1->hasMoved()) {
                                $data = [
                                    'news_id'     => $insert_id,
                                    'other_image' => $fileName1
                                ];
                               $this->db->table('tbl_news_image')->insert($data);
                               
                            }
                        }
                    }
                }

                $this->session->setFlashdata('success', 'Data inserted successfully.!');
            } else {
                $this->session->setFlashdata('error', 'Something went wrong, please try again.!');
            }
        }
        return redirect('news');
    }
    public function update()
    {
        if (ALLOW_MODIFICATION) {
            $this->session->setFlashdata('error', DEMO_VERSION_MSG);
        } else {
            $image = $this->request->getFile('file');
            $id = $this->request->getVar('edit_id');
            if ($image->getClientName() != '') {
                $newName = microtime(TRUE) . '.' . $image->getClientExtension();
                if ($image->isValid() && !$image->hasMoved()) {
                    $image->move($this->target_path, $newName);
                }
                if ($image->hasMoved()) {
                    $image_url = $this->request->getVar('image_url');
                    if (file_exists($image_url)) {
                        unlink($image_url);
                    }
                    $data = ['image' => $newName];
                    $this->News_Model->update($id, $data);
                } else {
                    $this->session->setFlashdata('error', 'Something went wrong, please try again.!');
                    return redirect('news');
                }
            }
            $content_type = $this->request->getVar('content_type');
            if ($content_type == "standard_post") {
                $content_value = "";
                $data1 = [
                
                'content_value'  => $content_value,
                
                ];
            } else if ($content_type == "video_youtube") {
                $content_value = $this->request->getVar('youtube_url');
                $data1 = [
                
                'content_value'  => $content_value,
                
                ];
            } else if ($content_type == "video_other") {
                $content_value = $this->request->getVar('other_url');
                $data1 = [
                
                'content_value'  => $content_value,
                
                ];
            } else if ($content_type == "video_upload") {
                 if($this->request->getFile('video_file') && $this->request->getFile('video_file')->getClientName() != null) {
                if (!is_dir($this->target_path_video)) {
                    mkdir($this->target_path_video, 0777, TRUE);
                }
                $file = $this->request->getFile('video_file');
                $fileName = microtime(TRUE) . '.' . $file->getClientExtension();
                if ($file->isValid() && !$file->hasMoved()) {
                    $file->move($this->target_path_video, $fileName);
                }
                $content_value = $fileName;
                 $data1 = [
                
                'content_value'  => $content_value,
                
                ];
                 }
            }
            $tag_id = $this->request->getVar('tag_id');
            if (!empty($tag_id)) {
                $tag_id = implode(',', $tag_id);
            } else {
                $tag_id = '';
            }
           
            $is_clone = $this->request->getVar('status') == '1' ? '0' : '1';
            $data1 = [
                'category_id'    => ($this->request->getVar('category_id')) ? $this->request->getVar('category_id') : 0,
                'subcategory_id' => ($this->request->getVar('subcategory_id')) ? $this->request->getVar('subcategory_id') : 0,
                'tag_id'         => $tag_id,
                'title'          => $this->request->getVar('title'),
                'content_type'   => $this->request->getVar('content_type'),
              
                'show_till'      => $this->request->getVar('show_till') ?? NULL,
                'language_id'    => $this->request->getVar('edit_language'),
                'status'         => $this->request->getVar('status'),
                'is_clone'       => $is_clone,
            ];
            $this->News_Model->update($id, $data1);
            print_r($this->request->getVar('notification'));
            if($this->request->getVar('notification') == '1' && $this->request->getVar('status')=='1'){
                $fcmMsg = array(
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'message' => $this->request->getVar('title'),
                    'body' => $this->request->getVar('title'),
                    'news_id' => $id,
                    'language_id' => $this->request->getVar('language'),
                    'type' => 'newlyadded',
                );
                $this->send_notification($fcmMsg);
            }
            $this->session->setFlashdata('success', 'Data Update successfully.!');
        }
        return redirect('news');
    }
    public function update_des()
    {
        if (ALLOW_MODIFICATION) {
            $this->session->setFlashdata('error', DEMO_VERSION_MSG);
        } else {
            $id = $this->request->getVar('edit_des_id');
            $data = [
                'description' => $this->request->getVar('des'),
            ];
            $this->News_Model->update($id, $data);
            $this->session->setFlashdata('success', 'Data Update successfully.!');
        }
        return redirect('news');
    }
    public function delete()
    {
        if (ALLOW_MODIFICATION) {
            return $this->response->setJSON(FALSE);
        } else {
            $id = $this->request->getVar('id');
            $dirPath = $this->target_path . $id;
            $data = $this->db->table('tbl_news_image')->where('news_id', $id)->get()->getResult();
            for ($i = 0; $i < count($data); $i++) {
                $otherImage = $dirPath . '/' . $data[$i]->other_image;
                if (file_exists($otherImage)) {
                    unlink($otherImage);
                }
            }
            if (is_dir($dirPath)) {
                rmdir($dirPath);
            }
            $this->db->table('tbl_news_image')->where('news_id', $id)->delete();
            $image_url = $this->request->getVar('image_url');
            $con_value = $this->request->getVar('con_value');
            if (file_exists($con_value)) {
                unlink($con_value);
            }
            if (file_exists($image_url)) {
                unlink($image_url);
            }
            $this->News_Model->where('id', $id)->delete();
            return $this->response->setJSON(TRUE);
        }
    }
    public function indexImage($id)
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            $this->data['news'] = $this->db->table('tbl_news')->where('id', $id)->get()->getResult();
            return view('news-image', $this->data);
        }
    }
    public function storeImage()
    {
        if (ALLOW_MODIFICATION) {
            $this->session->setFlashdata('error', DEMO_VERSION_MSG);
        } else {
            $news_id = $this->request->getVar('news_id');
            if (!is_dir($this->target_path . $news_id)) {
                mkdir($this->target_path . $news_id, 0777, TRUE);
            }
            foreach ($this->request->getFileMultiple('file') as $file1) {
                $fileName1 = microtime(TRUE) . '.' . $file1->getClientExtension();
                $extension = $file1->getClientExtension();
                $allowedExts = array("gif", "jpeg", "jpg", "png", "GIF", "JPEG", "JPG", "PNG");
                if (in_array($extension, $allowedExts)) {
                    if ($file1->isValid() && !$file1->hasMoved()) {
                        $file1->move($this->target_path . $news_id, $fileName1);
                    }
                    if ($file1->hasMoved()) {
                        $data = [
                            'news_id'     => $news_id,
                            'other_image' => $fileName1
                        ];
                        $this->db->table('tbl_news_image')->insert($data);
                    }
                }
            }
            $this->session->setFlashdata('success', 'Image inserted successfully.!');
        }
        return redirect()->back();
    }
    public function deleteImage()
    {
        if (ALLOW_MODIFICATION) {
            return $this->response->setJSON(FALSE);
        } else {
            $id = $this->request->getVar('id');
            $image_url = $this->request->getVar('image_url');
            if (file_exists($image_url)) {
                unlink($image_url);
            }
            $this->db->table('tbl_news_image')->where('id', $id)->delete();
            return $this->response->setJSON(TRUE);
        }
    }
    public function upload_img()
    {
        $baseurl = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $accepted_origins = array($baseurl . "://" . $_SERVER['HTTP_HOST']);
        if (!is_dir($this->target_path)) {
            mkdir($this->target_path, 0777, TRUE);
        }
        $imageFolder = $this->target_path; // Images upload path
        reset($_FILES);
        $temp = current($_FILES);
        if (is_uploaded_file($temp['tmp_name'])) {
            if (isset($_SERVER['HTTP_ORIGIN'])) {
                // Same-origin requests won't set an origin. If the origin is set, it must be valid.
                if (in_array($_SERVER['HTTP_ORIGIN'], $accepted_origins)) {
                    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
                } else {
                    header("HTTP/1.1 403 Origin Denied");
                    return;
                }
            }
            // Sanitize input
            if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
                header("HTTP/1.1 400 Invalid file name.");
                return;
            }
            $filetype = $_POST['filetype']; // file type
            // Valid extension
            if ($filetype == 'image') {
                $valid_ext = array('png', 'jpeg', 'jpg');
            } else if ($filetype == 'media') {
                $valid_ext = array('mp4', 'mp3');
            }
            $location = $imageFolder . $temp['name']; // Location
            $file_extension = pathinfo($location, PATHINFO_EXTENSION); // file extension
            $file_extension = strtolower($file_extension);
            // Accept upload if there was no origin, or if it is an accepted origin
            // $filename = $temp['name'];
            $filename = microtime(true) . '.' . $file_extension;
            $location = $imageFolder . $filename;
            $return_filename = "";
            // Check extension
            if (in_array($file_extension, $valid_ext)) {
                // Upload file
                if (move_uploaded_file($temp['tmp_name'], $location)) {
                    $return_filename = $filename;
                }
            }
            echo $return_filename;
        } else {
            header("HTTP/1.1 500 Server Error"); // Notify editor that the upload failed
        }
    }
    public function clone ()
    {
        if (ALLOW_MODIFICATION) {
            $this->session->setFlashdata('error', DEMO_VERSION_MSG);
        } else {
            $id = $this->request->getVar('id');
            $data_news = $this->db->table('tbl_news')->where('id', $id)->get()->getResult();
            if (isset($data_news)) {
                foreach ($data_news as $row) {
                    unset($row->id);
                    $row->status = 0;
                    $row->is_clone = 1;
                    $row->date = $this->toDate;
                    $result = $this->db->table('tbl_news')->insert($row);
                }
                $data_newsimages = $this->db->table('tbl_news_image')->where('news_id', $id)->get()->getResult();
                if (isset($data_newsimages)) {
                    foreach ($data_newsimages as $row) {
                        unset($row->id);
                        $news_id = $this->db->table('tbl_news')->select('id')->limit(1)->orderBy('id', 'DESC')->get()->getResult();
                        $row->news_id = json_decode($news_id[0]->id, true);
                        $result = $this->db->table('tbl_news_image')->insert($row);
                    }
                }
            }
            if ($result) {
                return $this->response->setJSON(TRUE);
            } else {
                return $this->response->setJSON(TRUE);
            }
        }
    }
    public function send_notification($fcmMsg) {
        $data = $this->db->table('tbl_settings')->where('type', 'fcm_sever_key')->get()->getResult();
        define('API_ACCESS_KEY', $data[0]->message);

        $devicetoken1 = array();
        $devicetoken = $this->db->table('tbl_token')->get()->getResult();
        foreach ($devicetoken as $value) {
            $devicetoken1[] = $value->token;
        }

        $registrationIDs_chunks = array_chunk($devicetoken1, 1000);
        $success = $failure = 0;

        foreach ($registrationIDs_chunks as $registrationIDs) {
            $fcmFields = array(
                'registration_ids' => $registrationIDs, // expects an array of ids
                'priority' => 'high',
                'notification' => $fcmMsg,
                'data' => $fcmMsg
            );

            $headers = array(
                'Authorization: key=' . API_ACCESS_KEY,
                'Content-Type: application/json'
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmFields));
            $result = curl_exec($ch);

            if ($result === FALSE) {
                die('Curl failed: ' . curl_error($ch));
            }
            //Now close the connection
            curl_close($ch);
        }
    }
}