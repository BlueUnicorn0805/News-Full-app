<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\Survey_Model;
use App\Models\Survey_Option_Model;
use App\Models\Survey_Result_Model;

class Survey extends Controller
{

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        $this->session = \Config\Services::session();
        $this->session->start();

        $this->helpers = helper('SystemSettings');

        $this->Survey_Model = new Survey_Model();
        $this->Survey_Option_Model = new Survey_Option_Model();
        $this->Survey_Result_Model = new Survey_Result_Model();

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
            return view('survey', $this->data);
        }
    }

    public function store_question()
    {

        if (is_modification_allowed()) {
            $this->session->setFlashdata('error', DEMO_VERSION_MSG);
        } else {

            $data = [
                'question' => $this->request->getVar('question'),
                'status' => '1',
                'language_id' => $this->request->getVar('language'),
            ];
            $this->Survey_Model->insert($data);

            $question_id = $this->Survey_Model->insertID;
            $option = $this->request->getVar('option');
            if(count($option) > 1){
            foreach ($option as $value) {
                $option_data = [
                    'question_id' => $question_id,
                    'options' => $value,
                    'counter' => '0'
                ];

                $this->Survey_Option_Model->insert($option_data);
            }

            $this->session->setFlashdata('success', 'Data inserted successfully.!');
        }else{
            $this->session->setFlashdata('error', 'Maximum 2 options are allowed.');
        }
        }
        return redirect('survey');
    }

    public function update_question()
    {

        if (is_modification_allowed()) {
            $this->session->setFlashdata('error', DEMO_VERSION_MSG);
        } else {
            $id = $this->request->getVar('edit_id');
            $data = [
                'question' => $this->request->getVar('question'),
                'status' => $this->request->getVar('edit_status'),
                'language_id' => $this->request->getVar('edit_language'),
            ];
            $this->Survey_Model->update($id, $data);
            $this->session->setFlashdata('success', 'Data updated successfully.!');
        }
        return redirect('survey');
    }

    public function delete_question()
    {

        if (is_modification_allowed()) {
            return $this->response->setJSON(FALSE);
        } else {
            $id = $this->request->getVar('id');
            $this->Survey_Option_Model->where('question_id', $id)->delete();
            $this->Survey_Result_Model->where('question_id', $id)->delete();
            $this->Survey_Model->where('id', $id)->delete();
            return $this->response->setJSON(TRUE);
        }
    }

    public function get_survey_option($id)
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            $this->data['option'] = $this->db->table('tbl_survey_option')->where('question_id', $id)->get()->getResult();
            $this->data['question'] = $this->db->table('tbl_survey_question')->where('id', $id)->get()->getResult();

            return view('survey_option', $this->data);
        }
    }

    public function store_option()
    {
        if (is_modification_allowed()) {
            $this->session->setFlashdata('error', DEMO_VERSION_MSG);
        } else {
            $question_id = $this->request->getVar('question_id');
            $option = $this->request->getVar('option');
            foreach ($option as $value) {
                $option_data = [
                    'question_id' => $question_id,
                    'options' => $value,
                    'counter' => '0'
                ];

                $this->Survey_Option_Model->insert($option_data);
            }

            $this->session->setFlashdata('success', 'Data inserted successfully.!');
        }
        return redirect()->back();
    }

    public function update_option()
    {
        if (is_modification_allowed()) {
            $this->session->setFlashdata('error', DEMO_VERSION_MSG);
        } else {

            $id = $this->request->getVar('edit_id');
            $question_id = $this->request->getVar('edit_question_id');
            $option = $this->request->getVar('option');

            $option_data = [
                'question_id' => $question_id,
                'options' => $option,
                'counter' => '0'
            ];

            $this->Survey_Option_Model->update($id, $option_data);

            $this->session->setFlashdata('success', 'Data updated successfully.!');
        }
        return redirect()->back();
    }

    public function delete_option()
    {

        if (is_modification_allowed()) {
            return $this->response->setJSON(FALSE);
        } else {
            $question_id = $this->request->getVar('question_id');
            $id = $this->request->getVar('id');
            $this->Survey_Option_Model->where('id', $id)->delete();
            $this->Survey_Result_Model->where('option_id', $id)->where('question_id', $question_id)->delete();
            return $this->response->setJSON(TRUE);
        }
    }

}