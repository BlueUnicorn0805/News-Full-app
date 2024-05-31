<?php
namespace App\Controllers;

use App\Models\BreakingNews_Model;
use App\Models\Category_Model;
use App\Models\Language_Model;
use App\Models\News_Model;
use App\Models\Subcategory_Model;
use App\Models\Survey_Model;
use App\Models\Pages_Model;
use App\Models\Tag_Model;
use CodeIgniter\Controller;
use CodeIgniter\Files\File;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Language extends Controller
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        $this->session = \Config\Services::session();
        $this->session->start();
        $this->helpers = helper('SystemSettings');
        $this->Category_Model = new Category_Model();
        $this->Subcategory_Model = new Subcategory_Model();
        $this->News_Model = new News_Model();
        $this->Language_Model = new Language_Model();
        $this->BreakingNews_Model = new BreakingNews_Model();
        $this->Tag_Model = new Tag_Model();
        $this->Survey_Model = new Survey_Model();
        $this->Pages_Model = new Pages_Model();
        $this->target_path = 'app/Language/';
        $this->db = \Config\Database::connect();
        $this->data['app_name'] = $this->db->table('tbl_settings')->where('type', 'app_name')->get()->getResult();
        $this->data['app_logo'] = $this->db->table('tbl_settings')->where('type', 'app_logo')->get()->getResult();
        $this->data['languages'] = $this->db->table('tbl_languages')->get()->getResult();
        $this->data['default_language'] = $this->db->table('tbl_settings')->where('type', 'default_language')->get()->getResult();
        $this->validation = \Config\Services::validation();

    }
    public function index()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            return view('language', $this->data);
        }
    }
    public function language_sample()
    {
        $filePath = $this->target_path . 'en.json';
        $headers = ['Content-Type: application/json'];
        $fileName = 'en.json';
        if (file_exists($filePath)) {
            return $this->response->download($filePath, null)->setFileName($fileName);
        } else {
            $this->session->setFlashdata('error', 'Can not Download File !');
            return redirect('language');
        }
    }
    public function delete()
    {
        if (is_modification_allowed()) {
            $this->session->setFlashdata('error', DEMO_VERSION_MSG);
        } else {
        $id = $this->request->getVar('id');
        $res = $this->db->table('tbl_languages')->where('id', $id)->get()->getResult();
        $data = [
            'status' => 0,
        ];
        $this->Language_Model->update($id, $data);
        // delete Category
        $this->Category_Model->where('language_id', $id)->delete();
        //delete Subcategory
        $this->Subcategory_Model->where('language_id', $id)->delete();
        //delete News
        $this->News_Model->where('language_id', $id)->delete();
        //delete Breaking news
        $this->BreakingNews_Model->where('language_id', $id)->delete();
        //delete Tags
        $this->Tag_Model->where('language_id', $id)->delete();
        //delete Survey
        $this->Survey_Model->where('language_id', $id)->delete();
        //delete Pages
        $this->Pages_Model->where('language_id', $id)->delete();
        }
        // return redirect('language');
        return $this->response->setJSON(true);
    }
    public function update()
    {
        if (is_modification_allowed()) {
            $this->session->setFlashdata('error', DEMO_VERSION_MSG);
        } else {
        $id = $this->request->getVar('language_id');
        $sql = $this->db->table('tbl_languages')->where('id', $id)->get()->getResult();
        $file = $this->request->getFile('file');
        if ($file->getClientName() != '') {
            $newName = $sql[0]->code . '.' . $file->getClientExtension();
            $path = $this->target_path . $newName;
            if ($file->isValid() && !$file->hasMoved()) {
                if (file_exists($path)) {
                    unlink($path);
                    $file->move($this->target_path, $newName);
                } else {
                    $file->move($this->target_path, $newName);
                }
            }
        }
        $flag = $this->request->getFile('flag');
        if ($flag->getClientName() != '') {
            $newName = $sql[0]->code . '.' . $flag->getClientExtension();
            $this->target_path = 'public/images/flags/';
            $flagpath = $this->target_path . $newName;
            if ($flag->isValid() && !$flag->hasMoved()) {
                if (!is_dir($this->target_path)) {
                    mkdir($this->target_path, 0777, TRUE);
                }
                if (file_exists($flagpath)) {
                    unlink($flagpath);
                    $flag->move($this->target_path, $newName);
                } else {
                    $flag->move($this->target_path, $newName);
                }
            }
        } else {
            $newName = $sql[0]->image;
        }
        $data = [
            'status' => $this->request->getVar('language_status'),
            'isRTL'  => $this->request->getVar('isRTL'),
            'image'  => $newName,
            'language'  => $this->request->getVar('language'),
            'display_name'  => $this->request->getVar('display_name'),
            'code' => $this->request->getVar('code'),
        ];
		
        $default_language = $this->db->table('tbl_settings')->where('type', 'default_language')->get()->getResult();
        $default_language = json_decode($default_language[0]->message, true);
        if ($default_language == $id && $this->request->getVar('language_status') == 0) {
            $this->session->setFlashdata('error', 'Default Language Can not be Disabled');
        } else {
            $this->Language_Model->update($id, $data);
            $this->session->setFlashdata('success', 'Data Update successfully.!');
        }
        }
        return redirect('language');
    }
    public function store()
    {
        if (is_modification_allowed()) {
            $this->session->setFlashdata('error', DEMO_VERSION_MSG);
        } else {
        $rules = 
            [
                'code' => 'is_unique[tbl_languages.code]',
            ];
        if($this->validate($rules)){
            $language = $this->request->getVar('language');
            $display_name = $this->request->getVar('display_name');
            $code = $this->request->getVar('code');
            $isRTL = $this->request->getVar('isRTL') ?? 0;
            
            $file = $this->request->getFile('file');
            $newName = $code . '.' . $file->getClientExtension();
            $path = $this->target_path . $newName;
            if ($file->isValid() && !$file->hasMoved()) {
                if (file_exists($path)) {
                    unlink($path);
                    $file->move($this->target_path, $newName);
                } else {
                    $file->move($this->target_path, $newName);
                }
            }
            $flag = $this->request->getFile('flag');
            $newName = $code . '.' . $flag->getClientExtension();
            $this->target_path = 'public/images/flags/';
            $flagpath = $this->target_path . $newName;
            if ($flag->isValid() && !$flag->hasMoved()) {
                if (!is_dir($this->target_path)) {
                    mkdir($this->target_path, 0777, TRUE);
                }
                if (file_exists($flagpath)) {
                    unlink($flagpath);
                    $flag->move($this->target_path, $newName);
                } else {
                    $flag->move($this->target_path, $newName);
                }
            }
            $data = [
                'status' => 1,
                'isRTL'  => $isRTL,
                'image'  => $newName,
                'language'  => $language,
                'display_name'  => $display_name,
                'code' => $code
            ];
            

            $this->Language_Model->insert($data);
            $this->session->setFlashdata('success', 'Data store successfully.!');
            return redirect('language');
        }else{
            $errors = $this->validation->getErrors();
            $this->session->setFlashdata('error', $errors['code']);
            
        }
        }
        return redirect('language');
    }
}