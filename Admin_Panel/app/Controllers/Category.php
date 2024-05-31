<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\Category_Model;
use App\Models\Subcategory_Model;
use App\Models\News_Model;

class Category extends Controller
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

        $this->target_path = 'public/images/category';
    }

    public function index()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            return view('category', $this->data);
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
            if ($image->hasMoved()) {
                $data = [
                    'category_name' => $this->request->getVar('name'),
                    'image' => $newName,
                    'language_id' => $this->request->getVar('language'),
                ];
                $this->Category_Model->insert($data);
                $this->session->setFlashdata('success', 'Data inserted successfully.!');
            } else {
                $this->session->setFlashdata('error', 'Something went wrong, please try again.!');
            }
        }
        return redirect('category');
    }

    public function update()
    {
       if (is_modification_allowed()) {
            $this->session->setFlashdata('error', DEMO_VERSION_MSG);
        } else {
            $image = $this->request->getFile('file');
            $id = $this->request->getVar('edit_id');

            if ($image->getClientName() == '') {
                $data = ['category_name' => $this->request->getVar('name'), 'language_id' => $this->request->getVar('edit_language'),];
                $this->Category_Model->update($id, $data);
                $this->session->setFlashdata('success', 'Data Update successfully.!');
            } else {
                $newName = microtime(TRUE) . '.' . $image->getClientExtension();
                if ($image->isValid() && !$image->hasMoved()) {
                    $image->move($this->target_path, $newName);
                }
                if ($image->hasMoved()) {
                    $image_url = $this->request->getVar('image_url');
                    if (file_exists($image_url)) {
                        unlink($image_url);
                    }
                    $data = [
                        'category_name' => $this->request->getVar('name'),
                        'image' => $newName,
                        'language_id' => $this->request->getVar('edit_language'),
                    ];
                    $this->Category_Model->update($id, $data);
                    $this->session->setFlashdata('success', 'Data Update successfully.!');
                } else {
                    $this->session->setFlashdata('error', 'Something went wrong, please try again.!');
                }
            }
        }
        return redirect('category');
    }

    public function delete()
    {
        if (is_modification_allowed()) {
            return $this->response->setJSON(FALSE);
        } else {
            $id = $this->request->getVar('id');
            $res = $this->db->table('tbl_news')->where('category_id', $id)->get()->getResult();
            foreach ($res as $value) {
                if (!empty($value->image) && file_exists('public/images/news/' . $value->image)) {
                    unlink('public/images/news/' . $value->image);
                }
            }
            $this->News_Model->where('category_id', $id)->delete();

            $res1 = $this->db->table('tbl_subcategory')->where('category_id', $id)->get()->getResult();
            foreach ($res1 as $value1) {
                if (!empty($value1->image) && file_exists('public/images/subcategory/' . $value1->image)) {
                    unlink('public/images/subcategory/' . $value1->image);
                }
            }
            $this->Subcategory_Model->where('category_id', $id)->delete();

            $image_url = $this->request->getVar('image_url');
            if (file_exists($image_url)) {
                unlink($image_url);
            }
            $this->Category_Model->where('id', $id)->delete();
            return $this->response->setJSON(TRUE);
        }
    }

}