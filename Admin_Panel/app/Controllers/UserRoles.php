<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\UserRoles_Model;

class UserRoles extends Controller {

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        $this->session = \Config\Services::session();
        $this->session->start();
        $this->helpers = helper('SystemSettings');
        $this->UserRoles_Model = new UserRoles_Model();
        $this->db = \Config\Database::connect();
        $this->data['app_name'] = $this->db->table('tbl_settings')->where('type', 'app_name')->get()->getResult();
        $this->data['app_logo'] = $this->db->table('tbl_settings')->where('type', 'app_logo')->get()->getResult();  
    }

    public function index() {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            return view('user_roles', $this->data);
        }
    }

    public function store() {
        if (is_modification_allowed()) {
            $this->session->setFlashdata('error', DEMO_VERSION_MSG);
        } else {
            $this->UserRoles_Model->insert([
                'role' => $this->request->getVar('role')
            ]);
            $this->session->setFlashdata('success', 'Data inserted successfully.!');
        }
        return redirect('user_roles');
    }

    public function update() {
       if (is_modification_allowed()) {
            $this->session->setFlashdata('error', DEMO_VERSION_MSG);
        } else {
            $id = $this->request->getVar('edit_id');
            $data = [
               'role' => $this->request->getVar('role')
            ];
            $this->UserRoles_Model->update($id, $data);
            $this->session->setFlashdata('success', 'Data Update successfully.!');
        }
        return redirect('user_roles');
    }

    public function delete() {
        if (is_modification_allowed()) {
            return $this->response->setJSON(FALSE);
        } else {
            $id = $this->request->getVar('id');
            $this->UserRoles_Model->where('id', $id)->delete();
            return $this->response->setJSON(TRUE);
        }
    }

}
