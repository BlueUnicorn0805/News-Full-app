<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\Tag_Model;

class Tag extends Controller
{

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        $this->session = \Config\Services::session();
        $this->session->start();

        $this->helpers = helper('SystemSettings');
        $this->Tag_Model = new Tag_Model();

        $this->db = \Config\Database::connect();
        $this->data['app_name'] = $this->db->table('tbl_settings')->where('type', 'app_name')->get()->getResult();
        $this->data['app_logo'] = $this->db->table('tbl_settings')->where('type', 'app_logo')->get()->getResult();
        $this->data['languages'] = $this->db->table('tbl_languages')->where('status', 1)->get()->getResult();
        
    }

    public function index()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            return view('tag', $this->data);
        }
    }

    public function store()
    {
        if (is_modification_allowed()) {
            $this->session->setFlashdata('error', DEMO_VERSION_MSG);
        } else {
            $this->Tag_Model->insert([
                'tag_name' => $this->request->getVar('name'),
                'language_id' => $this->request->getVar('language'),
            ]);
            $this->session->setFlashdata('success', 'Data inserted successfully.!');
        }
        return redirect('tag');
    }

    public function update()
    {
        if (is_modification_allowed()) {
            $this->session->setFlashdata('error', DEMO_VERSION_MSG);
        } else {
            $id = $this->request->getVar('edit_id');
            $data = [
                'tag_name' => $this->request->getVar('name'),
                'language_id' => $this->request->getVar('edit_language'),
            ];
            $this->Tag_Model->update($id, $data);
            $this->session->setFlashdata('success', 'Data Update successfully.!');
        }
        return redirect('tag');
    }

    public function delete()
    {
        if (is_modification_allowed()) {
            return $this->response->setJSON(FALSE);
        } else {
            $id = $this->request->getVar('id');
            $this->Tag_Model->where('id', $id)->delete();
            return $this->response->setJSON(TRUE);
        }
    }

}