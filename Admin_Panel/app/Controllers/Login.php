<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\Login_Model;

class Login extends Controller
{
    protected $session;
    protected $encrypter;
    protected $Login_Model;
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->session = \Config\Services::session();
        $this->session->start();
        $this->helpers = helper('SystemSettings');
        $this->toDateTime = date('Y-m-d H:i:s');
        $this->encrypter = \Config\Services::encrypter();
        $this->Login_Model = new Login_Model();
        $this->db = \Config\Database::connect();
        $this->data['app_name'] = $this->db->table('tbl_settings')->where('type', 'app_name')->get()->getResult();
        $this->data['app_logo'] = $this->db->table('tbl_settings')->where('type', 'app_logo')->get()->getResult();
        $this->data['app_logo_full'] = $this->db->table('tbl_settings')->where('type', 'app_logo_full')->get()->getResult();
        $this->email = \Config\Services::email();
    }
    public function index()
    {
        $isLoggedIn = $this->session->get('isLoggedIn');
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return view('login', $this->data);
        } else {
            return redirect('dashboard');
        }
    }
    public function checklogin()
    {
        $password = $this->request->getPost('password');
        $username = $this->request->getPost('username');
        $data = $this->Login_Model->where('username', $username)->findAll();
        if ($data) {
            $pass = $this->encrypter->decrypt(base64_decode($data[0]['password']));
            $name = $data[0]['username'];
            if ($pass == $password && $username == $name) {
                $sessionArray = array(
                    'adminName'  => $name,
                    'adminId'    => $data[0]['id'],
                    'isLoggedIn' => TRUE
                );
                $this->session->set($sessionArray);
            } else {
                $this->session->setFlashdata('error', 'Invalid Username or Password');
            }
        } else {
            $this->session->setFlashdata('error', 'Invalid Username or Password');
        }
        return redirect('/');
    }
    public function checkOldPass()
    {
        $id = $this->session->get('adminId');
        $password = $this->request->getvar('oldpass');
        $data = $this->Login_Model->where('id', $id)->findAll();
        if ($data) {
            $pass = $this->encrypter->decrypt(base64_decode($data[0]['password']));
            if ($password == $pass) {
                return $this->response->setJSON(TRUE);
            } else {
                return $this->response->setJSON(FALSE);
            }
        } else {
            return $this->response->setJSON(FALSE);
        }
    }
    public function edit_profile()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            $id = $this->session->get('adminId');
            $this->data['admin_info'] = $this->Login_Model->where('id', $id)->get()->getRow();
            $this->data['password'] = $this->encrypter->decrypt(base64_decode($this->data['admin_info']->password));
            return view('editProfile', $this->data);
        }
    }
    public function update_profile()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            if (is_modification_allowed()) {
                $this->session->setFlashdata('error', DEMO_VERSION_MSG);
            } else {
                $username = $this->request->getVar('username');
                $email = $this->request->getVar('email');
                $new_password = $this->request->getVar('newpassword');
                $confirm_password = $this->request->getVar('confirmpassword');
                $id = $this->session->get('adminId');
                if (!empty($new_password) && !empty($confirm_password)) {
                    if($new_password == $confirm_password){
                    $pass = base64_encode($this->encrypter->encrypt($confirm_password));
                    $data = [
                        'username' => $username,
                        'email' => $email,
                        'password' => $pass,
                    ];
                    $this->Login_Model->update($id, $data);
                    $this->session->setFlashdata('success', 'Password Change Successfully..');
                    }else{
                        $this->session->setFlashdata('error', 'New and Confirm Password not Match..');
                    }
                } else {
                    $data = [
                        'username' => $username,
                        'email' => $email,
                    ];
                    $this->Login_Model->update($id, $data);
                    $this->session->setFlashdata('success', 'Profile Updated Successfully..');
                } 
            }
            return redirect('edit_profile');
        }
    }
    public function logout()
    {
        $this->session->destroy();
        return redirect('/');
    }

    public function check_email()
    {
        $email = $this->request->getVar('email');
        $is_exist_email = $this->Login_Model->where('email', $email)->get()->getRow();
        if($is_exist_email){
            $id = $is_exist_email->id; 
            helper('text');
            $forgot_unique_code = random_string('alnum', 25);
            $data = [
                'forgot_unique_code' => $forgot_unique_code,
                'forgot_at' => $this->toDateTime,
            ];
            $this->Login_Model->update($id, $data);
            
            $full_url = APP_URL .'reset_password?forgot_code='.$forgot_unique_code;
            $to = $email;
            $subject = 'Forgot Password';
            $message = "Dear $is_exist_email->username, <br/> Your password reset link is $full_url. Click it to proceed further.<br/>Thank You!!";
            
            $this->email->setTo($to);
            $this->email->setFrom(is_email_setting()->SMTPUser, 'News Admin Panel');
            
            $this->email->setSubject($subject);
            $this->email->setMessage($message);
            
            if ($this->email->send()) {
                return $this->response->setJSON(TRUE);
            } 
        }
        else{
            return $this->response->setJSON(FALSE);
        } 
        //return redirect('/');
        
    }
    public function reset_password()
    {
        return view('reset_password', $this->data);
    }
    public function update_password()
    {
        $password = $this->request->getVar('password');
        $confirm_password = $this->request->getVar('confirm_password');
        $forgot_code = $this->request->getVar('forgot_unique_code');
        $is_exist_forgot_unique_code = $this->Login_Model->where('forgot_unique_code', $forgot_code)->get()->getRow();
        if($is_exist_forgot_unique_code){
            $forgot_unique_code = $is_exist_forgot_unique_code->forgot_unique_code;
            $forgot_at = $is_exist_forgot_unique_code->forgot_at;
            $id = $is_exist_forgot_unique_code->id;
            $username = $is_exist_forgot_unique_code->username;
            $email = $is_exist_forgot_unique_code->email;
            if(strtotime($forgot_at) < strtotime('24 hours') && $forgot_code == $forgot_unique_code) {
                if (!empty($password) && !empty($confirm_password && $password == $confirm_password)) {
                    $pass = base64_encode($this->encrypter->encrypt($confirm_password));
                    $data = [
                        'password' => $pass,
                        'forgot_unique_code' => '',
                    ];
                    $this->Login_Model->update($id, $data);
                    $to = $email;
                    $subject = 'Forgot Password';
                    $message = "Dear $username, <br/> Your password reset successfully. New password is <strong>$password</strong>.<br/>Thank You!!";
                  
                    
                    $this->email->setTo($to);
                    $this->email->setFrom(is_email_setting()->SMTPUser, 'News Admin Panel');
                    
                    $this->email->setSubject($subject);
                    $this->email->setMessage($message);
                    if ($this->email->send()) {
                        $this->session->setFlashdata('success', 'Email sent successfully');
                    } 
                    $data = [
                        'error' => false,
                        'message' => 'Password Change Successfully..',
                    ];
                    return $this->response->setJSON($data);
                }else{
                    $data = [
                        'error' => true,
                        'message' => 'New and Confirm Password not Match..',
                    ];
                    return $this->response->setJSON($data);
                }
            }else{
                $data = [
                    'error' => true,
                    'message' => 'Reset Password link is expired, please try again.',
                ];
                return $this->response->setJSON($data);
            }
              
        }else{
            $data = [
                'error' => true,
                'message' => 'Link is invalid',
            ];
            return $this->response->setJSON($data);
        }
    }
}