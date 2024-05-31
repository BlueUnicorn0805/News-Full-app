<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\AdSpaces_Model;

class AdSpaces extends Controller {

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        $this->session = \Config\Services::session();
        $this->session->start();

        $this->helpers = helper('SystemSettings');
        date_default_timezone_set(get_system_timezone());
        $this->toDate = date('Y-m-d');
		
		$this->AdSpaces_Model = new AdSpaces_Model();

        $this->db = \Config\Database::connect();
        $this->data['app_name'] = $this->db->table('tbl_settings')->where('type', 'app_name')->get()->getResult();
        $this->data['app_logo'] = $this->db->table('tbl_settings')->where('type', 'app_logo')->get()->getResult();
        $this->data['languages'] = $this->db->table('tbl_languages')->where('status', 1)->get()->getResult();
        $this->data['featured_sections'] = $this->db->table('tbl_languages')->where('status', 1)->get()->getResult();
		$this->target_path = 'public/images/ad_spaces/';
    }

    public function index() {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            $this->data['app_version'] = $this->db->table('tbl_settings')->where('type', 'app_version')->get()->getResult();
            return view('ad_spaces', $this->data);
        }
    }

    public function store() {
        if (is_modification_allowed()) {
            $this->session->setFlashdata('error', DEMO_VERSION_MSG);
        } else {
            $language_id = $this->request->getVar('language_id');
            $ad_space = $this->request->getVar('ad_space');
            $check_exist = $this->db->table('tbl_ad_spaces')->where('language_id', $language_id)->where('ad_space', $ad_space)->get()->getResult();
            if(COUNT($check_exist) == 0){
                if (!is_dir($this->target_path)) {
                    mkdir($this->target_path, 0777, TRUE);
                }
                $ad_image = $this->request->getFile('ad_image');
                $newName = microtime(TRUE) . '.' . $ad_image->getClientExtension();
                if ($ad_image->isValid() && !$ad_image->hasMoved()) {
                    $ad_image->move($this->target_path, $newName);
                }
                $web_ad_image = $this->request->getFile('web_ad_image');
                $web_newName = microtime(TRUE) . '.' . $ad_image->getClientExtension();
                if ($web_ad_image->isValid() && !$web_ad_image->hasMoved()) {
                    $web_ad_image->move($this->target_path, $web_newName);
                }
                if ($ad_image->hasMoved() && $web_ad_image->hasMoved()) {
                    $ad_space = $this->request->getVar('ad_space');
                    $string = explode("-", $ad_space);
                        if ($string[0] == 'featuredsection') {
                            $ad_featured_section_id	= $string[1];
                        } else {
                            $ad_featured_section_id	= 0;
                        }
                    $data = [
                        'language_id'       => $this->request->getVar('language_id'),
                        'ad_space'          => $this->request->getVar('ad_space'),
                        'ad_featured_section_id' => $ad_featured_section_id,
                        'ad_image'          => $newName,
                        'web_ad_image'      => $web_newName,
                        'ad_url'            => $this->request->getVar('ad_url'),
                        'created_at'        => $this->toDate,
                    ];
                    $this->AdSpaces_Model->insert($data);
                    $this->session->setFlashdata('success', 'Data inserted successfully.!');
                } else {
                    $this->session->setFlashdata('error', 'Something went wrong, please try again.!');
                }
            }
            else{
                $this->session->setFlashdata('error', 'Ad already added in this space.!');
            }
        }
    return redirect('ad_spaces');
    }

    public function update()
    {
        if (is_modification_allowed()) {
            $this->session->setFlashdata('error', DEMO_VERSION_MSG);
        } else {
            $language_id = $this->request->getVar('language_id');
            $ad_space = $this->request->getVar('ad_space');
            $edit_id = $this->request->getVar('edit_id');
           
            $check_exist = $this->db->table('tbl_ad_spaces')->where('language_id', $language_id)->where('ad_space', $ad_space)->where('id !=',$edit_id)->get()->getResult();
            // $db = \Config\Database::connect();  
            // // your queries here
            // $query = $db->getLastQuery();
            // $sql = $query->getQuery();
          
            if(COUNT($check_exist) == 0){
                $edit_id = $this->request->getVar('edit_id');
                $image = $this->request->getFile('ad_image');
                if ($image->getClientName() != '') {
                    echo 'app';
                    $newName = microtime(TRUE) . '.' . $image->getClientExtension();
                    if ($image->isValid() && !$image->hasMoved()) {
                        $image->move($this->target_path, $newName);
                    }
                    if ($image->hasMoved()) {
                        $image_url = $this->request->getVar('ad_image_url');
                        if (file_exists($image_url)) {
                            unlink($image_url);
                        }
                        $data_ad_image = ['ad_image' => $newName];
                        $this->AdSpaces_Model->update($edit_id, $data_ad_image);
                    } else {
                        $this->session->setFlashdata('error', 'Something went wrong, please try again.!');
                        return redirect('ad_spaces');
                    }
                }
                $web_image = $this->request->getFile('web_ad_image');
                if ($web_image->getClientName() != '') {
                    echo 'web';
                    $web_newName = microtime(TRUE) . '.' . $web_image->getClientExtension();
                    if ($web_image->isValid() && !$web_image->hasMoved()) {
                        $web_image->move($this->target_path, $web_newName);
                    }
                    if ($web_image->hasMoved()) {
                        $web_image_url = $this->request->getVar('web_ad_image_url');
                        if (file_exists($web_image_url)) {
                            unlink($web_image_url);
                        }
                        
                        $data_web_ad_image = ['web_ad_image' => $web_newName];
                        $this->AdSpaces_Model->update($edit_id, $data_web_ad_image);
                    } else {
                        $this->session->setFlashdata('error', 'Something went wrong, please try again.!');
                        return redirect('ad_spaces');
                    }
                }
                $ad_space = $this->request->getVar('ad_space');
                $string = explode("-", $ad_space);
                if ($string[0] == 'featuredsection') {
                    $ad_featured_section_id	= $string[1];
                } else {
                    $ad_featured_section_id	= 0;
                }
                $data_ad_space = [
                    'language_id'       => $this->request->getVar('language_id'),
                    'ad_space'          => $this->request->getVar('ad_space'),
                    'ad_featured_section_id' => $ad_featured_section_id,
                    'ad_url'            => $this->request->getVar('ad_url'),
                    'status'            => $this->request->getVar('status'),
                ];
            
                $this->AdSpaces_Model->update($edit_id, $data_ad_space);
                $this->session->setFlashdata('success', 'Data Update successfully.!');
            }
            else{
                $this->session->setFlashdata('error', 'Ad already added in this space.!');
            }
        }
    return redirect('ad_spaces');
    }

     public function delete()
    {
        if (is_modification_allowed()) {
            return $this->response->setJSON(FALSE);
        } else {
            $id = $this->request->getVar('id');
            $ad_image = $this->request->getVar('image');
			$image_url = $this->target_path .'/'.$ad_image;
            if (file_exists($image_url)) {
                unlink($image_url);
            }
            $web_ad_image = $this->request->getVar('web_image');
			$web_image_url = $this->target_path .'/'.$web_ad_image;
            if (file_exists($web_image_url)) {
                unlink($web_image_url);
            }
            $this->AdSpaces_Model->where('id', $id)->delete();
            return $this->response->setJSON(TRUE);
        }
    }

}

?>