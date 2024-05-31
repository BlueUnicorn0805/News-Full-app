<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\BreakingNews_Model;

class BreakingNews extends Controller
{

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        $this->session = \Config\Services::session();
        $this->session->start();

        $this->helpers = helper('SystemSettings');

        $this->BreakingNews_Model = new BreakingNews_Model();

        $this->db = \Config\Database::connect();
        $this->data['app_name'] = $this->db->table('tbl_settings')->where('type', 'app_name')->get()->getResult();
        $this->data['app_logo'] = $this->db->table('tbl_settings')->where('type', 'app_logo')->get()->getResult();
        $this->data['languages'] = $this->db->table('tbl_languages')->where('status', 1)->get()->getResult();

        $this->target_path = 'public/images/breaking_news/';
        $this->target_path_video = 'public/images/breaking_news_video';
      
    }

    public function index()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            return view('breaking_news', $this->data);
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
                $data = [
                    'title' => $this->request->getVar('title'),
                    'content_type' => $this->request->getVar('content_type'),
                    'content_value' => $content_value,
                    'description' => $this->request->getVar('des'),
                    'image' => $newName,
                    'language_id' => $this->request->getVar('language'),
                ];
                $this->BreakingNews_Model->insert($data);
                $this->session->setFlashdata('success', 'Data inserted successfully.!');
            } else {
                $this->session->setFlashdata('error', 'Something went wrong, please try again.!');
            }
        }
        return redirect('breaking_news');
    }

    public function update()
    {
        if (is_modification_allowed()) {
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
                    $this->BreakingNews_Model->update($id, $data);
                } else {
                    $this->session->setFlashdata('error', 'Something went wrong, please try again.!');
                    return redirect('news');
                }
            }
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
            $data1 = [
                'title' => $this->request->getVar('title'),
                'content_type' => $this->request->getVar('content_type'),
                'content_value' => $content_value,
                'description' => $this->request->getVar('des'),
                'language_id' => $this->request->getVar('edit_language'),
            ];
            $this->BreakingNews_Model->update($id, $data1);
            $this->session->setFlashdata('success', 'Data Update successfully.!');
        }
        return redirect('breaking_news');
    }

    public function delete()
    {
        if (is_modification_allowed()) {
            return $this->response->setJSON(FALSE);
        } else {
            $id = $this->request->getVar('id');
            $image_url = $this->request->getVar('image_url');
            $con_value = $this->request->getVar('con_value');
            if (file_exists($con_value)) {
                unlink($con_value);
            }
            if (file_exists($image_url)) {
                unlink($image_url);
            }
            $this->BreakingNews_Model->where('id', $id)->delete();
            return $this->response->setJSON(TRUE);
        }
    }

}