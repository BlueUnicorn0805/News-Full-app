<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\Pages_Model;

class Pages extends Controller
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
        $this->Pages_Model = new Pages_Model();
        $this->db = \Config\Database::connect();
        $this->data['app_name'] = $this->db->table('tbl_settings')->where('type', 'app_name')->get()->getResult();
        $this->data['app_logo'] = $this->db->table('tbl_settings')->where('type', 'app_logo')->get()->getResult();
        $this->data['languages'] = $this->db->table('tbl_languages')->where('status', 1)->get()->getResult();
        $this->target_path = 'public/images/pages/';
    }
    public function index()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            $pages = $this->Pages_Model->orderBy('id', 'DESC')->findAll();
            return view('pages', $this->data);
        }
    }
    public function store()
    {
        if (is_modification_allowed()) {
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
            $language_id = $this->request->getVar('language');
            if ($this->request->getVar('termspolicy_mode') && $this->request->getVar('termspolicy_mode') == 1) {
                $data = array(
                    'is_termspolicy' => 0,
                    'language_id'    => $this->request->getVar('language'),
                );
                $this->db->table('tbl_pages')->where('language_id', $language_id)->update($data);
            }
            if ($this->request->getVar('privacypolicy_mode') && $this->request->getVar('privacypolicy_mode') == 1) {
                $data = array(
                    'is_privacypolicy' => 0,
                    'language_id'      => $this->request->getVar('language'),
                );
                $this->db->table('tbl_pages')->where('language_id', $language_id)->update($data);
            }
            if(empty($this->request->getVar('slug') )){
                $page_slug = slug($this->request->getVar('title'));
            }
            else{
                $page_slug = slug($this->request->getVar('slug'));
            }
           
            if ($image->hasMoved()) {
                $data = [
                    'title'            => $this->request->getVar('title'),
                    'slug'             => $page_slug,
                    'meta_description' => $this->request->getVar('meta_description') ?? '',
                    'meta_keywords'    => $this->request->getVar('meta_keywords') ?? '',
                    'language_id'      => $this->request->getVar('language'),
                    'page_content'     => $this->request->getVar('page_content'),
                    'page_icon'        => $newName,
                    'is_termspolicy'   => $this->request->getVar('termspolicy_mode'),
                    'is_privacypolicy' => $this->request->getVar('privacypolicy_mode'),
                ];
               
                $this->Pages_Model->insert($data);
                $this->session->setFlashdata('success', 'Data inserted successfully.!');
            }
        }
        return redirect('pages');
    }
    public function update()
    {
        if (is_modification_allowed()) {
            $this->session->setFlashdata('error', DEMO_VERSION_MSG);
        } else {
            $id = $this->request->getVar('edit_id');
            $image = $this->request->getFile('file');
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
                    $data = ['page_icon' => $newName];
                    $this->Pages_Model->update($id, $data);
                } else {
                    $this->session->setFlashdata('error', 'Something went wrong, please try again.!');
                    return redirect('pages');
                }
            }
            $language_id = $this->request->getVar('edit_language');
            
            if ($this->request->getVar('termspolicy_mode') && $this->request->getVar('termspolicy_mode') == 1) {
                $data = array(
                    'is_termspolicy' => 0,
                    'language_id'    => $this->request->getVar('edit_language'),
                );
                $this->db->table('tbl_pages')->where('language_id', $language_id)->update($data);
            }
            if ($this->request->getVar('privacypolicy_mode') && $this->request->getVar('privacypolicy_mode') == 1) {
                $data = array(
                    'is_privacypolicy' => 0,
                    'language_id'      => $this->request->getVar('edit_language'),
                );
                $this->db->table('tbl_pages')->where('language_id', $language_id)->update($data);
            }
            $data = [
                'title'            => $this->request->getVar('title'),
                'slug'             => $this->request->getVar('slug'),
                'meta_description' => $this->request->getVar('meta_description') ?? '',
                'meta_keywords'    => $this->request->getVar('meta_keywords') ?? '',
                'language_id'      => $this->request->getVar('edit_language'),
                'page_content'     => $this->request->getVar('page_content'),
                'is_termspolicy'   => ($this->request->getVar('is_termspolicy') == 'on') ? 1 : 0,
                'is_privacypolicy' => ($this->request->getVar('is_privacypolicy') == 'on') ? 1 : 0,
                'status'           =>  $this->request->getVar('status'),
            ];
            $this->Pages_Model->update($id, $data);
            $this->session->setFlashdata('success', 'Data Update successfully.!');
        }
        return redirect('pages');
    }
    public function delete()
    {
        if (is_modification_allowed()) {
            return $this->response->setJSON(FALSE);
        } else {
            $id = $this->request->getVar('id');
            $is_custom = $this->request->getVar('is_custom');
            if ($is_custom == '1') {
                $dirPath = $this->target_path;
                $data = $this->db->table('tbl_pages')->where('id', $id)->get()->getResult();
                for ($i = 0; $i < count($data); $i++) {
                    $page_icon = $dirPath . '/' . $data[$i]->page_icon;
                    if (file_exists($page_icon)) {
                        unlink($page_icon);
                    }
                }
                $this->Pages_Model->where('id', $id)->delete();
                $this->session->setFlashdata('success', 'Page Delete successfully.!');
                return $this->response->setJSON(TRUE);
            } else {
                $this->session->setFlashdata('error', 'Default pages not deletable');
                return $this->response->setJSON(TRUE);
            }
        }
    }
    public function upload_pages_img()
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
}