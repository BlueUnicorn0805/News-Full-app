<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\LiveStreaming_Model;

class LiveStreaming extends Controller
{

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        $this->session = \Config\Services::session();
        $this->session->start();

        $this->helpers = helper('SystemSettings');

        $this->LiveStreaming_Model = new LiveStreaming_Model();

        $this->db = \Config\Database::connect();
        $this->data['app_name'] = $this->db->table('tbl_settings')->where('type', 'app_name')->get()->getResult();
        $this->data['app_logo'] = $this->db->table('tbl_settings')->where('type', 'app_logo')->get()->getResult();
        $this->data['languages'] = $this->db->table('tbl_languages')->where('status', 1)->get()->getResult();

        $this->target_path = 'public/images/liveStreaming';
       
    }

    public function index()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            return view('live_streaming', $this->data);
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
                    'title' => $this->request->getVar('title'),
                    'image' => $newName,
                    'type' => $this->request->getVar('type'),
                    'url' => $this->request->getVar('url'),
                    'language_id' => $this->request->getVar('language'),
                ];
                $this->LiveStreaming_Model->insert($data);
                $this->session->setFlashdata('success', 'Data inserted successfully.!');
            } else {
                $this->session->setFlashdata('error', 'Something went wrong, please try again.!');
            }

        }
        return redirect('live_streaming');
    }

    public function update()
    {
        if (is_modification_allowed()) {
            $this->session->setFlashdata('error', DEMO_VERSION_MSG);
        } else {
            $image = $this->request->getFile('file');
            $id = $this->request->getVar('edit_id');

            if ($image->getClientName() == '') {
                $data = [
                    'title' => $this->request->getVar('title'),
                    'type' => $this->request->getVar('type'),
                    'url' => $this->request->getVar('url'),
                    'language_id' => $this->request->getVar('edit_language'),
                ];
                $this->LiveStreaming_Model->update($id, $data);
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
                        'title' => $this->request->getVar('title'),
                        'image' => $newName,
                        'type' => $this->request->getVar('type'),
                        'url' => $this->request->getVar('url'),
                        'language_id' => $this->request->getVar('edit_language'),
                    ];
                    $this->LiveStreaming_Model->update($id, $data);
                    $this->session->setFlashdata('success', 'Data Update successfully.!');
                } else {
                    $this->session->setFlashdata('error', 'Something went wrong, please try again.!');
                }
            }
        }
        return redirect('live_streaming');
    }

    public function delete()
    {
        if (is_modification_allowed()) {
            return $this->response->setJSON(FALSE);
        } else {
            $id = $this->request->getVar('id');

            $image_url = $this->request->getVar('image_url');
            if (file_exists($image_url)) {
                unlink($image_url);
            }
            $this->LiveStreaming_Model->where('id', $id)->delete();
            return $this->response->setJSON(TRUE);
        }
    }

}