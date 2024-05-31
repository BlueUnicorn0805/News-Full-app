<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\Category_Model;
use App\Models\Subcategory_Model;
use App\Models\News_Model;

class Subcategory extends Controller
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

        $this->db = \Config\Database::connect();
        $this->data['app_name'] = $this->db->table('tbl_settings')->where('type', 'app_name')->get()->getResult();
        $this->data['app_logo'] = $this->db->table('tbl_settings')->where('type', 'app_logo')->get()->getResult();
        $this->data['languages'] = $this->db->table('tbl_languages')->where('status', 1)->get()->getResult();

        $this->target_path = 'public/images/subcategory';
        
    }

    public function index()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            $this->data['cate'] = $this->Category_Model->orderBy('id', 'DESC')->findAll();
            return view('subcategory', $this->data);
        }
    }

    public function store()
    {
        if (is_modification_allowed()) {
            $this->session->setFlashdata('error', DEMO_VERSION_MSG);
        } else {
                $data = [
                    'category_id' => $this->request->getVar('category_id'),
                    'subcategory_name' => $this->request->getVar('name'),
                    'image' => '',
                    'language_id' => $this->request->getVar('language'),
                ];
                $this->Subcategory_Model->insert($data);
                $this->session->setFlashdata('success', 'Data inserted successfully.!');
        
        }
        return redirect('subcategory');
    }

    public function update()
    {
        if (is_modification_allowed()) {
            $this->session->setFlashdata('error', DEMO_VERSION_MSG);
        } else {
            $id = $this->request->getVar('edit_id');
            $data = [
                'category_id' => $this->request->getVar('category_id'),
                'subcategory_name' => $this->request->getVar('name'),
                'language_id' => $this->request->getVar('edit_language'),
            ];
            $this->Subcategory_Model->update($id, $data);
            $this->session->setFlashdata('success', 'Data Update successfully.!');
        }
        return redirect('subcategory');
    }

    public function delete()
    {
        if (is_modification_allowed()) {
            return $this->response->setJSON(FALSE);
        } else {
            $id = $this->request->getVar('id');
            $res = $this->db->table('tbl_news')->where('subcategory_id', $id)->get()->getResult();
            foreach ($res as $value) {
                if (!empty($value->image) && file_exists('public/images/news/' . $value->image)) {
                    unlink('public/images/news/' . $value->image);
                }
            }
            $this->News_Model->where('subcategory_id', $id)->delete();

            $image_url = $this->request->getVar('image_url');
            if (file_exists($image_url)) {
                unlink($image_url);
            }
            $this->Subcategory_Model->where('id', $id)->delete();
            return $this->response->setJSON(TRUE);
        }
    }

}