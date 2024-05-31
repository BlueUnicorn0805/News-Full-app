<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use ZipArchive;

class Settings extends Controller {

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        $this->session = \Config\Services::session();
        $this->session->start();

        $this->helpers = helper('SystemSettings');
        date_default_timezone_set(get_system_timezone());
        $this->toDate = date('Y-m-d');

        $this->db = \Config\Database::connect();
        $this->data['app_name'] = $this->db->table('tbl_settings')->where('type', 'app_name')->get()->getResult();
        $this->data['app_logo'] = $this->db->table('tbl_settings')->where('type', 'app_logo')->get()->getResult();
        
    }

    public function index() {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            $this->data['app_version'] = $this->db->table('tbl_settings')->where('type', 'app_version')->get()->getResult();
            return view('system_updates', $this->data);
        }
    }

    public function store_system_update() {
        if (is_modification_allowed()) {
            $this->session->setFlashdata('error', DEMO_VERSION_MSG);
        } else {
            $validated = $this->validate([
                'file' => [
                    'uploaded[file]',
                    'mime_in[file,application/zip,application/x-7z-compressed]',
                ],
            ]);
            if ($validated) {
                $tmp_path = 'public/tmp';
                if (!is_dir($tmp_path)) {
                    mkdir($tmp_path, 0777, TRUE);
                }

                $target_path = getcwd() . DIRECTORY_SEPARATOR;
                $target_path1 = $tmp_path;

                $file = $this->request->getFile('file');
                $fileName = $file->getName();

                if ($file->isValid() && !$file->hasMoved()) {
                    $file->move($target_path1);

                    $zip = new ZipArchive();
                    $filePath = $target_path1 . '/' . $fileName;
                    $zipFile = $zip->open($filePath);

                    if ($zipFile === true) {
                        $zip->extractTo($target_path1);
                        $zip->close();
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                        $ver_file1 = $target_path1 . '/version_info.php';
                        $source_path1 = $target_path1 . '/source_code.zip';
                        $sql_file1 = $target_path1 . '/database.sql';
                        if (file_exists($ver_file1) && file_exists($source_path1) && file_exists($sql_file1)) {
                            $ver_file = $target_path . '/version_info.php';
                            $source_path = $target_path . '/source_code.zip';
                            $sql_file = $target_path . '/database.sql';
                            if (rename($ver_file1, $ver_file) && rename($source_path1, $source_path) && rename($sql_file1, $sql_file)) {
                                $this->DeleteDir($target_path1);
                                $version_file = require_once ($ver_file);

                                $res = $this->db->table('tbl_settings')->where('type', 'app_version')->get()->getResult();
                                $current_version = (!empty($res)) ? $res[0]->message : '';
                                if ($current_version == $version_file['current_version']) {
                                    $zip1 = new ZipArchive();
                                    $zipFile1 = $zip1->open($source_path);
                                    if ($zipFile1 === true) {
                                        $zip1->extractTo($target_path);
                                        $zip1->close();
                                        if (file_exists($sql_file)) {
                                            $lines = file($sql_file);
                                            for ($i = 0; $i < count($lines); $i++) {
                                                if (!empty($lines[$i])) {
                                                    $this->db->query($lines[$i]);
                                                }
                                            }
                                        }

                                        unlink($source_path);
                                        unlink($ver_file);
                                        unlink($sql_file);
                                        $frm_data = ['message' => $version_file['update_version']];
                                        $this->db->table('tbl_settings')->where('type', 'app_version')->set($frm_data)->update();

                                        $this->session->setFlashdata('success', 'System update successfully, now your version is ' . $version_file['update_version']);
                                    } else {
                                        unlink($source_path);
                                        unlink($ver_file);
                                        unlink($sql_file);
                                        $this->session->setFlashdata('error', 'Something wrong, please try again.!');
                                    }
                                } else if ($current_version == $version_file['update_version']) {
                                    unlink($source_path);
                                    unlink($ver_file);
                                    unlink($sql_file);
                                    $this->session->setFlashdata('error', 'System is already updated.!');
                                } else {
                                    unlink($source_path);
                                    unlink($ver_file);
                                    unlink($sql_file);
                                    $this->session->setFlashdata('error', 'Your version is ' . $current_version . '. Please update nearest version first');
                                }
                            } else {
                                $this->DeleteDir($target_path1);
                                $this->session->setFlashdata('error', 'Invalid file, please try again.!');
                            }
                        } else {
                            $this->DeleteDir($target_path1);
                            $this->session->setFlashdata('error', 'Invalid file, please try again.!');
                        }
                    } else {
                        $this->DeleteDir($target_path1);
                        $this->session->setFlashdata('error', 'Invalid file, please try again.!');
                    }
                } else {
                    $this->session->setFlashdata('error', 'Only zip allow, please try again.!');
                }
            } else {
                $this->session->setFlashdata('error', 'Invalid file, please try again.!');
            }
        }
        return redirect('system_updates');
    }

    public function DeleteDir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") {
                        $dir_sec = $dir . "/" . $object;
                        if (is_dir($dir_sec)) {
                            rmdir($dir_sec);
                        }
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }

}

?>