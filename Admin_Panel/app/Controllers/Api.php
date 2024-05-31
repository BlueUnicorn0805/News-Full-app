<?php
namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class Api extends ResourceController {
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        $this->db = \Config\Database::connect();
        $this->access_key = "5670";
        $res = $this->db->table('tbl_settings')->where('type', 'jwt_key')->get()->getResult();
        $this->JWT_KEY = (!empty($res)) ? $res[0]->message : "";
        $this->helpers = helper('SystemSettings');
        date_default_timezone_set(get_system_timezone());
        $this->toDate = date('Y-m-d');
        $this->toDateTime = date('Y-m-d H:i:s');
    }
    public function delete_user(){
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('user_id') ) {
                $user_id = $this->request->getVar('user_id');
                $this->db->table('tbl_bookmark')->where('user_id', $user_id)->delete();
                $this->db->table('tbl_comment')->where('user_id', $user_id)->delete();
                $this->db->table('tbl_comment_flag')->where('user_id', $user_id)->delete();
                $this->db->table('tbl_comment_like')->where('user_id', $user_id)->delete();
                $this->db->table('tbl_news_like')->where('user_id', $user_id)->delete();
                $this->db->table('tbl_survey_result')->where('user_id', $user_id)->delete();
                $this->db->table('tbl_users_category')->where('user_id', $user_id)->delete();
                $this->db->table('tbl_users')->where('id', $user_id)->delete();
                $response['error'] = "false";
                $response['message'] = "user delete successfully";
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function get_user_notification() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('user_id') ) {
                $user_id = $this->request->getVar('user_id');
                $offset = ($this->request->getVar('offset')) ? $this->request->getVar('offset') : 0;
                $limit = ($this->request->getVar('limit')) ? $this->request->getVar('limit') : 10;
                $res = $this->db->table('tbl_comment_notification');
                $res->where('user_id', $user_id);
                $res->limit($limit, $offset)->orderBy('id', 'DESC');
                $data = $res->get()->getResult();
                if ($data) {
                    $res = $this->db->table('tbl_comment_notification');
                    $res->where('user_id', $user_id);
                    $data1 = $res->get()->getResult();
                    $total = count($data1);
                    $response['error'] = "false";
                    $response['total'] = "$total";
                    $response['data'] = $data;
                } else {
                    $response['error'] = "true";
                    $response['message'] = "No Data Found";
                }
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function delete_user_notification() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('id')) {
                $comment_notification_id = $this->request->getVar('id');
                $this->db->table('tbl_comment_notification')->whereIn('id', explode(",", $comment_notification_id))->delete();
                $response['error'] = "false";
                $response['message'] = "Notification deleted!";
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function get_news_by_tag() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('tag_id') && $this->request->getVar('user_id') != '' && $this->request->getVar('language_id')) {
                $tag_id = $this->request->getVar('tag_id');
                $user_id = $this->request->getVar('user_id');
                $language_id = $this->request->getVar('language_id');
                $offset = ($this->request->getVar('offset')) ? $this->request->getVar('offset') : 0;
                $limit = ($this->request->getVar('limit')) ? $this->request->getVar('limit') : 10;
                $join = ' LEFT JOIN tbl_category ON tbl_category.id = tbl_news.category_id';
                $join .= ' LEFT JOIN tbl_subcategory ON tbl_subcategory.id = tbl_news.subcategory_id';
                $where = ' WHERE tbl_news.tag_id IN (' . $tag_id . ')';
                $where .= 'AND tbl_news.language_id = ' . $language_id;
                $where .= " AND (tbl_news.show_till >= '" . $this->toDate . "' OR CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00')";
                $where .= 'AND tbl_news.status = 1';
                $where .= " AND tbl_news.description != ''";
                $query = $this->db->query('SELECT tbl_news.*, IFNULL(tbl_category.category_name, "") as category_name, IFNULL(tbl_subcategory.subcategory_name, "") as subcategory_name FROM tbl_news' . $join . ' ' . $where . ' ORDER BY id DESC LIMIT ' . $offset . ',' . $limit . '');
                $data = $query->getResult();
                if ($data) {
                    $query1 = $this->db->query('SELECT tbl_news.*, IFNULL(tbl_category.category_name, "") as category_name, IFNULL(tbl_subcategory.subcategory_name, "") as subcategory_name FROM tbl_news ' . $join . ' ' . $where . ' ORDER BY id DESC');
                    $data1 = $query1->getResult();
                    $total = count($data1);
                    for ($i = 0; $i < count($data); $i++) {
                        $data[$i]->image = ($data[$i]->image) ? base_url() . '/public/images/news/' . $data[$i]->image : '';
                        if ($data[$i]->content_type == "video_upload") {
                            $data[$i]->content_value = ($data[$i]->content_value) ? base_url() . '/public/images/news_video/' . $data[$i]->content_value : '';
                        }
                        $img = array();
                        $img = $this->db->table('tbl_news_image')->select('other_image')->where('news_id', $data[$i]->id)->get()->getResult();
                        for ($j = 0; $j < count($img); $j++) {
                            $img[$j]->other_image = ($img[$j]->other_image) ? base_url() . '/public/images/news/' . $data[$i]->id . '/' . $img[$j]->other_image : '';
                        }
                        $data[$i]->image_data = $img;
                        $like = $this->db->table('tbl_news_like')->select('COUNT(id) as total')->where('news_id', $data[$i]->id)->where('status', '1')->get()->getResult();
                        $data[$i]->total_like = (!empty($like)) ? $like[0]->total : "0";
                        $dislike = $this->db->table('tbl_news_like')->select('COUNT(id) as total')->where('news_id', $data[$i]->id)->where('status', '2')->get()->getResult();
                        $data[$i]->total_dislike = (!empty($dislike)) ? $dislike[0]->total : "0";
                        $ulike = $this->db->table('tbl_news_like')->where('news_id', $data[$i]->id)->where('user_id', $user_id)->where('status', '1')->get()->getResult();
                        $data[$i]->like = (!empty($ulike)) ? '1' : '0';
                        $udislike = $this->db->table('tbl_news_like')->where('news_id', $data[$i]->id)->where('user_id', $user_id)->where('status', '2')->get()->getResult();
                        $data[$i]->dislike = (!empty($udislike)) ? '1' : '0';
                        $views = $this->db->table('	tbl_news_view')->select('COUNT(id) as total')->where('news_id', $data[$i]->id)->get()->getResult();
                        $data[$i]->total_views = (!empty($views)) ? $views[0]->total : "0";
                        $bookmark = $this->db->table('tbl_bookmark')->select('COUNT(id) as total')->where('news_id', $data[$i]->id)->get()->getResult();
                        $data[$i]->total_bookmark = (!empty($bookmark)) ? $bookmark[0]->total : "0";
                        $ubookmark = $this->db->table('tbl_bookmark')->where('news_id', $data[$i]->id)->where('user_id', $user_id)->get()->getResult();
                        $data[$i]->bookmark = (!empty($ubookmark)) ? '1' : '0';
                        $query2 = $this->db->query('SELECT GROUP_CONCAT(tag_name) as tag_name, GROUP_CONCAT(id) as tag_id FROM tbl_tag WHERE id IN(' . $data[$i]->tag_id . ')');
                        $res2 = $query2->getResult();
                        $data[$i]->tag_name = (!empty($res2)) ? $res2[0]->tag_name : '';
                        $data[$i]->tag_id = (!empty($res2)) ? $res2[0]->tag_id : $data[$i]->tag_id;
                    }
                    $response['error'] = "false";
                    $response['total'] = "$total";
                    $response['data'] = $data;
                } else {
                    $response['error'] = "true";
                    $response['message'] = "No Data Found";
                }
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function get_subcategory_by_category() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('category_id') && $this->request->getVar('language_id')) {
                $category_id = $this->request->getVar('category_id');
                $language_id = $this->request->getVar('language_id');
                $res = $this->db->table('tbl_subcategory');
                $res->select('tbl_subcategory.*, tbl_category.category_name');
                $res->join('tbl_category', 'tbl_category.id = tbl_subcategory.category_id');
                $res->where('tbl_subcategory.category_id', $category_id);
                $res->where('tbl_subcategory.language_id', $language_id);
                $data = $res->get()->getResult();
                if ($data) {
                    for ($i = 0; $i < count($data); $i++) {
                        $data[$i]->image = ($data[$i]->image) ? base_url() . '/public/images/subcategory/' . $data[$i]->image : '';
                    }
                    $response['error'] = "false";
                    $response['data'] = $data;
                } else {
                    $response['error'] = "true";
                    $response['message'] = "No Data Found";
                }
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function set_comment_like_dislike() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('user_id') && $this->request->getVar('comment_id') && $this->request->getVar('status') != '') {
                $user_id = $this->request->getVar('user_id');
                 $language_id = $this->request->getVar('language_id');
                $comment_id = $this->request->getVar('comment_id');
                $status = $this->request->getVar('status');
                if ($status != '0') {
                   
                    $res = $this->db->table('tbl_comment_like')->where('comment_id', $comment_id)->where('user_id', $user_id)->get()->getResult();
                    
                    if (!empty($res)) {
                        $data = array(
                            'status' => $status
                        );
                        $this->db->table('tbl_comment_like')->where('comment_id', $comment_id)->where('user_id', $user_id)->update($data);
                        $insert_id = $res[0]->id;
                    } else {
                        $data = [
                            'user_id' => $user_id,
                            'comment_id' => $comment_id,
                            'status' => $status
                        ];
                        $this->db->table('tbl_comment_like')->insert($data);
                        $insert_id = $this->db->insertID();
                        
                    }
                    if ($status == '1') {
                       
                        $res_comment = $this->db->table('tbl_comment_like')->where('id', $insert_id)->get()->getResult();
                       
                        if (!empty($res_comment)) {
                            $comment_id = $res_comment[0]->comment_id;
                            $res_comment1 = $this->db->table('tbl_comment')->where('id', $comment_id)->get()->getResult();
                            if ($comment_id) {
                                $old_user_id = $res_comment1[0]->user_id;
                                $res1 = $this->db->table('tbl_users')->where('id', $old_user_id)->get()->getResult();
                                if (!empty($res1)) {
                                    $get_name = $this->db->table('tbl_users')->where('id', $user_id)->get()->getResult();
                                    $fcmMsg = array(
                                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                                        'type' => 'comment_like',
                                        'language_id' => $language_id,
                                        'message' => 'Like in your comment in ' . $res_comment1[0]->message . ' by ' . $get_name[0]->name,
                                    );
                                    $fcm_id = ($res1[0]->fcm_id) ? $res1[0]->fcm_id : "";
                                    $fcmFields = array(
                                        'to' => $fcm_id,
                                        'priority' => 'high',
                                        'notification' => $fcmMsg,
                                        'data' => $fcmMsg
                                    );
                                    $res2 = $this->db->table('tbl_settings')->where('type', 'fcm_sever_key')->get()->getResult();
                                    $API_ACCESS_KEY = (!empty($res2)) ? $res2[0]->message : '';
                                    $headers = array(
                                        'Authorization: key=' . $API_ACCESS_KEY,
                                        'Content-Type: application/json'
                                    );
                                    $ch = curl_init();
                                    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
                                    curl_setopt($ch, CURLOPT_POST, true);
                                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmFields));
                                    $result = curl_exec($ch);
                                    if ($result === FALSE) {
                                        die('Curl failed: ' . curl_error($ch));
                                    }
                                    //Now close the connection
                                    curl_close($ch);
                                    $like_data = [
                                        'master_id' => $insert_id,
                                        'user_id' => $old_user_id,
                                        'sender_id' => $user_id,
                                        'type' => 'comment_like',
                                        'message' => 'Like in your comment in ' . $res_comment1[0]->message . ' by ' . $get_name[0]->name,
                                        'date' => $this->toDateTime
                                    ];
                                    $this->db->table('tbl_comment_notification')->insert($like_data);
                                }
                            }
                        }
                    }
                } else {
                    $this->db->table('tbl_comment_like')->where('comment_id', $comment_id)->where('user_id', $user_id)->delete();
                   
                }
                $response['error'] = "false";
                $response['message'] = "updated successfully!";
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function get_live_streaming() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            $language_id = $this->request->getVar('language_id');
            $data = $this->db->table('tbl_live_streaming')->where('language_id',$language_id)->orderBy('id', 'DESC')->get()->getResult();
            foreach ($data as $value) {
                if($value->image!='')
                {
                    $value->image=base_url() . '/public/images/liveStreaming/'.$value->image;
                }
            }
            if ($data) {
                $response['error'] = "false";
                $response['data'] = $data;
            } else {
                $response['error'] = "true";
                $response['message'] = "No Data Found";
            }
        }
        return $this->respond($response);
    }
    public function set_flag() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('comment_id') && $this->request->getVar('user_id') && $this->request->getVar('news_id') && $this->request->getVar('message')) {
                $user_id = $this->request->getVar('user_id');
                $news_id = $this->request->getVar('news_id');
                $comment_id = $this->request->getVar('comment_id');
                $message = $this->request->getVar('message');
                $status = '1';
                $data = [
                    'comment_id' => $comment_id,
                    'user_id' => $user_id,
                    'news_id' => $news_id,
                    'message' => $message,
                    'status' => $status,
                    'date' => $this->toDate
                ];
                $this->db->table('tbl_comment_flag')->insert($data);
                $response['error'] = "false";
                $response['message'] = "flag successfully";
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function set_like_dislike() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('user_id') && $this->request->getVar('news_id') && $this->request->getVar('status') != '') {
                $user_id = $this->request->getVar('user_id');
                $news_id = $this->request->getVar('news_id');
                $status = $this->request->getVar('status');
                if ($status != '0') {
                    $res = $this->db->table('tbl_news_like')->where('news_id', $news_id)->where('user_id', $user_id)->get()->getResult();
                    if (!empty($res)) {
                        $data = array(
                            'status' => $status
                        );
                        $this->db->table('tbl_news_like')->where('news_id', $news_id)->where('user_id', $user_id)->update($data);
                    } else {
                        $data = [
                            'user_id' => $user_id,
                            'news_id' => $news_id,
                            'status' => $status
                        ];
                        $this->db->table('tbl_news_like')->insert($data);
                    }
                } else {
                    $this->db->table('tbl_news_like')->where('news_id', $news_id)->where('user_id', $user_id)->delete();
                }
                $response['error'] = "false";
                $response['message'] = "updated successfully!";
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function get_news_by_user_category() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('user_id') && $this->request->getVar('category_id') && $this->request->getVar('language_id')) {
                $category_id = $this->request->getVar('category_id');
                $user_id = $this->request->getVar('user_id');
                $offset = ($this->request->getVar('offset')) ? $this->request->getVar('offset') : 0;
                $limit = ($this->request->getVar('limit')) ? $this->request->getVar('limit') : 10;
                $language_id = $this->request->getVar('language_id');
                $data = $this->db->query("SELECT n.*,c.category_name FROM tbl_news n JOIN tbl_category c ON n.category_id = c.id WHERE n.language_id = $language_id AND n.status = 1 AND n.description !='' AND n.category_id IN ($category_id) AND (n.show_till >=  '$this->toDate' OR CAST(n.show_till AS CHAR(20)) = '0000-00-00') ORDER BY rand() LIMIT $offset, $limit")->getResult();
                if ($data) {
                    $data1 = $this->db->query("SELECT n.*,c.category_name FROM tbl_news n JOIN tbl_category c ON n.category_id = c.id WHERE n.language_id = $language_id AND n.status = 1 AND n.description !='' AND n.category_id IN ($category_id) AND (n.show_till >=  '$this->toDate' OR CAST(n.show_till AS CHAR(20)) = '0000-00-00')")->getResult();
                    $total = count($data1);
                    for ($i = 0; $i < count($data); $i++) {
                        $data[$i]->image = ($data[$i]->image) ? base_url() . '/public/images/news/' . $data[$i]->image : '';
                        if ($data[$i]->content_type == "video_upload") {
                            $data[$i]->content_value = ($data[$i]->content_value) ? base_url() . '/public/images/news_video/' . $data[$i]->content_value : '';
                        }
                        $img = array();
                        $img = $this->db->table('tbl_news_image')->select('other_image')->where('news_id', $data[$i]->id)->get()->getResult();
                        for ($j = 0; $j < count($img); $j++) {
                            $img[$j]->other_image = ($img[$j]->other_image) ? base_url() . '/public/images/news/' . $data[$i]->id . '/' . $img[$j]->other_image : '';
                        }
                        $data[$i]->image_data = $img;
                        $like = $this->db->table('tbl_news_like')->select('COUNT(id) as total')->where('news_id', $data[$i]->id)->where('status', '1')->get()->getResult();
                        $data[$i]->total_like = (!empty($like)) ? $like[0]->total : "0";
                        $dislike = $this->db->table('tbl_news_like')->select('COUNT(id) as total')->where('news_id', $data[$i]->id)->where('status', '2')->get()->getResult();
                        $data[$i]->total_dislike = (!empty($dislike)) ? $dislike[0]->total : "0";
                        $ulike = $this->db->table('tbl_news_like')->where('news_id', $data[$i]->id)->where('user_id', $user_id)->where('status', '1')->get()->getResult();
                        $data[$i]->like = (!empty($ulike)) ? '1' : '0';
                        $udislike = $this->db->table('tbl_news_like')->where('news_id', $data[$i]->id)->where('user_id', $user_id)->where('status', '2')->get()->getResult();
                        $data[$i]->dislike = (!empty($udislike)) ? '1' : '0';
                        $views = $this->db->table('	tbl_news_view')->select('COUNT(id) as total')->where('news_id', $data[$i]->id)->get()->getResult();
                        $data[$i]->total_views = (!empty($views)) ? $views[0]->total : "0";
                        $bookmark = $this->db->table('tbl_bookmark')->select('COUNT(id) as total')->where('news_id', $data[$i]->id)->get()->getResult();
                        $data[$i]->total_bookmark = (!empty($bookmark)) ? $bookmark[0]->total : "0";
                        $ubookmark = $this->db->table('tbl_bookmark')->where('news_id', $data[$i]->id)->where('user_id', $user_id)->get()->getResult();
                        $data[$i]->bookmark = (!empty($ubookmark)) ? '1' : '0';
                        if(isset($data[$i]->tag_id) && !empty($data[$i]->tag_id)){
                            $query2 = $this->db->query('SELECT GROUP_CONCAT(tag_name) as tag_name, GROUP_CONCAT(id) as tag_id FROM tbl_tag WHERE id IN(' . $data[$i]->tag_id . ')');
                            $res2 = $query2->getResult();
                            $data[$i]->tag_name = (!empty($res2)) ? $res2[0]->tag_name : '';
                            $data[$i]->tag_id = (!empty($res2)) ? $res2[0]->tag_id : $data[$i]->tag_id;
                        }
                    }
                    $response['error'] = "false";
                    $response['total'] = "$total";
                    $response['data'] = $data;
                } else {
                    $response['error'] = "true";
                    $response['message'] = "No Data Found";
                }
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function set_user_category() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('user_id') && $this->request->getVar('category_id') != '') {
                $user_id = $this->request->getVar('user_id');
                $category_id = $this->request->getVar('category_id');
                if ($category_id == '0') {
                    $this->db->table('tbl_users_category')->where('user_id', $user_id)->delete();
                } else {
                    $res = $this->db->table('tbl_users_category')->where('user_id', $user_id)->get()->getResult();
                    if (!empty($res)) {
                        $data = ['category_id' => $category_id];
                        $this->db->table('tbl_users_category')->where('user_id', $user_id)->update($data);
                    } else {
                        $data = [
                            'user_id' => $user_id,
                            'category_id' => $category_id,
                        ];
                        $this->db->table('tbl_users_category')->insert($data);
                    }
                }
                $response['error'] = "false";
                $response['message'] = "updated successfully";
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function get_notification() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            
            $offset = ($this->request->getVar('offset')) ? $this->request->getVar('offset') : 0;
            $limit = ($this->request->getVar('limit')) ? $this->request->getVar('limit') : 10;
            $language_id = $this->request->getVar('language_id')?? 14;
            $res = $this->db->table('tbl_notifications');
            $res->select('tbl_notifications.*,tbl_category.category_name, tbl_news.title as new_title');
            
            $res->join('tbl_category', 'tbl_category.id = tbl_notifications.category_id', 'left');
            $res->join('tbl_news', 'tbl_news.id = tbl_notifications.news_id', 'left');
            $res->where('tbl_notifications.language_id', $language_id);
            $res->limit($limit, $offset)->orderBy('tbl_notifications.id', 'DESC');
            $data = $res->get()->getResult();
            if ($data) {
                $res = $this->db->table('tbl_notifications');
                $res->select('tbl_notifications.*,tbl_category.category_name');
               
                $res->join('tbl_category', 'tbl_category.id = tbl_notifications.category_id', 'left');
                $res->join('tbl_news', 'tbl_news.id = tbl_notifications.news_id', 'left');
                $res->where('tbl_notifications.language_id', $language_id);
                $data1 = $res->get()->getResult();
                $total = count($data1);
                for ($i = 0; $i < count($data); $i++) {
                    $data[$i]->image = ($data[$i]->image) ? base_url() . '/public/images/notification/' . $data[$i]->image : '';
                    $data[$i]->category_name = ($data[$i]->category_name != NULL) ? $data[$i]->category_name : '';
                    $data[$i]->new_title = ($data[$i]->new_title != NULL) ? $data[$i]->new_title : '';
                }
                $response['error'] = "false";
                $response['total'] = "$total";
                $response['data'] = $data;
            } else {
                $response['error'] = "true";
                $response['message'] = "No Data Found";
            }
        } 
        
        return $this->respond($response);
    }
    public function get_bookmark() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('user_id') && $this->request->getVar('language_id')) {
                $today =$this->toDate;
                $user_id = $this->request->getVar('user_id');
                $language_id = $this->request->getVar('language_id');
                $offset = ($this->request->getVar('offset')) ? $this->request->getVar('offset') : 0;
                $limit = ($this->request->getVar('limit')) ? $this->request->getVar('limit') : 10;
                $res = $this->db->table('tbl_bookmark b');
                $res->select('b.*,n.category_id,c.category_name,,n.subcategory_id,n.language_id,n.title,n.date,n.tag_id,n.content_type,n.content_value,n.image,n.description');
                $res->join('tbl_news n', 'n.id = b.news_id');
                $res->join('tbl_category c', 'c.id = n.category_id');
                $res->where('(n.show_till >= "'.$today.'" OR CAST(n.show_till AS CHAR(20)) = 0000-00-00)');
                $res->where('n.status', '1');
                $res->where('b.user_id', $user_id)->where('n.language_id', $language_id)->limit($limit, $offset)->orderBy('id', 'DESC');
                $data = $res->get()->getResult();
                if ($data) {
                    $res = $this->db->table('tbl_bookmark b');
                    $res->select('b.*,n.category_id,c.category_name,n.title,n.date,n.content_type,n.content_value,n.image,n.description');
                    $res->join('tbl_news n', 'n.id = b.news_id');
                    $res->join('tbl_category c', 'c.id = n.category_id');
                    $res->where('(n.show_till >= "'.$today.'" OR CAST(n.show_till AS CHAR(20)) = 0000-00-00)');
                $res->where('n.status', '1');
                    $res->where('b.user_id', $user_id)->where('n.language_id', $language_id);
                    $data1 = $res->get()->getResult();
                    $total = count($data1);
                    for ($i = 0; $i < count($data); $i++) {
                        $data[$i]->image = ($data[$i]->image) ? base_url() . '/public/images/news/' . $data[$i]->image : '';
                        if ($data[$i]->content_type == "video_upload") {
                            $data[$i]->content_value = ($data[$i]->content_value) ? base_url() . '/public/images/news_video/' . $data[$i]->content_value : '';
                        }
                        $img = array();
                        $img = $this->db->table('tbl_news_image')->select('other_image')->where('news_id', $data[$i]->news_id)->get()->getResult();
                        for ($j = 0; $j < count($img); $j++) {
                            $img[$j]->other_image = ($img[$j]->other_image) ? base_url() . '/public/images/news/' . $data[$i]->news_id . '/' . $img[$j]->other_image : '';
                        }
                        $data[$i]->image_data = $img;
                        $like = $this->db->table('tbl_news_like')->select('COUNT(id) as total')->where('news_id', $data[$i]->news_id)->where('status', '1')->get()->getResult();
                        $data[$i]->total_like = (!empty($like)) ? $like[0]->total : "0";
                        $dislike = $this->db->table('tbl_news_like')->select('COUNT(id) as total')->where('news_id', $data[$i]->news_id)->where('status', '2')->get()->getResult();
                        $data[$i]->total_dislike = (!empty($dislike)) ? $dislike[0]->total : "0";
                        $ulike = $this->db->table('tbl_news_like')->where('news_id', $data[$i]->news_id)->where('user_id', $user_id)->where('status', '1')->get()->getResult();
                        $data[$i]->like = (!empty($ulike)) ? '1' : '0';
                        $udislike = $this->db->table('tbl_news_like')->where('news_id', $data[$i]->news_id)->where('user_id', $user_id)->where('status', '2')->get()->getResult();
                        $data[$i]->dislike = (!empty($udislike)) ? '1' : '0';
                        $views = $this->db->table('	tbl_news_view')->select('COUNT(id) as total')->where('news_id', $data[$i]->news_id)->get()->getResult();
                        $data[$i]->total_views = (!empty($views)) ? $views[0]->total : "0";
                        if(isset($data[$i]->tag_id) && !empty($data[$i]->tag_id)){
                            $query2 = $this->db->query('SELECT GROUP_CONCAT(distinct(tag_name)) as tag_name FROM tbl_tag WHERE id IN(' . $data[$i]->tag_id . ')');
                            $res2 = $query2->getResult();
                            $data[$i]->tag_name = (!empty($res2)) ? $res2[0]->tag_name : '';
                        }
                    }
                    $response['error'] = "false";
                    $response['total'] = "$total";
                    $response['data'] = $data;
                } else {
                    $response['error'] = "true";
                    $response['message'] = "No Data Found";
                }
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function set_bookmark() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('user_id') && $this->request->getVar('news_id') && $this->request->getVar('status') != '') {
                $user_id = $this->request->getVar('user_id');
                $news_id = $this->request->getVar('news_id');
                $status = $this->request->getVar('status');
                if ($status == '1') {
                    $data = $this->db->table('tbl_bookmark')->where('user_id', $user_id)->where('news_id', $news_id)->get()->getResult();
                    if (empty($data)) {
                        $data = [
                            'user_id' => $user_id,
                            'news_id' => $news_id,
                        ];
                        $this->db->table('tbl_bookmark')->insert($data);
                        $response['error'] = "false";
                        $response['message'] = "bookmark successfully";
                    } else {
                        $response['error'] = "true";
                        $response['message'] = "already bookmark";
                    }
                } else if ($status == '0') {
                    $this->db->table('tbl_bookmark')->where('user_id', $user_id)->where('news_id', $news_id)->delete();
                    $response['error'] = "false";
                    $response['message'] = "bookmark remove successfully";
                }
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function delete_comment() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('user_id') && $this->request->getVar('comment_id')) {
                $user_id = $this->request->getVar('user_id');
                $comment_id = $this->request->getVar('comment_id');
                $this->db->table('tbl_comment')->where('id', $comment_id)->where('user_id', $user_id)->delete();
                $response['error'] = "false";
                $response['message'] = "comment deleted!";
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function get_comment_by_news() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('news_id') && $this->request->getVar('user_id') != '') {
                $news_id = $this->request->getVar('news_id');
                $user_id = $this->request->getVar('user_id');
                $offset = ($this->request->getVar('offset')) ? $this->request->getVar('offset') : 0;
                $limit = ($this->request->getVar('limit')) ? $this->request->getVar('limit') : 10;
                $res = $this->db->table('tbl_comment c');
                $res->select('c.*,u.name,u.profile');
                $res->join('tbl_users u', 'u.id = c.user_id');
                $res->where('c.news_id', $news_id)->where('c.parent_id', '0')->where('c.status', '1')->limit($limit, $offset)->orderBy('id', 'DESC');
                $data = $res->get()->getResult();
                if ($data) {
                    for ($i = 0; $i < count($data); $i++) {
                        if (isset($data[$i]->profile) && filter_var($data[$i]->profile, FILTER_VALIDATE_URL) === FALSE) {
                            // Not a valid URL. Its a image only or empty
                            $data[$i]->profile = (!empty($data[$i]->profile)) ? base_url() . '/public/images/profile/' . $data[$i]->profile : '';
                        }
                        $tbl = $this->db->table('tbl_comment_like');
                        $like = $tbl->select('COUNT(id) as total')->where('comment_id', $data[$i]->id)->where('status', '1')->get()->getResult();
                        $data[$i]->total_like = (!empty($like)) ? $like[0]->total : "0";
                        $dislike = $tbl->select('COUNT(id) as total')->where('comment_id', $data[$i]->id)->where('status', '2')->get()->getResult();
                        $data[$i]->total_dislike = (!empty($dislike)) ? $dislike[0]->total : "0";
                        $ulike = $tbl->where('comment_id', $data[$i]->id)->where('user_id', $user_id)->where('status', '1')->get()->getResult();
                        $data[$i]->like = (!empty($ulike)) ? '1' : '0';
                        $udislike = $tbl->where('comment_id', $data[$i]->id)->where('user_id', $user_id)->where('status', '2')->get()->getResult();
                        $data[$i]->dislike = (!empty($udislike)) ? '1' : '0';
                        $data[$i]->replay = $data3 = array();
                        $res = $this->db->table('tbl_comment c');
                        $res->select('c.*,u.name,u.profile');
                        $res->join('tbl_users u', 'u.id = c.user_id');
                        $res->where('c.news_id', $news_id)->where('c.parent_id', $data[$i]->id)->where('c.status', '1')->orderBy('id', 'ASC');
                        $data3 = $res->get()->getResult();
                        for ($j = 0; $j < count($data3); $j++) {
                            if (isset($data3[$j]->profile) && filter_var($data3[$j]->profile, FILTER_VALIDATE_URL) === FALSE) {
                                // Not a valid URL. Its a image only or empty
                                $data3[$j]->profile = (!empty($data3[$j]->profile)) ? base_url() . '/public/images/profile/' . $data3[$j]->profile : '';
                            }
                            $like = $tbl->select('COUNT(id) as total')->where('comment_id', $data3[$j]->id)->where('status', '1')->get()->getResult();
                            $data3[$j]->total_like = (!empty($like)) ? $like[0]->total : "0";
                            $dislike = $tbl->select('COUNT(id) as total')->where('comment_id', $data3[$j]->id)->where('status', '2')->get()->getResult();
                            $data3[$j]->total_dislike = (!empty($dislike)) ? $dislike[0]->total : "0";
                            $ulike = $tbl->where('comment_id', $data3[$j]->id)->where('user_id', $user_id)->where('status', '1')->get()->getResult();
                            $data3[$j]->like = (!empty($ulike)) ? '1' : '0';
                            $udislike = $tbl->where('comment_id', $data3[$j]->id)->where('user_id', $user_id)->where('status', '2')->get()->getResult();
                            $data3[$j]->dislike = (!empty($udislike)) ? '1' : '0';
                        }
                        $data[$i]->replay = $data3;
                    }
                    $res = $this->db->table('tbl_comment c');
                    $res->select('c.*, u.name')->join('tbl_users u', 'u.id = c.user_id');
                    $res->where('c.news_id', $news_id)->where('c.status', '1');
                    $data1 = $res->get()->getResult();
                    $total = count($data1);
                    $response['error'] = "false";
                    $response['total'] = "$total";
                    $response['data'] = $data;
                } else {
                    $response['error'] = "true";
                    $response['message'] = "No Data Found";
                }
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function set_comment() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('user_id') && $this->request->getVar('parent_id') != '' && $this->request->getVar('news_id') && $this->request->getVar('message')) {
                $user_id = $this->request->getVar('user_id');
                $parent_id = $this->request->getVar('parent_id');
                $news_id = $this->request->getVar('news_id');
                $message = $this->request->getVar('message');
                $language_id = $this->request->getVar('language_id');
                $status = '1';
                $data2 = [
                    'user_id' => $user_id,
                    'parent_id' => $parent_id,
                    'news_id' => $news_id,
                    'message' => $message,
                    'status' => $status,
                    'date' => $this->toDateTime
                ];
                $this->db->table('tbl_comment')->insert($data2);
                $insert_id = $this->db->insertID();
                if ($parent_id) {
                    $res = $this->db->table('tbl_comment')->where('id', $parent_id)->get()->getResult();
                    if (!empty($res)) {
                        $old_user_id = $res[0]->user_id;
                        $res1 = $this->db->table('tbl_users')->where('id', $old_user_id)->get()->getResult();
                        if (!empty($res1)) {
                            $get_name = $this->db->table('tbl_users')->where('id', $user_id)->get()->getResult();
                            $fcmMsg = array(
                                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                                'type' => 'comment',
                                'news_id' => $news_id,
                                'language_id' => $language_id,
                                'message' => 'Reply in your comment in ' . $res[0]->message . ' by ' . $get_name[0]->name,
                                
                            );
                            $fcm_id = ($res1[0]->fcm_id) ? $res1[0]->fcm_id : "";
                            $fcmFields = array(
                                'to' => $fcm_id,
                                'priority' => 'high',
                                'notification' => $fcmMsg,
                                'data' => $fcmMsg
                            );
                            $res2 = $this->db->table('tbl_settings')->where('type', 'fcm_sever_key')->get()->getResult();
                            $API_ACCESS_KEY = (!empty($res2)) ? $res2[0]->message : '';
                            $headers = array(
                                'Authorization: key=' . $API_ACCESS_KEY,
                                'Content-Type: application/json'
                            );
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmFields));
                            $result = curl_exec($ch);
                            if ($result === FALSE) {
                                die('Curl failed: ' . curl_error($ch));
                            }
                            curl_close($ch);
                            $notification_comment = [
                                'master_id' => $insert_id,
                                'user_id' => $old_user_id,
                                'sender_id' => $user_id,
                                'type' => 'comment',
                                'message' => 'Reply in your comment in ' . $res[0]->message . ' by ' . $get_name[0]->name,
                                'date' => $this->toDateTime
                            ];
                            $this->db->table('tbl_comment_notification')->insert($notification_comment);
                        }
                    }
                }
                $offset = ($this->request->getVar('offset')) ? $this->request->getVar('offset') : 0;
                $limit = ($this->request->getVar('limit')) ? $this->request->getVar('limit') : 10;
                $res = $this->db->table('tbl_comment c');
                $res->select('c.*,u.name,u.profile');
                $res->join('tbl_users u', 'u.id = c.user_id');
                $res->where('c.news_id', $news_id)->where('c.parent_id', '0')->where('c.status', '1')->limit($limit, $offset)->orderBy('id', 'DESC');
                $data = $res->get()->getResult();
                if ($data) {
                    for ($i = 0; $i < count($data); $i++) {
                        if (isset($data[$i]->profile) && filter_var($data[$i]->profile, FILTER_VALIDATE_URL) === FALSE) {
                            // Not a valid URL. Its a image only or empty
                            $data[$i]->profile = (!empty($data[$i]->profile)) ? base_url() . '/public/images/profile/' . $data[$i]->profile : '';
                        }
                        $tbl = $this->db->table('tbl_comment_like');
                        $like = $tbl->select('COUNT(id) as total')->where('comment_id', $data[$i]->id)->where('status', '1')->get()->getResult();
                        $data[$i]->total_like = (!empty($like)) ? $like[0]->total : "0";
                        $dislike = $tbl->select('COUNT(id) as total')->where('comment_id', $data[$i]->id)->where('status', '2')->get()->getResult();
                        $data[$i]->total_dislike = (!empty($dislike)) ? $dislike[0]->total : "0";
                        $ulike = $tbl->where('comment_id', $data[$i]->id)->where('user_id', $user_id)->where('status', '1')->get()->getResult();
                        $data[$i]->like = (!empty($ulike)) ? '1' : '0';
                        $udislike = $tbl->where('comment_id', $data[$i]->id)->where('user_id', $user_id)->where('status', '2')->get()->getResult();
                        $data[$i]->dislike = (!empty($udislike)) ? '1' : '0';
                        $data[$i]->replay = $data3 = array();
                        $res = $this->db->table('tbl_comment c');
                        $res->select('c.*,u.name,u.profile');
                        $res->join('tbl_users u', 'u.id = c.user_id');
                        $res->where('c.news_id', $news_id)->where('c.parent_id', $data[$i]->id)->where('c.status', '1')->orderBy('id', 'ASC');
                        $data3 = $res->get()->getResult();
                        for ($j = 0; $j < count($data3); $j++) {
                            
                            if (isset($data[$j]->profile) && filter_var($data[$j]->profile, FILTER_VALIDATE_URL) === FALSE) {
                                // Not a valid URL. Its a image only or empty
                                $data3[$j]->profile = (!empty($data3[$j]->profile)) ? base_url() . '/public/images/profile/' . $data3[$j]->profile : '';
                            }
                            $like = $tbl->select('COUNT(id) as total')->where('comment_id', $data3[$j]->id)->where('status', '1')->get()->getResult();
                            $data3[$j]->total_like = (!empty($like)) ? $like[0]->total : "0";
                            $dislike = $tbl->select('COUNT(id) as total')->where('comment_id', $data3[$j]->id)->where('status', '2')->get()->getResult();
                            $data3[$j]->total_dislike = (!empty($dislike)) ? $dislike[0]->total : "0";
                            $ulike = $tbl->where('comment_id', $data3[$j]->id)->where('user_id', $user_id)->where('status', '1')->get()->getResult();
                            $data3[$j]->like = (!empty($ulike)) ? '1' : '0';
                            $udislike = $tbl->where('comment_id', $data3[$j]->id)->where('user_id', $user_id)->where('status', '2')->get()->getResult();
                            $data3[$j]->dislike = (!empty($udislike)) ? '1' : '0';
                        }
                        $data[$i]->replay = $data3;
                    }
                    $res = $this->db->table('tbl_comment c');
                    $res->select('c.*, u.name')->join('tbl_users u', 'u.id = c.user_id');
                    $res->where('c.news_id', $news_id)->where('c.status', '1');
                    $data1 = $res->get()->getResult();
                    $total = count($data1);
                    $response['error'] = "false";
                    $response['message'] = "Comment successfully";
                    $response['total'] = "$total";
                    $response['data'] = $data;
                } else {
                    $response['error'] = "true";
                    $response['message'] = "No Data Found";
                }
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function get_news_by_id() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            $today =$this->toDate;
            if ($this->request->getVar('news_id') && $this->request->getVar('user_id') != '' && $this->request->getVar('language_id')) {
                $news_id = $this->request->getVar('news_id');
                $user_id = $this->request->getVar('user_id');
                $res = $this->db->table('tbl_news');
                $res->select('tbl_news.*, tbl_category.category_name');
                $res->join('tbl_category', 'tbl_category.id = tbl_news.category_id');
                $res->where('tbl_news.language_id', $this->request->getVar('language_id'));
                $res->where('tbl_news.id', $news_id);
                $res->where('(tbl_news.show_till >= "'.$today.'" OR CAST(tbl_news.show_till AS CHAR(20)) = 0000-00-00)');
                $data = $res->get()->getResult();
                if ($data) {
                    for ($i = 0; $i < count($data); $i++) {
                        $data[$i]->image = ($data[$i]->image) ? base_url() . '/public/images/news/' . $data[$i]->image : '';
                        if ($data[$i]->content_type == "video_upload") {
                            $data[$i]->content_value = ($data[$i]->content_value) ? base_url() . '/public/images/news_video/' . $data[$i]->content_value : '';
                        }
                        $img = array();
                        $img = $this->db->table('tbl_news_image')->select('other_image')->where('news_id', $data[$i]->id)->get()->getResult();
                        for ($j = 0; $j < count($img); $j++) {
                            $img[$j]->other_image = ($img[$j]->other_image) ? base_url() . '/public/images/news/' . $data[$i]->id . '/' . $img[$j]->other_image : '';
                        }
                        $data[$i]->image_data = $img;
                        $like = $this->db->table('tbl_news_like')->select('COUNT(id) as total')->where('news_id', $data[$i]->id)->where('status', '1')->get()->getResult();
                        $data[$i]->total_like = (!empty($like)) ? $like[0]->total : "0";
                        $dislike = $this->db->table('tbl_news_like')->select('COUNT(id) as total')->where('news_id', $data[$i]->id)->where('status', '2')->get()->getResult();
                        $data[$i]->total_dislike = (!empty($dislike)) ? $dislike[0]->total : "0";
                        $ulike = $this->db->table('tbl_news_like')->where('news_id', $data[$i]->id)->where('user_id', $user_id)->where('status', '1')->get()->getResult();
                        $data[$i]->like = (!empty($ulike)) ? '1' : '0';
                        $udislike = $this->db->table('tbl_news_like')->where('news_id', $data[$i]->id)->where('user_id', $user_id)->where('status', '2')->get()->getResult();
                        $data[$i]->dislike = (!empty($udislike)) ? '1' : '0';
                        $views = $this->db->table('	tbl_news_view')->select('COUNT(id) as total')->where('news_id', $data[$i]->id)->get()->getResult();
                        $data[$i]->total_views = (!empty($views)) ? $views[0]->total : "0";
                        $bookmark = $this->db->table('tbl_bookmark')->select('COUNT(id) as total')->where('news_id', $data[$i]->id)->get()->getResult();
                        $data[$i]->total_bookmark = (!empty($bookmark)) ? $bookmark[0]->total : "0";
                        $ubookmark = $this->db->table('tbl_bookmark')->where('news_id', $data[$i]->id)->where('user_id', $user_id)->get()->getResult();
                        $data[$i]->bookmark = (!empty($ubookmark)) ? '1' : '0';
                        if(isset($data[$i]->tag_id) && !empty($data[$i]->tag_id)){
                            $query2 = $this->db->query('SELECT GROUP_CONCAT(tag_name) as tag_name, GROUP_CONCAT(id) as tag_id FROM tbl_tag WHERE id IN(' . $data[$i]->tag_id . ')');
                            $res2 = $query2->getResult();
                            $data[$i]->tag_name = (!empty($res2)) ? $res2[0]->tag_name : '';
                            $data[$i]->tag_id = (!empty($res2)) ? $res2[0]->tag_id : $data[$i]->tag_id;
                        }
                        // Ads in news details  - Top section
                        
                        $ad_space = $this->db->table('tbl_ad_spaces')->where('ad_space', 'news_details-top')->where('status', 1)->get()->getResult();
                        if (!empty($ad_space)) {
                            for ($a = 0; $a < count($ad_space); $a++) {
                                $ad_space[$a]->ad_image = ($ad_space[$a]->ad_image) ? base_url() . '/public/images/ad_spaces/' . $ad_space[$a]->ad_image : '';
                                $ad_space[$a]->position = 'top';
                            }
                            $data[$i]->ad_spaces_top = $ad_space;
                        }
                        // Ads in news details  - Bottom section
                        $ad_space = $this->db->table('tbl_ad_spaces')->where('ad_space', 'news_details-bottom')->where('status', 1)->get()->getResult();
                        if (!empty($ad_space)) {
                            for ($a = 0; $a < count($ad_space); $a++) {
                                $ad_space[$a]->ad_image = ($ad_space[$a]->ad_image) ? base_url() . '/public/images/ad_spaces/' . $ad_space[$a]->ad_image : '';
                                $ad_space[$a]->position = 'bottom';
                            }
                            $data[$i]->ad_spaces_bottom = $ad_space;
                        }
                    }
                    $response['error'] = "false";
                    $response['data'] = $data;
                } else {
                    $response['error'] = "true";
                    $response['message'] = "No Data Found";
                }
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function get_news_by_category() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            $today = $this->toDate;
            if (($this->request->getVar('category_id') || $this->request->getVar('subcategory_id')) && $this->request->getVar('user_id') != '' && $this->request->getVar('language_id')) {
                $category_id = ($this->request->getVar('category_id')) ? $this->request->getVar('category_id') : 0;
                $subcategory_id = ($this->request->getVar('subcategory_id')) ? $this->request->getVar('subcategory_id') : 0;
                $user_id = $this->request->getVar('user_id');
                $offset = ($this->request->getVar('offset')) ? $this->request->getVar('offset') : 0;
                $limit = ($this->request->getVar('limit')) ? $this->request->getVar('limit') : 10;
                $res = $this->db->table('tbl_news');
                $res->select('tbl_news.*,tbl_category.category_name');
                $res->where('tbl_news.language_id', $this->request->getVar('language_id'));
                $res->where('(tbl_news.show_till >= "' . $today . '" OR CAST(tbl_news.show_till AS CHAR(20)) = 0000-00-00)');
                $res->join('tbl_category', 'tbl_news.category_id = tbl_category.id');
                $res->join('tbl_subcategory', 'tbl_news.subcategory_id = tbl_subcategory.id', 'left');
                $res->where('tbl_news.status', '1');
                $res->where('tbl_news.description != ""');
              
                if ($this->request->getVar('category_id')) {
                    $res->where('tbl_news.category_id', $category_id);
                } else {
                    $res->where('tbl_news.subcategory_id', $subcategory_id);
                }
                $res->limit($limit, $offset)->orderBy('id', 'DESC');
                $data = $res->get()->getResult();
                if ($data) {
                    $res1 = $this->db->table('tbl_news');
                    $res1->where('tbl_news.language_id', $this->request->getVar('language_id'));
                    $res1->where('(tbl_news.show_till >= "' . $today . '" OR CAST(tbl_news.show_till AS CHAR(20)) = 0000-00-00)');
                    $res1->join('tbl_category', 'tbl_news.category_id = tbl_category.id');
                    $res1->join('tbl_subcategory', 'tbl_news.subcategory_id = tbl_subcategory.id', 'left');
                    $res1->where('tbl_news.status', '1');
                    $res1->where('tbl_news.description != ""');
                    if ($this->request->getVar('category_id')) {
                        $res1->where('tbl_news.category_id', $category_id);
                    } else {
                        $res1->where('tbl_news.subcategory_id', $subcategory_id);
                    }
                    $data1 = $res1->get()->getResult();
                    $total = count($data1);
                    for ($i = 0; $i < count($data); $i++) {
                        $data[$i]->image = ($data[$i]->image) ? base_url() . '/public/images/news/' . $data[$i]->image : '';
                        if ($data[$i]->content_type == "video_upload") {
                            $data[$i]->content_value = ($data[$i]->content_value) ? base_url() . '/public/images/news_video/' . $data[$i]->content_value : '';
                        }
                        $img = array();
                        $img = $this->db->table('tbl_news_image')->select('other_image')->where('news_id', $data[$i]->id)->get()->getResult();
                        for ($j = 0; $j < count($img); $j++) {
                            $img[$j]->other_image = ($img[$j]->other_image) ? base_url() . '/public/images/news/' . $data[$i]->id . '/' . $img[$j]->other_image : '';
                        }
                        $data[$i]->image_data = $img;
                        $like = $this->db->table('tbl_news_like')->select('COUNT(id) as total')->where('news_id', $data[$i]->id)->where('status', '1')->get()->getResult();
                        $data[$i]->total_like = (!empty($like)) ? $like[0]->total : "0";
                        $dislike = $this->db->table('tbl_news_like')->select('COUNT(id) as total')->where('news_id', $data[$i]->id)->where('status', '2')->get()->getResult();
                        $data[$i]->total_dislike = (!empty($dislike)) ? $dislike[0]->total : "0";
                        $ulike = $this->db->table('tbl_news_like')->where('news_id', $data[$i]->id)->where('user_id', $user_id)->where('status', '1')->get()->getResult();
                        $data[$i]->like = (!empty($ulike)) ? '1' : '0';
                        $udislike = $this->db->table('tbl_news_like')->where('news_id', $data[$i]->id)->where('user_id', $user_id)->where('status', '2')->get()->getResult();
                        $data[$i]->dislike = (!empty($udislike)) ? '1' : '0';
                        $views = $this->db->table('	tbl_news_view')->select('COUNT(id) as total')->where('news_id', $data[$i]->id)->get()->getResult();
                        $data[$i]->total_views = (!empty($views)) ? $views[0]->total : "0";
                        $bookmark = $this->db->table('tbl_bookmark')->select('COUNT(id) as total')->where('news_id', $data[$i]->id)->get()->getResult();
                        $data[$i]->total_bookmark = (!empty($bookmark)) ? $bookmark[0]->total : "0";
                        $ubookmark = $this->db->table('tbl_bookmark')->where('news_id', $data[$i]->id)->where('user_id', $user_id)->get()->getResult();
                        $data[$i]->bookmark = (!empty($ubookmark)) ? '1' : '0';
                        if(isset($data[$i]->tag_id) && !empty($data[$i]->tag_id)){
                            $query2 = $this->db->query('SELECT GROUP_CONCAT(tag_name) as tag_name, GROUP_CONCAT(id) as tag_id FROM tbl_tag WHERE id IN(' . $data[$i]->tag_id . ')');
                            $res2 = $query2->getResult();
                            $data[$i]->tag_name = (!empty($res2)) ? $res2[0]->tag_name : '';
                            $data[$i]->tag_id = (!empty($res2)) ? $res2[0]->tag_id : $data[$i]->tag_id;
                        }
                    }
                    $response['error'] = "false";
                    $response['total'] = "$total";
                    $response['data'] = $data;
                } else {
                    $response['error'] = "true";
                    $response['message'] = "No Data Found";
                }
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function get_news() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('user_id') != '') {
                $today =date('Y-m-d');
                $language_id = $this->request->getVar('language_id');
                $user_id = $this->request->getVar('user_id');
                $offset = ($this->request->getVar('offset')) ? $this->request->getVar('offset') : 0;
                $limit = ($this->request->getVar('limit')) ? $this->request->getVar('limit') : 10;
                $get_user_news = ($this->request->getVar('get_user_news')) ? $this->request->getVar('get_user_news') : 0;
                $join = ' LEFT JOIN tbl_category ON tbl_category.id = tbl_news.category_id';
                $join .= ' LEFT JOIN tbl_subcategory ON tbl_subcategory.id = tbl_news.subcategory_id';
                $where = " WHERE tbl_news.title IS  NOT NULL";
                if ($this->request->getVar('search')) {
                    $search = $this->request->getVar('search');
                    $where .= " AND (LOWER(tbl_news.title) like LOWER('%" . $search . "%'))";
                }
                if(!empty($language_id)){
                    $where .= " AND tbl_news.language_id = '" . $language_id . "'";
                }
                $where .= " AND (tbl_news.show_till >= '" . $today . "' OR CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00')";
                
                $where .= " AND tbl_news.status = '1'";
               
                if ($get_user_news) {
                    $where .= " AND tbl_news.user_id = $get_user_news";
                }
                else{
                   $where .= " AND tbl_news.description != ''";  
                }
                $query = $this->db->query('SELECT tbl_news.*, IFNULL(tbl_category.category_name, "") as category_name, IFNULL(tbl_subcategory.subcategory_name, "") as subcategory_name FROM tbl_news' . $join . ' ' . $where . ' ORDER BY id DESC LIMIT ' . $offset . ',' . $limit . '');
                //print_r('SELECT tbl_news.*, IFNULL(tbl_category.category_name, "") as category_name, IFNULL(tbl_subcategory.subcategory_name, "") as subcategory_name FROM tbl_news' . $join . ' ' . $where . ' ORDER BY id DESC LIMIT ' . $offset . ',' . $limit . '');
               
                $data = $query->getResult();
                if ($data) {
                    $query1 = $this->db->query('SELECT tbl_news.*, IFNULL(tbl_category.category_name, "") as category_name, IFNULL(tbl_subcategory.subcategory_name, "") as subcategory_name FROM tbl_news ' . $join . ' ' . $where . ' ORDER BY id DESC');
                    
                    $data1 = $query1->getResult();
                    $total = count($data1);
                    for ($i = 0; $i < count($data); $i++) {
                        $data[$i]->image = ($data[$i]->image) ? base_url() . '/public/images/news/' . $data[$i]->image : '';
                        if ($data[$i]->content_type == "video_upload") {
                            $data[$i]->content_value = ($data[$i]->content_value) ? base_url() . '/public/images/news_video/' . $data[$i]->content_value : '';
                        }
                        $img = array();
                        $img = $this->db->table('tbl_news_image')->select('other_image')->select('id')->where('news_id', $data[$i]->id)->get()->getResult();
                        for ($j = 0; $j < count($img); $j++) {
                            $img[$j]->other_image = ($img[$j]->other_image) ? base_url() . '/public/images/news/' . $data[$i]->id . '/' . $img[$j]->other_image : '';
                            $img[$j]->id = $img[$j]->id;
                        }
                        $data[$i]->image_data = $img;
                        $data[$i]->subcategory_name = ($data[$i]->subcategory_name != NULL) ? $data[$i]->subcategory_name : "";
                        $like = $this->db->table('tbl_news_like')->select('COUNT(id) as total')->where('news_id', $data[$i]->id)->where('status', '1')->get()->getResult();
                        $data[$i]->total_like = (!empty($like)) ? $like[0]->total : "0";
                        $dislike = $this->db->table('tbl_news_like')->select('COUNT(id) as total')->where('news_id', $data[$i]->id)->where('status', '2')->get()->getResult();
                        $data[$i]->total_dislike = (!empty($dislike)) ? $dislike[0]->total : "0";
                        $ulike = $this->db->table('tbl_news_like')->where('news_id', $data[$i]->id)->where('user_id', $user_id)->where('status', '1')->get()->getResult();
                        $data[$i]->like = (!empty($ulike)) ? '1' : '0';
                        $udislike = $this->db->table('tbl_news_like')->where('news_id', $data[$i]->id)->where('user_id', $user_id)->where('status', '2')->get()->getResult();
                        $data[$i]->dislike = (!empty($udislike)) ? '1' : '0';
                        $views = $this->db->table('	tbl_news_view')->select('COUNT(id) as total')->where('news_id', $data[$i]->id)->get()->getResult();
                        $data[$i]->total_views = (!empty($views)) ? $views[0]->total : "0";
                        $bookmark = $this->db->table('tbl_bookmark')->select('COUNT(id) as total')->where('news_id', $data[$i]->id)->get()->getResult();
                        $data[$i]->total_bookmark = (!empty($bookmark)) ? $bookmark[0]->total : "0";
                        $ubookmark = $this->db->table('tbl_bookmark')->where('news_id', $data[$i]->id)->where('user_id', $user_id)->get()->getResult();
                        $data[$i]->bookmark = (!empty($ubookmark)) ? '1' : '0';
                        if(isset($data[$i]->tag_id) && !empty($data[$i]->tag_id)){
                            $query2 = $this->db->query('SELECT GROUP_CONCAT(tag_name) as tag_name, GROUP_CONCAT(id) as tag_id FROM tbl_tag WHERE id IN(' . $data[$i]->tag_id . ')');
                            $res2 = $query2->getResult();
                            $data[$i]->tag_name = (!empty($res2)) ? $res2[0]->tag_name : '';
                            $data[$i]->tag_id = (!empty($res2)) ? $res2[0]->tag_id : $data[$i]->tag_id;
                        }
                    }
                    $response['error'] = "false";
                    $response['total'] = "$total";
                    $response['data'] = $data;
                } else {
                    $response['error'] = "true";
                    $response['message'] = "No Data Found";
                }
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function get_category()
    {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('language_id') && $this->request->getVar('language_id') != '') {
                $offset = ($this->request->getVar('offset')) ? $this->request->getVar('offset') : 0;
                $limit = ($this->request->getVar('limit')) ? $this->request->getVar('limit') : 10;
                $data = $this->db->table('tbl_category')->where('language_id', $this->request->getVar('language_id'))->limit($limit, $offset)->orderBy('id', 'DESC')->get()->getResult();
                if ($data) {
                    $data1 = $this->db->table('tbl_category')->where('language_id', $this->request->getVar('language_id'))->get()->getResult();
                    $total = count($data1);
                    for ($i = 0; $i < count($data); $i++) {
                        $url = base_url() . '/public/images/category/';
                        $data[$i]->image = ($data[$i]->image) ? $url . $data[$i]->image : '';
                        $s_res = $this->db->table('tbl_subcategory')->where('category_id', $data[$i]->id)->get()->getResult();
                        $data[$i]->subcategory = $s_res;
                    }
                    $response['error'] = "false";
                    $response['total'] = "$total";
                    $response['data'] = $data;
                } else {
                    $response['error'] = "true";
                    $response['message'] = "No Data Found";
                }
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function get_breaking_news()
    {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('language_id') && $this->request->getVar('language_id') != '') {
                $data = $this->db->table('tbl_breaking_news')->where('language_id', $this->request->getVar('language_id'))->orderBy('id', 'DESC')->get()->getResult();
                if ($data) {
                    for ($i = 0; $i < count($data); $i++) {
                        $data[$i]->image = ($data[$i]->image) ? base_url() . '/public/images/breaking_news/' . $data[$i]->image : '';
                        if ($data[$i]->content_type == "video_upload") {
                            $data[$i]->content_value = ($data[$i]->content_value) ? base_url() . '/public/images/breaking_news_video/' . $data[$i]->content_value : '';
                        }
                        $views = $this->db->table('tbl_breaking_news_view')->select('COUNT(id) as total')->where('breaking_news_id', $data[$i]->id)->get()->getResult();
                        $data[$i]->total_views = (!empty($views)) ? $views[0]->total : "0";
                    }
                    $response['error'] = "false";
                    $response['data'] = $data;
                } else {
                    $response['error'] = "true";
                    $response['message'] = "No Data Found";
                }
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function get_settings() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            $data = $this->db->table('tbl_settings')->get()->getResult();
            $default_language = $this->db->table('tbl_settings')->where('type', 'default_language')->get()->getResult();
            $default_language = json_decode($default_language[0]->message, true);
            $language_data = $this->db->table('tbl_languages')->select( 'id,code,isRTL')->where('id', $default_language)->get()->getResult();
            if ($data) {
                for ($i = 0; $i < count($data); $i++) {
                    $data1[$data[$i]->type] = $data[$i]->message;
                }
                $data1['default_language'] = $language_data;
                $response['error'] = "false";
                $response['data'] = $data1;
            } else {
                $response['error'] = "true";
                $response['message'] = "No data found!";
            }
        }
        return $this->respond($response);
    }
    public function get_user_by_id() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('user_id')) {
                $user_id = $this->request->getVar('user_id');
                $res = $this->db->table('tbl_users')->where('id', $user_id)->get()->getResult();
                if ($res) {
                    for ($i = 0; $i < count($res); $i++) {
                        if (isset($res[$i]->profile) && filter_var($res[$i]->profile, FILTER_VALIDATE_URL) === FALSE) {
                            // Not a valid URL. Its a image only or empty
                            $res[$i]->profile = (!empty($res[$i]->profile)) ? base_url() . '/public/images/profile/' . $res[$i]->profile : '';
                        }
                        $cat = $this->db->table('tbl_users_category')->select('category_id')->where('user_id', $user_id)->get()->getResult();
                        $res[$i]->category_id = ($cat) ? $cat[0]->category_id : '';
                    }
                    $response['error'] = "false";
                    $response['data'] = $res;
                } else {
                    $response['error'] = "true";
                    $response['message'] = "No Data Found";
                }
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function update_fcm_id() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('user_id') && $this->request->getVar('fcm_id')) {
                $user_id = $this->request->getVar('user_id');
                $fcm_id = $this->request->getVar('fcm_id');
                $data['fcm_id'] = $fcm_id;
                $this->db->table('tbl_users')->where('id', $user_id)->update($data);
                $response['error'] = "false";
                $response['message'] = "updated successfully";
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function register_token() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }

        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('token')) {
                $token = $this->request->getVar('token');
                $data = $this->db->table('tbl_token')->where('token', $token)->get()->getResult();
               
                if (empty($data)) {
                    $data = ['token' => $token];
                    $this->db->table('tbl_token')->insert($data);
                    $response['error'] = "false";
                    $response['message'] = "Device registered successfully";
                } else {
                    $response['error'] = "true";
                    $response['message'] = "Device already registered";
                }
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function set_profile_image() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            $image = $this->request->getFile('image');
            if ($this->request->getVar('user_id') && $image->getClientName() != '') {
                if (!is_dir('public/images/profile')) {
                    mkdir('./public/images/profile', 0777, TRUE);
                }
                $user_id = $this->request->getVar('user_id');
                $data = $this->db->table('tbl_users')->where('id', $user_id)->get()->getResult();
                if ($data[0]->profile != "") {
                    if (file_exists('public/images/profile/' . $data[0]->profile)) {
                        unlink('public/images/profile/' . $data[0]->profile);
                    }
                }
                $newName = microtime(TRUE) . '.' . $image->getClientExtension();
                if ($image->isValid() && !$image->hasMoved()) {
                    $image->move('public/images/profile', $newName);
                }
                if ($image->hasMoved()) {
                    $data = ['profile' => $newName];
                    $this->db->table('tbl_users')->where('id', $user_id)->update($data);
                    $response['error'] = "false";
                    $response['message'] = 'File uploaded successfully!';
                    $response['file_path'] = base_url() . '/public/images/profile/' . $newName;
                } else {
                    $response['error'] = "true";
                    $response['message'] = "Could not move the file!";
                }
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function update_profile() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('user_id')) {
                $user_id = $this->request->getVar('user_id');
                $data = array();
                if ($this->request->getVar('name')) {
                    $name = $this->request->getVar('name');
                    $data['name'] = $name;
                }
                if ($this->request->getVar('mobile')) {
                    $mobile = $this->request->getVar('mobile');
                    $data['mobile'] = $mobile;
                }
                if ($this->request->getVar('email')) {
                    $email = $this->request->getVar('email');
                    $data['email'] = $email;
                }
                $this->db->table('tbl_users')->where('id', $user_id)->update($data);
                $data1 = $this->db->table('tbl_users')->where('id', $user_id)->get()->getRow();
                if (isset($data1->profile) && filter_var($data1->profile, FILTER_VALIDATE_URL) === FALSE) {
                    // Not a valid URL. Its a image only or empty
                    $data1->profile = (!empty($data1->profile)) ? base_url() . '/public/images/profile/' . $data1->profile : '';
                }
                    
                $response['error'] = "false";
                $response['message'] = "Profile updated successfully";
                $response['data'] = $data1;
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function user_signup() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('firebase_id') && $type = $this->request->getVar('type')) {
                $firebase_id = $this->request->getVar('firebase_id');
                $email = ($this->request->getVar('email')) ? $this->request->getVar('email') : "";
                $type = $this->request->getVar('type');
                $name = ($this->request->getVar('name')) ? $this->request->getVar('name') : "";
                $mobile = ($this->request->getVar('mobile')) ? $this->request->getVar('mobile') : "";
                $status = ($this->request->getVar('status')) ? $this->request->getVar('status') : "1";
                $profile = ($this->request->getVar('profile')) ? $this->request->getVar('profile') : "";
                $fcm_id = ($this->request->getVar('fcm_id')) ? $this->request->getVar('fcm_id') : "";
                $res = $this->db->table('tbl_users')->where('firebase_id', $firebase_id)->get()->getResult();
                if (empty($res)) {
                    $data = [
                        'firebase_id' => $firebase_id,
                        'name' => $name,
                        'email' => $email,
                        'mobile' => $mobile,
                        'type' => $type,
                        'profile' => $profile,
                        'status' => $status,
                        'date' => $this->toDate,
                        'fcm_id' => $fcm_id
                    ];
                    $this->db->table('tbl_users')->insert($data);
                    $insert_id = $this->db->insertID();
                    $data1 = $this->db->table('tbl_users')->where('id', $insert_id)->get()->getResult();
                    $data1[0]->is_login = '1';
                    $response['error'] = "false";
                    $response['message'] = "User Registered successfully";
                    $response['data'] = $data1[0];
                } else {
                    if($res[0]->status == 1){
                    if (!empty($fcm_id)) {
                        $data = ['fcm_id' => $fcm_id];
                        $this->db->table('tbl_users')->where('firebase_id', $firebase_id)->update($data);
                        $res = $this->db->table('tbl_users')->where('firebase_id', $firebase_id)->get()->getResult();
                    }
                    for ($i = 0; $i < count($res); $i++) {
                       if (isset($res[$i]->profile) && filter_var($res[$i]->profile, FILTER_VALIDATE_URL) === FALSE) {
                            // Not a valid URL. Its a image only or empty
                            $res[$i]->profile = (!empty($res[$i]->profile)) ? base_url() . '/public/images/profile/' . $res[$i]->profile : '';
                        }
                        $res[$i]->is_login = '0';
                    }
                    $response['error'] = "false";
                    $response['message'] = "Successfully logged in";
                    $response['data'] = $res[0];
                }else{
                    $response['error'] = "true";
                    $response['message'] = "Your Account is Deactivated.";
                }
                }
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function generate_token() {
        $payload = [
            'iat' => time(), /* issued at time */
            'iss' => 'WRTEAM',
             'exp' => time() + (30 * 60),
            'sub' => 'WRTEAM Authentication'
        ];
        return JWT::encode($payload, $this->JWT_KEY, 'HS256');
    }
    public function verify_token() {
        try {
            $token = JWT::getBearerToken();      
        } catch (\Exception $e) {
            $response['error'] = "true";
            $response['message'] = $e->getMessage();
            return $this->respond($response);
        }
        if (!empty($token)) {
            try {
                $payload = JWT::decode($token, new Key($this->JWT_KEY, 'HS256'));
                if (!isset($payload->iss) || $payload->iss != 'WRTEAM') {
                    $response['error'] = "true";
                    $response['message'] = 'Invalid Hash';
                    return $this->respond($response);
                }
            } catch (\Exception $e) {
                $response['error'] = "true";
                $response['message'] = $e->getMessage();
                return $this->respond($response);
            }
        } else {
            $response['error'] = "true";
            $response['message'] = "Unauthorized access not allowed";
            return $this->respond($response);
        }
    }
     public function get_question()
    {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('user_id') && $this->request->getVar('language_id')) {
                $offset = ($this->request->getVar('offset')) ? $this->request->getVar('offset') : 0;
                $limit = ($this->request->getVar('limit')) ? $this->request->getVar('limit') : 10;
                $user_id = $this->request->getVar('user_id');
                $chk_que = $this->db->table('tbl_survey_result')->where('user_id', $user_id)->get()->getResult();
                $val = '';
                if ($chk_que) {
                    $question = array();
                    for ($k = 0; $k < count($chk_que); $k++) {
                        $question[$k] = $chk_que[$k]->question_id;
                    }
                    $val = implode(',', $question);
                }
                $where = "WHERE status='1' AND language_id=" . $this->request->getVar('language_id') . "";
                if ($val != '') {
                    $where .= " AND id NOT IN ($val)";
                }
                $query = $this->db->query("SELECT * FROM tbl_survey_question $where ORDER BY id DESC LIMIT $offset ,$limit");
                $data = $query->getResult();
                if ($data) {
                    $query1 = $this->db->query("SELECT * FROM tbl_survey_question $where ORDER BY id DESC");
                    $data1 = $query1->getResult();
                    $total = count($data);
                    for ($i = 0; $i < count($data); $i++) {
                        $option = array();
                        $option = $this->db->table('tbl_survey_option')->where('question_id', $data[$i]->id)->get()->getResult();
                        for ($j = 0; $j < count($option); $j++) {
                            $option[$j]->options = ($option[$j]->options) ? $option[$j]->options : '';
                        }
                        $data[$i]->option = $option;
                    }
                    $response['error'] = "false";
                    $response['total'] = "$total";
                    $response['data'] = $data;
                } else {
                    $response['error'] = "true";
                    $response['message'] = "No Data Found";
                }
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function set_question_result() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('question_id') && $this->request->getVar('option_id') && $this->request->getVar('user_id')) {
                $question_id = $this->request->getVar('question_id');
                $option_id = $this->request->getVar('option_id');
                $user_id = $this->request->getVar('user_id');
                $data = [
                    'user_id' => $user_id,
                    'question_id' => $question_id,
                    'option_id' => $option_id,
                ];
                $this->db->table('tbl_survey_result')->insert($data);
                $get_val = $this->db->table('tbl_survey_option')->where('id', $option_id)->get()->getResult();
                $counter = ($get_val[0]->counter) + 1;
                $data2 = [
                    'counter' => $counter,
                ];
                $this->db->table('tbl_survey_option')->where('id', $option_id)->update($data2);
                $response['error'] = "false";
                $response['message'] = "Data inserted successfully";
            } else {
                $response['error'] = "true";
                $response['message'] = "No Data Found";
            }
        }
        return $this->respond($response);
    }
    public function get_question_result()
    {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('user_id') && $this->request->getVar('language_id')) {
                $offset = ($this->request->getVar('offset')) ? $this->request->getVar('offset') : 0;
                $limit = ($this->request->getVar('limit')) ? $this->request->getVar('limit') : 10;
                $user_id = $this->request->getVar('user_id');
                $chk_que = $this->db->table('tbl_survey_result')->where('user_id', $user_id)->get()->getResult();
                $val = '';
                if ($chk_que) {
                    $question = array();
                    for ($k = 0; $k < count($chk_que); $k++) {
                        $question[$k] = $chk_que[$k]->question_id;
                    }
                    $val = implode(',', $question);
                    $where = "WHERE status='1' AND language_id=" . $this->request->getVar('language_id') . "";
                    if ($val != '') {
                        $where .= " AND id IN ($val)";
                    }
                    $query = $this->db->query("SELECT * FROM tbl_survey_question $where ORDER BY id DESC LIMIT $offset ,$limit");
                    $data = $query->getResult();
                    if ($data) {
                        $query1 = $this->db->query("SELECT * FROM tbl_survey_question $where ORDER BY id DESC");
                        $data1 = $query1->getResult();
                        $total = count($data);
                        for ($i = 0; $i < count($data); $i++) {
                            $get_user = $this->db->table('tbl_survey_result')->where('question_id', $data[$i]->id)->get()->getResult();
                            $total_user = count($get_user);
                            $option = array();
                            $option = $this->db->table('tbl_survey_option')->select('options,counter')->where('question_id', $data[$i]->id)->get()->getResult();
                            for ($j = 0; $j < count($option); $j++) {
                                $option[$j]->options = ($option[$j]->options) ? $option[$j]->options : '';
                                $option[$j]->counter = ($option[$j]->counter) ? $option[$j]->counter : '0';
                                $per = $option[$j]->counter * 100 / $total_user;
                                $per = round($per, 2);
                                $option[$j]->percentage = "$per";
                            }
                            $data[$i]->option = $option;
                        }
                        $response['error'] = "false";
                        $response['total'] = "$total";
                        $response['data'] = $data;
                    } else {
                        $response['error'] = "true";
                        $response['message'] = "No Data Found";
                    }
                } else {
                    $response['error'] = "true";
                    $response['message'] = "No Data Found";
                }
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    // public function set_news() {
    //     if ($this->verify_token()) {
    //         return $this->verify_token();
    //     }
    //     if ($this->access_key != $this->request->getVar('access_key')) {
    //         $response['error'] = "true";
    //         $response['message'] = "Invalid Access Key";
    //     } else {
    //         if ($this->request->getVar('category_id') && $this->request->getVar('title') && $this->request->getVar('language_id')) {
    //         if (!is_dir('public/images/news')) {
    //             mkdir('./public/images/news', 0777, TRUE);
    //         }
    //         $image = $this->request->getFile('image');
    //         $newName = microtime(TRUE) . '.' . $image->getClientExtension();
    //         if ($image->isValid() && !$image->hasMoved()) {
    //             $image->move('public/images/news', $newName);
    //         }
    //         if ($image->hasMoved()) {
    //             $content_type = $this->request->getVar('content_type');
    //             if ($content_type == "standard_post") {
    //                 $content_value = "";
    //             } else if ($content_type == "video_youtube") {
    //                 $content_value = $this->request->getVar('url');
    //             } else if ($content_type == "video_other") {
    //                 $content_value = $this->request->getVar('url');
    //             } else if ($content_type == "video_upload") {
    //                 if (!is_dir('public/images/news_video')) {
    //                     mkdir('./public/images/news_video', 0777, TRUE);
    //                 }
    //                 $file = $this->request->getFile('video_file');
    //                 $fileName = microtime(TRUE) . '.' . $file->getClientExtension();
    //                 if ($file->isValid() && !$file->hasMoved()) {
    //                     $file->move('public/images/news_video', $fileName);
    //                 }
    //                 $content_value = $fileName;
    //             }
    //             $data = [
    //                 'category_id' => ($this->request->getVar('category_id')) ? $this->request->getVar('category_id') : 0,
    //                 'subcategory_id' => ($this->request->getVar('subcategory_id')) ? $this->request->getVar('subcategory_id') : 0,
    //                 'tag_id' => ($this->request->getVar('tag_id') ) ? $this->request->getVar('tag_id') : '',
    //                 'title' => $this->request->getVar('title'),
    //                 'date' => date('Y-m-d H:i:s'),
    //                 'content_type' => $this->request->getVar('content_type'),
    //                 'content_value' =>$content_value,
    //                 'description' => ($this->request->getVar('description')) ? $this->request->getVar('description') : '',
    //                 'image' => $newName,
    //                 'user_id' => ($this->request->getVar('user_id')) ? $this->request->getVar('user_id') : 0,
    //                 'status' => 0 ,
    //                 'show_till' => ($this->request->getVar('show_till')) ? $this->request->getVar('show_till') : '',
    //                 'language_id' => $this->request->getVar('language_id'),
    //             ];
    //             $this->db->table('tbl_news')->insert($data);
    //             $insert_id = $this->db->insertID();
    //             if ($this->request->getFileMultiple('ofile')) {
    //                 foreach ($this->request->getFileMultiple('ofile') as $file1) {
    //                     if (!is_dir('public/images/news/' . $insert_id)) {
    //                         mkdir('./public/images/news/' . $insert_id, 0777, TRUE);
    //                     }
    //                     $fileName1 = microtime(TRUE) . '.' . $file1->getClientExtension();
    //                     $extension = $file1->getClientExtension();
    //                     $allowedExts = array("gif", "jpeg", "jpg", "png", "GIF", "JPEG", "JPG", "PNG");
    //                     if (in_array($extension, $allowedExts)) {
    //                         if ($file1->isValid() && !$file1->hasMoved()) {
    //                             $file1->move('public/images/news/' . $insert_id, $fileName1);
    //                         }
    //                         if ($file1->hasMoved()) {
    //                             $data = [
    //                                 'news_id' => $insert_id,
    //                                 'other_image' => $fileName1
    //                             ];
    //                             $this->db->table('tbl_news_image')->insert($data);
    //                         }
    //                     }
    //                 }
    //             }
    //             $response['error'] = "false";
    //             $response['message'] = "Data inserted successfully";
    //         } else {
    //             $response['error'] = "true";
    //             $response['message'] = "Please check Image";
    //         }
    //     }else{
    //         $response['error'] = "true";
    //         $response['message'] = "Please fill all the data and submit!"; 
    //     }
    //     }
    //     return $this->respond($response);
    // }
    public function set_news() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
           if ($this->request->getVar('action_type') && $this->request->getVar('action_type')=='2') {
            if ($this->request->getVar('news_id')) {
                $news_id = $this->request->getVar('news_id');
                $data = array();
                if ($this->request->getVar('user_id')) {
                    $user_id = $this->request->getVar('user_id');
                    $data['user_id'] = $user_id;
                }
                if ($this->request->getVar('category_id')) {
                    $category_id = $this->request->getVar('category_id');
                    $data['category_id'] = $category_id;
                }
                if ($this->request->getVar('subcategory_id')) {
                    $subcategory_id = $this->request->getVar('subcategory_id');
                    $data['subcategory_id'] = $subcategory_id;
                }
                if ($this->request->getVar('tag_id')) {
                    $tag_id = $this->request->getVar('tag_id');
                    $data['tag_id'] = $tag_id;
                }
                if ($this->request->getVar('title')) {
                    $title = $this->request->getVar('title');
                    $data['title'] = $title;
                }
                if ($this->request->getVar('date')) {
                    $date = $this->request->getVar('date');
                    $data['date'] = $date;
                }
                if ($this->request->getVar('description')) {
                    $description = $this->request->getVar('description');
                    $data['description'] = $description;
                }
                if ($this->request->getVar('status')) {
                    $status = $this->request->getVar('status');
                    $data['status'] = $status;
                }
                if ($this->request->getVar('show_till')) {
                    $show_till = $this->request->getVar('show_till');
                    $data['show_till'] = $show_till;
                }
                if ($this->request->getVar('language_id')) {
                    $language_id = $this->request->getVar('language_id');
                    $data['language_id'] = $language_id;
                }
                if ($this->request->getVar('content_type')) {
                $content_type = $this->request->getVar('content_type');
                if ($content_type == "standard_post") {
                    $content_value = "";
                    $data['content_value'] = $content_value;
                } else if ($content_type == "video_youtube") {
                    $content_value = $this->request->getVar('content_data');
                    $data['content_value'] = $content_value;
                } else if ($content_type == "video_other") {
                    $content_value = $this->request->getVar('content_data');
                    $data['content_value'] = $content_value;
                }else if ($content_type == "video_upload") {
                if($this->request->getFile('content_data') && $this->request->getFile('content_data')->getClientName() != null) {
                    if (!is_dir('public/images/news_video')) {
                        mkdir('./public/images/news_video', 0777, TRUE);
                    }
                $file = $this->request->getFile('content_data');
                $fileName = microtime(TRUE) . '.' . $file->getClientExtension();
                if ($file->isValid() && !$file->hasMoved()) {
                    $file->move('public/images/news_video', $fileName);
                }
                $content_value = $fileName;
                $data['content_value'] = $content_value;
               
                 }
            }
                    $data['content_type'] = $this->request->getVar('content_type');
                    
                }
                if($this->request->getFile('image') && $this->request->getFile('image')->getClientName() != null) {
                    
                    $image = $this->request->getFile('image');
                    if (!is_dir('public/images/news')) {
                        mkdir('./public/images/news', 0777, TRUE);
                    }
                   $getdata = $this->db->table('tbl_news')->where('id', $news_id)->get()->getResult();
                    if ($getdata[0]->image != "") {
                        if (file_exists('public/images/news/' . $getdata[0]->image)) {
                            unlink('public/images/news/' . $getdata[0]->image);
                        }
                    }
                    if($getdata[0]->content_type == 'video_upload' ){
                        $news_video =  'public/images/news_video/' . $getdata[0]->content_value;
                        if (file_exists($news_video)) {
                            unlink($news_video);
                        }
                    }
                    $newName = microtime(TRUE) . '.' . $image->getClientExtension();
                    if ($image->isValid() && !$image->hasMoved()) {
                        $image->move('public/images/news', $newName);
                        $data['image'] = $newName;
                    
                    }
                }
                if ($this->request->getFileMultiple('ofile')) {
                    foreach ($this->request->getFileMultiple('ofile') as $file1) {
                        if (!is_dir('public/images/news/' . $news_id)) {
                            mkdir('./public/images/news/' . $news_id, 0777, TRUE);
                        }
                        $fileName1 = microtime(TRUE) . '.' . $file1->getClientExtension();
                        $extension = $file1->getClientExtension();
                        $allowedExts = array("gif", "jpeg", "jpg", "png", "GIF", "JPEG", "JPG", "PNG");
                        if (in_array($extension, $allowedExts)) {
                            if ($file1->isValid() && !$file1->hasMoved()) {
                                $file1->move('public/images/news/' . $news_id, $fileName1);
                            }
                            if ($file1->hasMoved()) {
                                $odata = [
                                    'news_id' => $news_id,
                                    'other_image' => $fileName1
                                ];
                                $this->db->table('tbl_news_image')->insert($odata);
                            }
                        }
                    }
                }
              
                $this->db->table('tbl_news')->where('id', $news_id)->update($data);
                $response['error'] = "false";
                $response['message'] = 'News Updated Successfully';
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
           } elseif ($this->request->getVar('action_type') && $this->request->getVar('action_type')=='1') {
            if ($this->request->getVar('category_id') && $this->request->getVar('title') && $this->request->getVar('language_id')) {
            if (!is_dir('public/images/news')) {
                mkdir('./public/images/news', 0777, TRUE);
            }
            $image = $this->request->getFile('image');
            $newName = microtime(TRUE) . '.' . $image->getClientExtension();
            if ($image->isValid() && !$image->hasMoved()) {
                $image->move('public/images/news', $newName);
            }
            if ($image->hasMoved()) {
                $content_type = $this->request->getVar('content_type');
                if ($content_type == "standard_post") {
                    $content_value = "";
                } else if ($content_type == "video_youtube") {
                    $content_value = $this->request->getVar('content_data');
                } else if ($content_type == "video_other") {
                    $content_value = $this->request->getVar('content_data');
                } else if ($content_type == "video_upload") {
                    if (!is_dir('public/images/news_video')) {
                        mkdir('./public/images/news_video', 0777, TRUE);
                    }
                    $file = $this->request->getFile('content_data');
                    $fileName = microtime(TRUE) . '.' . $file->getClientExtension();
                    if ($file->isValid() && !$file->hasMoved()) {
                        $file->move('public/images/news_video', $fileName);
                    }
                    $content_value = $fileName;
                }
                $data = [
                    'category_id' => ($this->request->getVar('category_id')) ? $this->request->getVar('category_id') : 0,
                    'subcategory_id' => ($this->request->getVar('subcategory_id')) ? $this->request->getVar('subcategory_id') : 0,
                    'tag_id' => ($this->request->getVar('tag_id') ) ? $this->request->getVar('tag_id') : '',
                    'title' => $this->request->getVar('title'),
                    'date' => date('Y-m-d H:i:s'),
                    'content_type' => $this->request->getVar('content_type'),
                    'content_value' =>$content_value,
                    'description' => ($this->request->getVar('description')) ? $this->request->getVar('description') : '',
                    'image' => $newName,
                    'user_id' => ($this->request->getVar('user_id')) ? $this->request->getVar('user_id') : 0,
                    'status' => 0 ,
                    'show_till' => ($this->request->getVar('show_till')) ? $this->request->getVar('show_till') : '',
                    'language_id' => $this->request->getVar('language_id'),
                ];
                $this->db->table('tbl_news')->insert($data);
                $insert_id = $this->db->insertID();
                if ($this->request->getFileMultiple('ofile')) {
                    foreach ($this->request->getFileMultiple('ofile') as $file1) {
                        if (!is_dir('public/images/news/' . $insert_id)) {
                            mkdir('./public/images/news/' . $insert_id, 0777, TRUE);
                        }
                        $fileName1 = microtime(TRUE) . '.' . $file1->getClientExtension();
                        $extension = $file1->getClientExtension();
                        $allowedExts = array("gif", "jpeg", "jpg", "png", "GIF", "JPEG", "JPG", "PNG");
                        if (in_array($extension, $allowedExts)) {
                            if ($file1->isValid() && !$file1->hasMoved()) {
                                $file1->move('public/images/news/' . $insert_id, $fileName1);
                            }
                            if ($file1->hasMoved()) {
                                $data = [
                                    'news_id' => $insert_id,
                                    'other_image' => $fileName1
                                ];
                                $this->db->table('tbl_news_image')->insert($data);
                            }
                        }
                    }
                }
                $response['error'] = "false";
                $response['message'] = "Data inserted successfully";
            } else {
                $response['error'] = "true";
                $response['message'] = "Please check Image";
            }
        }else{
            $response['error'] = "true";
            $response['message'] = "Please fill all the data and submit!"; 
        }
               
           }
        }
        return $this->respond($response);
    }
    public function delete_news_images() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('id')) {
            $id = $this->request->getVar('id');
            $data_image = $this->db->table('tbl_news_image')->where('id', $id)->get()->getResult();
            $news_image = 'public/images/news/'. $data_image[0]->news_id .'/' . $data_image[0]->other_image;
            if (file_exists($news_image)) {
                unlink($news_image);
            }
            $this->db->table('tbl_news_image')->where('id', $id)->delete();
                $response['error'] = "false";
                $response['message'] = "Image deleted!";
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function delete_news() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('id')) {
            $id = $this->request->getVar('id');
            $dirPath = 'public/images/news/' . $id;
            $data = $this->db->table('tbl_news_image')->where('news_id', $id)->get()->getResult();
            if($data){
                for ($i = 0; $i < count($data); $i++) {
                    $otherImage = $dirPath . '/' . $data[$i]->other_image;
                    if (file_exists($otherImage)) {
                        unlink($otherImage);
                    }
                }
                if (is_dir($dirPath)) {
                    rmdir($dirPath);
                }
            }
            $this->db->table('tbl_news_image')->where('news_id', $id)->delete();
            $data_image = $this->db->table('tbl_news')->where('id', $id)->get()->getResult();
            $news_image = 'public/images/news/' . $data_image[0]->image;
            if (file_exists($news_image)) {
                unlink($news_image);
            }
            if($data_image[0]->content_type == 'video_upload' ){
                $news_video =  'public/images/news_video/' . $data_image[0]->content_value;
                if (file_exists($news_video)) {
                    unlink($news_video);
                }
            }
            $this->db->table('tbl_news')->where('id', $id)->delete();
                $response['error'] = "false";
                $response['message'] = "News deleted!";
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function get_tag() {     
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            $offset = ($this->request->getVar('offset')) ? $this->request->getVar('offset') : 0;
            $limit = ($this->request->getVar('limit')) ? $this->request->getVar('limit') : 10;
            $language_id = $this->request->getVar('language_id');
            $data = $this->db->table('tbl_tag')->limit($limit, $offset)->where('language_id', $language_id)->orderBy('id', 'DESC')->get()->getResult();
            if ($data) {
                $data1 = $this->db->table('tbl_tag')->where('language_id', $language_id)->get()->getResult();
                $total = count($data1);
                $response['error'] = "false";
                $response['total'] = "$total";
                $response['data'] = $data;
            } else {
                $response['error'] = "true";
                $response['message'] = "No Data Found";
            }
        }
        return $this->respond($response);
    }
    public function get_videos() {    
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('language_id') && $this->request->getVar('language_id') != '') {
                $offset = ($this->request->getVar('offset')) ? $this->request->getVar('offset') : 0;
                $limit = ($this->request->getVar('limit')) ? $this->request->getVar('limit') : 10000;
                $language_id = $this->request->getVar('language_id');
                $data = $this->db->table('tbl_news')->select('id,date,image,title,content_type,content_value')->limit($limit, $offset)->orderBy('id', 'DESC')->whereIn('content_type', explode(",", 'video_upload,video_youtube,video_other'))->where('language_id', $language_id)->where('status', 1)->get()->getResult();
                if ($data) {
                    for ($i = 0; $i < count($data); $i++) {
                        $data[$i]->image = ($data[$i]->image) ? base_url() . '/public/images/news/' . $data[$i]->image : '';
                        if ($data[$i]->content_type == "video_upload") {
                            $data[$i]->content_value = ($data[$i]->content_value) ? base_url() . '/public/images/news_video/' . $data[$i]->content_value : '';
                        }
                    }
                    $data1 = $this->db->table('tbl_news')->select('id,date,image,title,content_type,content_value')->limit($limit, $offset)->orderBy('id', 'DESC')->whereIn('content_type', explode(",", 'video_upload,video_youtube,video_other'))->where('language_id', $language_id)->where('status', 1)->get()->getResult();
                    $total = count($data1);
                    $response['error'] = "false";
                    $response['total'] = "$total";
                    $response['data'] = $data;
                } else {
                    $response['error'] = "true";
                    $response['message'] = "No Data Found";
                }
            }else{
                $response['error'] = "true";
                $response['message'] = "Please Pass Language ID";
            }
        }
        return $this->respond($response);
    }
    public function get_pages()
    {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('language_id') && $this->request->getVar('language_id') != '') {
                $offset = ($this->request->getVar('offset')) ? $this->request->getVar('offset') : 0;
                $limit = ($this->request->getVar('limit')) ? $this->request->getVar('limit') : 10;
                $data = $this->db->table('tbl_pages')->where('language_id', $this->request->getVar('language_id'))->where('status', 1)->limit($limit, $offset)->orderBy('id', 'DESC')->get()->getResult();
                if ($data) {
                    for ($i = 0; $i < count($data); $i++) {
                        $data[$i]->page_icon = ($data[$i]->page_icon) ? base_url() . '/public/images/pages/' . $data[$i]->page_icon : '';
                    }
                    $data1 = $this->db->table('tbl_pages')->where('language_id', $this->request->getVar('language_id'))->where('status', 1)->get()->getResult();
                    $total = count($data1);
                    $response['error'] = "false";
                    $response['total'] = "$total";
                    $response['data'] = $data;
                } else {
                    $response['error'] = "true";
                    $response['message'] = "No Data Found";
                }
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function get_languages_list()
    {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            $result = $this->db->table('tbl_languages')->where('status', 1)->get()->getResult();
            $default_language = $this->db->table('tbl_settings')->where('type', 'default_language')->get()->getResult();
            $default_language = json_decode($default_language[0]->message, true);
            for ($i = 0; $i < count($result); $i++) {
                $result[$i]->image = ($result[$i]->image) ? base_url() . '/public/images/flags/' . $result[$i]->image : '';
            }
            if (!empty($result)) {
                $response['error'] = "false";
                $response['default_language'] = $default_language; 
                $response['data'] = $result;
            } else {
                $response['error'] = "true";
                $response['message'] = "No data found!";
            }
        }
        return $this->respond($response);
    }
    public function get_language_json_data()
    {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        try {
            $code = $this->request->getVar('code');
            $jsonString = file_get_contents(('app/Language/' . $code . '.json'));
            $jsonString = json_decode($jsonString);
            $response['error'] = "false";
            $response['data'] = $jsonString;
        } catch (\Exception $e) {
            $response['error'] = "false";
            $response['message'] = $e;
        }
        return $this->respond($response);
    }
    public function get_policy_pages()
    {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('language_id') && $this->request->getVar('language_id') != '') {
                $language_id = $this->request->getVar('language_id');
                $terms_policy = $this->db->table('tbl_pages')->where('is_termspolicy', '1')->where('language_id', $language_id)->get()->getRow();
                $privacy_policy = $this->db->table('tbl_pages')->where('is_privacypolicy', '1')->where('language_id', $language_id)->get()->getRow();
                if (!empty($terms_policy)) {
                    $response['error'] = "false";
                    $response['terms_policy'] = $terms_policy;
                    $response['privacy_policy'] = $privacy_policy;
                } else {
                    $response['error'] = "true";
                    $response['message'] = "No data found!";
                }
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function set_news_view()
    {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('user_id') && $this->request->getVar('news_id')) {
                $user_id = $this->request->getVar('user_id');
                $news_id = $this->request->getVar('news_id');
                    $data = $this->db->table('tbl_news_view')->where('user_id', $user_id)->where('news_id', $news_id)->get()->getResult();
                    if (empty($data)) {
                        $data = [
                            'user_id' => $user_id,
                            'news_id' => $news_id,
                        ];
                        $this->db->table('tbl_news_view')->insert($data);
                        $response['error'] = "false";
                        $response['message'] = "News View added successfully";
                    } else {
                        $response['error'] = "true";
                        $response['message'] = "News already viewed by this user.";
                    }
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function set_breaking_news_view()
    {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('user_id') && $this->request->getVar('breaking_news_id')) {
                $user_id = $this->request->getVar('user_id');
                $breaking_news_id = $this->request->getVar('breaking_news_id');
                $data = $this->db->table('tbl_breaking_news_view')->where('user_id', $user_id)->where('breaking_news_id', $breaking_news_id)->get()->getResult();
                if (empty($data)) {
                    $data = [
                        'user_id' => $user_id,
                        'breaking_news_id' => $breaking_news_id,
                    ];
                    $this->db->table('tbl_breaking_news_view')->insert($data);
                    $response['error'] = "false";
                    $response['message'] = "Breaking News View added successfully";
                } else {
                    $response['error'] = "true";
                    $response['message'] = "already Breaking News View by this user";
                }
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function get_featured_sections()
    {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('language_id') && $this->request->getVar('language_id') != '') {
                $language_id = $this->request->getVar('language_id');
                $news_type = $this->request->getVar('news_type');
                $style_web = $this->request->getVar('style_web');
                $user_id = $this->request->getVar('user_id');
                $offset = ($this->request->getVar('offset')) ? $this->request->getVar('offset') : 0;
                $limit = ($this->request->getVar('limit')) ? $this->request->getVar('limit') : 6;
                if(!empty($news_type) && !empty($style_web)){
                    $data = $this->db->table('tbl_featured_sections')->where('language_id', $language_id)->where('news_type', $news_type)->where('status', 1)->where('style_web', $style_web)->orderBy('row_order', 'ASC')->get()->getResult();
                }else{
                    $data = $this->db->table('tbl_featured_sections')->where('language_id', $language_id)->where('status', 1)->orderBy('row_order', 'ASC')->get()->getResult();
                }
                
                $join = '';
                $where = '';
                $select = '';
                $orderby = '';
                if ($data) {
                    if(!empty($news_type) && !empty($style_web)){
                        $data1 = $this->db->table('tbl_featured_sections')->where('language_id', $language_id)->where('news_type', $news_type)->where('style_web', $style_web)->orderBy('row_order', 'ASC')->get()->getResult();
                    }else{
                        $data1 = $this->db->table('tbl_featured_sections')->where('language_id', $language_id)->orderBy('row_order', 'ASC')->get()->getResult();
                    }
                    
                    $total = count($data1);
                    for ($i = 0; $i < count($data); $i++) {
                        // 1. news type == news or video  (2. breaking_news mate code niche chhe) //
                        if ($data[$i]->news_type == 'news' || $data[$i]->news_type == 'videos') {
                            $table = 'tbl_news';
                            if ($data[$i]->filter_type == 'most_commented') {
                                if ($data[$i]->news_type == 'news') {
                                    // 1.1 most_commented news, 
                                    $join = ' LEFT JOIN tbl_category ON tbl_category.id = ' . $table . '.category_id';
                                    $join .= ' LEFT JOIN tbl_subcategory ON tbl_subcategory.id = ' . $table . '.subcategory_id';
                                    $join .= ' INNER JOIN ( SELECT news_id, count(*) AS newscount FROM tbl_comment GROUP BY news_id ) as tbl_comment on tbl_news.id = tbl_comment.news_id';
                                    $where = ' WHERE ' . $table . '.status = 1';
                                    $where .= ' AND ' . $table . '.language_id = ' . $language_id;
                                    $where .= " AND (tbl_news.show_till >= '" . $this->toDate . "' OR CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00')";
                                    $where .= " AND tbl_news.description != ''";
                                    if ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids != NULL) {
                                        $where .= ' AND (' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ') OR ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . '))';
                                    } elseif ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids == NULL) {
                                        $where .= ' AND ' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ')';
                                    } elseif ($data[$i]->category_ids == NULL && $data[$i]->subcategory_ids != NULL) {
                                        $where .= ' AND ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . ')';
                                    } elseif ($data[$i]->category_ids == NULL && $data[$i]->subcategory_ids == NULL) {
                                        $where .= ' ';
                                    }
                                    $select = ' ' . $table . '.*, tbl_category.category_name, tbl_subcategory.subcategory_name, tbl_comment.newscount';
                                    $orderby = ' tbl_comment.newscount DESC';
                                } elseif ($data[$i]->news_type == 'videos') {
                                    if ($data[$i]->videos_type == 'news') {
                                        //1.2 most commented news videos (comment feature available only in news)
                                        $join = ' LEFT JOIN tbl_category ON tbl_category.id = ' . $table . '.category_id';
                                        $join .= ' LEFT JOIN tbl_subcategory ON tbl_subcategory.id = ' . $table . '.subcategory_id';
                                        $join .= ' INNER JOIN ( SELECT news_id, count(*) AS newscount FROM tbl_comment GROUP BY news_id ) as tbl_comment on tbl_news.id = tbl_comment.news_id';
                                        $where = ' WHERE ' . $table . '.content_type IN ( "video_upload","video_youtube","video_other")';
                                        $where .= ' AND ' . $table . '.status = 1';
                                        $where .= ' AND ' . $table . '.language_id = ' . $language_id;
                                        $where .= " AND (tbl_news.show_till >= '" . $this->toDate . "' OR CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00')";
                                        if ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids != NULL) {
                                            $where .= ' AND (' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ') OR ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . '))';
                                        } elseif ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids == NULL) {
                                            $where .= ' AND ' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ')';
                                        } elseif ($data[$i]->category_ids == NULL && $data[$i]->subcategory_ids != NULL) {
                                            $where .= ' AND ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . ')';
                                        } elseif ($data[$i]->category_ids == NULL && $data[$i]->subcategory_ids == NULL) {
                                            $where .= ' ';
                                        }
                                        $select = ' ' . $table . '.*, IFNULL(tbl_category.category_name, "") as category_name, IFNULL(tbl_subcategory.subcategory_name, "") as subcategory_name, tbl_comment.newscount';
                                        $orderby = ' tbl_comment.newscount DESC';
                                    } 
                                }
                            } elseif ($data[$i]->filter_type == 'recently_added') {
                                if ($data[$i]->news_type == 'news') {
                                    //1.3 recently_added news
                                    $join = ' LEFT JOIN tbl_category ON tbl_category.id = ' . $table . '.category_id';
                                    $join .= ' LEFT JOIN tbl_subcategory ON tbl_subcategory.id = ' . $table . '.subcategory_id';
                                    $where = ' WHERE ' . $table . '.status = 1';
                                    $where .= ' AND ' . $table . '.language_id = ' . $language_id;
                                    $where .= " AND (tbl_news.show_till >= '" . $this->toDate . "' OR CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00')";
                                    $where .= " AND tbl_news.description != ''";
                                    if ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids != NULL) {
                                        $where .= ' AND (' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ') OR ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . '))';
                                    } elseif ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids == NULL) {
                                        $where .= ' AND ' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ')';
                                    } elseif ($data[$i]->category_ids == NULL && $data[$i]->subcategory_ids != NULL) {
                                        $where .= ' AND ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . ')';
                                    } elseif ($data[$i]->category_ids == NULL && $data[$i]->subcategory_ids == NULL) {
                                        $where .= ' ';
                                    }
                                    $select = ' ' . $table . '.*, IFNULL(tbl_category.category_name, "") as category_name, IFNULL(tbl_subcategory.subcategory_name, "") as subcategory_name';
                                    $orderby = ' ' . $table . '.id DESC';
                                } 
                                elseif ($data[$i]->news_type == 'videos') {
                                    if ($data[$i]->videos_type == 'news') {
                                        //1.4 recently_added news video
                                        $join = ' LEFT JOIN tbl_category ON tbl_category.id = ' . $table . '.category_id';
                                        $join .= ' LEFT JOIN tbl_subcategory ON tbl_subcategory.id = ' . $table . '.subcategory_id';
                                        $where = ' WHERE ' . $table . '.status = 1';
                                        $where .= ' AND ' . $table . '.content_type IN ( "video_upload","video_youtube","video_other")';
                                        $where .= ' AND ' . $table . '.language_id = ' . $language_id;
                                        $where .= " AND (tbl_news.show_till >= '" . $this->toDate . "' OR CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00')";
                                        if ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids != NULL) {
                                            $where .= ' AND (' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ') OR ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . '))';
                                        } elseif ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids == NULL) {
                                            $where .= ' AND ' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ')';
                                        } elseif ($data[$i]->category_ids == NULL && $data[$i]->subcategory_ids != NULL) {
                                            $where .= ' AND ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . ')';
                                        } elseif ($data[$i]->category_ids == NULL && $data[$i]->subcategory_ids == NULL) {
                                            $where .= ' ';
                                        }
                                        $select = ' ' . $table . '.*, IFNULL(tbl_category.category_name, "") as category_name, IFNULL(tbl_subcategory.subcategory_name, "") as subcategory_name';
                                        $orderby = ' ' . $table . '.id DESC';
                                    } elseif ($data[$i]->videos_type == 'breaking_news') {
                                         //1.5 recently_added breaking_news video
                                        $join = '';
                                        $table = 'tbl_breaking_news';
                                        $where = ' WHERE tbl_breaking_news.content_type IN ( "video_upload","video_youtube","video_other")';
                                        $where .= ' AND tbl_breaking_news.language_id = ' . $language_id;
                                        $select = ' tbl_breaking_news.* ';
                                        $orderby = ' tbl_breaking_news.id DESC';
                                    }
                                }
                            } elseif ($data[$i]->filter_type == 'most_viewed') {
                                if ($data[$i]->news_type == 'news') {
                                     //1.6 most_viewed news 
                                    $join = ' LEFT JOIN tbl_category ON tbl_category.id = ' . $table . '.category_id';
                                    $join .= ' LEFT JOIN tbl_subcategory ON tbl_subcategory.id = ' . $table . '.subcategory_id';
                                    $join .= ' INNER JOIN ( SELECT news_id, count(*) AS viewcount FROM tbl_news_view GROUP BY news_id ) as tbl_news_view on tbl_news.id = tbl_news_view.news_id';
                                    $where = ' WHERE ' . $table . '.status = 1';
                                    $where .= ' AND ' . $table . '.language_id = ' . $language_id;
                                    $where .= " AND (tbl_news.show_till >= '" . $this->toDate . "' OR CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00')";
                                    $where .= " AND tbl_news.description != ''";
                                    if ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids != NULL) {
                                        $where .= ' AND (' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ') OR ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . '))';
                                    } elseif ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids == NULL) {
                                        $where .= ' AND ' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ')';
                                    } elseif ($data[$i]->category_ids == NULL && $data[$i]->subcategory_ids != NULL) {
                                        $where .= ' AND ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . ')';
                                    }
                                    $select = ' ' . $table . '.*, IFNULL(tbl_category.category_name, "") as category_name, IFNULL(tbl_subcategory.subcategory_name, "") as subcategory_name, tbl_news_view.viewcount';
                                    $orderby = ' tbl_news_view.viewcount DESC';
                                } elseif ($data[$i]->news_type == 'videos') {
                                    if ($data[$i]->videos_type == 'news') {
                                         //1.7 most_viewed news video
                                         $join = ' LEFT JOIN tbl_category ON tbl_category.id = ' . $table . '.category_id';
                                $join .= ' LEFT JOIN tbl_subcategory ON tbl_subcategory.id = ' . $table . '.subcategory_id';
                                        $join .= ' INNER JOIN ( SELECT news_id, count(*) AS viewcount FROM tbl_news_view GROUP BY news_id ) as tbl_news_view on tbl_news.id = tbl_news_view.news_id';
                                        $where = ' WHERE ' . $table . '.content_type IN ( "video_upload","video_youtube","video_other")';
                                        $where .= ' AND ' . $table . '.status = 1';
                                        $where .= ' AND ' . $table . '.language_id = ' . $language_id;
                                        $where .= " AND (tbl_news.show_till >= '" . $this->toDate . "' OR CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00')";
                                        if ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids != NULL) {
                                            $where .= ' AND (' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ') OR ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . '))';
                                        } elseif ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids == NULL) {
                                            $where .= ' AND ' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ')';
                                        } elseif ($data[$i]->category_ids == NULL && $data[$i]->subcategory_ids != NULL) {
                                            $where .= ' AND ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . ')';
                                        } elseif ($data[$i]->category_ids == NULL && $data[$i]->subcategory_ids == NULL) {
                                            $where .= ' ';
                                        }
                                        $select = ' ' . $table . '.*, IFNULL(tbl_category.category_name, "") as category_name, IFNULL(tbl_subcategory.subcategory_name, "") as subcategory_name, tbl_news_view.viewcount';
                                        $orderby = ' tbl_news_view.viewcount DESC';
                                    } elseif ($data[$i]->videos_type == 'breaking_news') {
                                         //1.8 most_viewed breaking_news video
                                        $table = 'tbl_breaking_news';
                                        $join = ' INNER JOIN ( SELECT breaking_news_id, count(*) AS viewcount FROM tbl_breaking_news_view GROUP BY breaking_news_id ) as tbl_breaking_news_view on tbl_breaking_news.id = tbl_breaking_news_view.breaking_news_id';
                                        $where = 'WHERE tbl_breaking_news.content_type IN ( "video_upload","video_youtube","video_other")';
                                        $where .= ' AND tbl_breaking_news.language_id = ' . $language_id;
                                        $select = ' tbl_breaking_news.*, tbl_breaking_news_view.viewcount';
                                        $orderby = ' tbl_breaking_news_view.viewcount DESC';
                                    }
                                }
                            } elseif ($data[$i]->filter_type == 'most_favorite') {
                                //1.9 most_favorite news, video
                                $join = ' LEFT JOIN tbl_category ON tbl_category.id = ' . $table . '.category_id';
                                $join .= ' LEFT JOIN tbl_subcategory ON tbl_subcategory.id = ' . $table . '.subcategory_id';
                                $join .= ' INNER JOIN ( SELECT news_id, count(*) AS newscount FROM tbl_bookmark GROUP BY news_id ) as tbl_bookmark on tbl_news.id = tbl_bookmark.news_id';
                                $where = ' WHERE ' . $table . '.status = 1';
                                $where .= ' AND ' . $table . '.language_id = ' . $language_id;
                                $where .= " AND (tbl_news.show_till >= '" . $this->toDate . "' OR CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00')";
                                $where .= " AND tbl_news.description != ''";
                                if ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids != NULL) {
                                    $where .= ' AND (' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ') OR ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . '))';
                                } elseif ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids == NULL) {
                                    $where .= ' AND ' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ')';
                                } elseif ($data[$i]->category_ids == NULL && $data[$i]->subcategory_ids != NULL) {
                                    $where .= ' AND ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . ')';
                                }
                                if ($data[$i]->news_type == 'videos') {
                                    $where .= ' AND ' . $table . '.content_type IN ( "video_upload","video_youtube","video_other")';
                                }
                                $select = ' ' . $table . '.*, IFNULL(tbl_category.category_name, "") as category_name, IFNULL(tbl_subcategory.subcategory_name, "") as subcategory_name, tbl_bookmark.newscount';
                                $orderby = ' tbl_bookmark.newscount DESC';
                            } elseif ($data[$i]->filter_type == 'most_like') {
                                //1.9 most_favorite like, video
                                $join = ' LEFT JOIN tbl_category ON tbl_category.id = ' . $table . '.category_id';
                                $join .= ' LEFT JOIN tbl_subcategory ON tbl_subcategory.id = ' . $table . '.subcategory_id';
                                $join .= ' INNER JOIN ( SELECT news_id, count(*) AS likecount FROM tbl_news_like WHERE status="1" GROUP BY news_id ) as tbl_news_like on tbl_news.id = tbl_news_like.news_id';
                                $where = ' WHERE ' . $table . '.status = 1';
                                $where .= ' AND ' . $table . '.language_id = ' . $language_id;
                                $where .= " AND (tbl_news.show_till >= '" . $this->toDate . "' OR CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00')";
                                $where .= " AND tbl_news.description != ''";
                                if ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids != NULL) {
                                    $where .= ' AND (' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ') OR ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . '))';
                                } elseif ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids == NULL) {
                                    $where .= ' AND ' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ')';
                                } elseif ($data[$i]->category_ids == NULL && $data[$i]->subcategory_ids != NULL) {
                                    $where .= ' AND ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . ')';
                                }
                                if ($data[$i]->news_type == 'videos') {
                                    $where .= ' AND ' . $table . '.content_type IN ( "video_upload","video_youtube","video_other")';
                                }
                                $select = ' ' . $table . '.*, IFNULL(tbl_category.category_name, "") as category_name, IFNULL(tbl_subcategory.subcategory_name, "") as subcategory_name, tbl_news_like.likecount';
                                $orderby = ' tbl_news_like.likecount DESC';
                            } elseif ($data[$i]->filter_type == 'custom') {
                                //1.10 custom (based on selected category, subcategory)
                                if ($data[$i]->news_type == 'news') {
                                    //1.10.1 custom news
                                    $join = ' LEFT JOIN tbl_category ON tbl_category.id = ' . $table . '.category_id';
                                    $join .= ' LEFT JOIN tbl_subcategory ON tbl_subcategory.id = ' . $table . '.subcategory_id';
                                    $where = ' WHERE ' . $table . '.status = 1';
                                    $where .= ' AND ' . $table . '.id IN ( ' . $data[$i]->news_ids . ')  ';
                                    $where .= ' AND ' . $table . '.language_id = ' . $language_id;
                                    $where .= " AND (tbl_news.show_till >= '" . $this->toDate . "' OR CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00')";
                                    $where .= " AND tbl_news.description != ''";
                                    $select = ' ' . $table . '.*, IFNULL(tbl_category.category_name, "") as category_name, IFNULL(tbl_subcategory.subcategory_name, "") as subcategory_name';
                                    $orderby = ' tbl_news.id DESC';
                                } elseif ($data[$i]->news_type == 'videos') {
                                    if ($data[$i]->videos_type == 'news') {
                                        //1.10.1 custom news video
                                        $join = ' LEFT JOIN tbl_category ON tbl_category.id = ' . $table . '.category_id';
                                        $join .= ' LEFT JOIN tbl_subcategory ON tbl_subcategory.id = ' . $table . '.subcategory_id';
                                        $where = ' WHERE ' . $table . '.status = 1';
                                        $where .= ' AND ' . $table . '.content_type IN ( "video_upload","video_youtube","video_other")';
                                        $where .= ' AND ' . $table . '.id IN ( ' . $data[$i]->news_ids . ')  ';
                                        $where .= ' AND ' . $table . '.language_id = ' . $language_id;
                                        $where .= " AND (tbl_news.show_till >= '" . $this->toDate . "' OR CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00')";
                                        $select = ' ' . $table . '.*, IFNULL(tbl_category.category_name, "") as category_name, IFNULL(tbl_subcategory.subcategory_name, "") as subcategory_name';
                                        $orderby = ' tbl_news.id DESC';
                                    } elseif ($data[$i]->videos_type == 'breaking_news') {
                                        //1.10.1 custom breaking_news video
                                        $table = 'tbl_breaking_news';
                                        $join = ' ';
                                        $where = 'WHERE tbl_breaking_news.content_type IN ( "video_upload","video_youtube","video_other")';
                                        $where .= ' AND tbl_breaking_news.language_id = ' . $language_id;
                                        $select = ' tbl_breaking_news.*';
                                        $orderby = ' tbl_breaking_news.id DESC';
                                    }
                                }
                            }
                        } elseif ($data[$i]->news_type == 'breaking_news') {
                            //2. Breaking News
                            $table = 'tbl_breaking_news';
                            
                            if ($data[$i]->filter_type == 'recently_added') {
                                //2.1 Breaking News recently_added
                                $table = 'tbl_breaking_news';
                                $join = '';
                                $where = 'WHERE ' . $table . '.language_id = ' . $language_id;
                                $select = ' ' . $table . '.*';
                                $orderby = ' ' . $table . '.id DESC';
                               
                            } elseif ($data[$i]->filter_type == 'most_viewed') {
                                 //2.2 Breaking News most_viewed
                                $table = 'tbl_breaking_news';
                                $join = ' INNER JOIN ( SELECT breaking_news_id, count(*) AS viewcount FROM tbl_breaking_news_view GROUP BY breaking_news_id ) as tbl_breaking_news_view on tbl_breaking_news.id = tbl_breaking_news_view.breaking_news_id';
                                $where = 'WHERE ' . $table . '.language_id = ' . $language_id;
                                $select = ' ' . $table . '.*, tbl_breaking_news_view.viewcount';
                                $orderby = ' tbl_breaking_news_view.viewcount DESC';
                            } elseif ($data[$i]->filter_type == 'custom') {
                                 //2.3 Breaking News custom(based on selected category, subcategory)
                                $table = 'tbl_breaking_news';
                                $join = ' ';
                                $where = ' WHERE ' . $table . '.id IN ( ' . $data[$i]->news_ids . ')  ';
                                $where .= ' AND ' . $table . '.language_id = ' . $language_id;
                                $select = ' ' . $table . '.*';
                                $orderby = ' ' . $table . '.id DESC';
                            }
                        } elseif($data[$i]->is_based_on_user_choice == '1'){
                            // based_on_user's_choice_section code ** different from above all section //
                           $select = '';
                           $table = 'tbl_news';
                            $user_category = $this->db->table('tbl_users_category')->select('category_id')->where('user_id', $user_id)->get()->getRow();
                            if($user_category != null){
                                $table = 'tbl_news';
                                $user_category = $this->db->table('tbl_users_category')->select('category_id')->where('user_id', $user_id)->get()->getRow()->category_id;;  
                                $join = ' LEFT JOIN tbl_category ON tbl_category.id = ' . $table . '.category_id';
                                $join .= ' LEFT JOIN tbl_subcategory ON tbl_subcategory.id = ' . $table . '.subcategory_id';
                                $where = ' WHERE ' . $table . '.status = 1';
                                $where .= ' AND ' . $table . '.category_id IN ( ' . $user_category . ')';
                                $where .= ' AND ' . $table . '.language_id = ' . $language_id;
                                $where .= " AND (tbl_news.show_till >= '" . $this->toDate . "' OR CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00')";
                                $where .= " AND tbl_news.description != ''";
                                $select = ' ' . $table . '.*, IFNULL(tbl_category.category_name, "") as category_name, IFNULL(tbl_subcategory.subcategory_name, "") as subcategory_name';
                                $orderby = ' tbl_news.id DESC';
                                
                                $table = 'tbl_news';
                            }
                           
                            
                        }   
                         if(isset ($select) && $select != null){
                            // print_r('SELECT ' . $select . ' FROM ' . $table . ' ' . $join . '  ' . $where . ' ORDER BY ' . $orderby . ' LIMIT ' . $offset . ',' . $limit . '');
                            $result = $this->db->query('SELECT ' . $select . ' FROM ' . $table . ' ' . $join . '  ' . $where . ' ORDER BY ' . $orderby . ' LIMIT ' . $offset . ',' . $limit . '')->getResult();
                       
                        if ($result) {
                            for ($j = 0; $j < count($result); $j++) {
                                if (($data[$i]->news_type == 'news') OR ($data[$i]->is_based_on_user_choice == '1')) {
                                    $result[$j]->image = ($result[$j]->image) ? base_url() . '/public/images/news/' . $result[$j]->image : '';
                                    if ($result[$j]->content_type == "video_upload") {
                                        $result[$j]->content_value = ($result[$j]->content_value) ? base_url() . '/public/images/news_video/' . $result[$j]->content_value : '';
                                    }
                                    $img = array();
                                    $img = $this->db->table('tbl_news_image')->select('other_image')->select('id')->where('news_id', $result[$j]->id)->get()->getResult();
                                    for ($k = 0; $k < count($img); $k++) {
                                        $img[$k]->other_image = ($img[$k]->other_image) ? base_url() . '/public/images/news/' . $result[$j]->id . '/' . $img[$k]->other_image : '';
                                        $img[$k]->id = $img[$k]->id;
                                    }
                                    $result[$j]->image_data = $img;

                                    $like = $this->db->table('tbl_news_like')->select('COUNT(id) as total')->where('news_id', $result[$j]->id)->where('status', '1')->get()->getResult();
                                    $result[$j]->total_like = (!empty($like)) ? $like[0]->total : "0";

                                    $dislike = $this->db->table('tbl_news_like')->select('COUNT(id) as total')->where('news_id', $result[$j]->id)->where('status', '2')->get()->getResult();
                                    $result[$j]->total_dislike = (!empty($dislike)) ? $dislike[0]->total : "0";

                                    $bookmark = $this->db->table('tbl_bookmark')->select('COUNT(id) as total')->where('news_id', $data[$i]->id)->get()->getResult();
                                    $result[$j]->total_bookmark = (!empty($bookmark)) ? $bookmark[0]->total : "0";
                                    $ubookmark = $this->db->table('tbl_bookmark')->where('news_id', $data[$i]->id)->where('user_id', $user_id)->get()->getResult();
                                    $result[$j]->bookmark = (!empty($ubookmark)) ? '1' : '0';

                                    $views = $this->db->table('	tbl_news_view')->select('COUNT(id) as total')->where('news_id', $result[$j]->id)->get()->getResult();
                                    $result[$j]->total_views = (!empty($views)) ? $views[0]->total : "0";
                                    if (isset($result[$j]->tag_id) && !empty($result[$j]->tag_id)) {
                                        $query2 = $this->db->query('SELECT GROUP_CONCAT(tag_name) as tag_name, GROUP_CONCAT(id) as tag_id FROM tbl_tag WHERE id IN(' . $result[$j]->tag_id . ')');
                                        $res2 = $query2->getResult();
                                        $result[$j]->tag_name = (!empty($res2)) ? $res2[0]->tag_name : '';
                                        $result[$j]->tag_id = (!empty($res2)) ? $res2[0]->tag_id : $result[$j]->tag_id;
                                    }
                                }
                                elseif($data[$i]->news_type == 'breaking_news' ){
                                    $result[$j]->image = ($result[$j]->image) ? base_url() . '/public/images/breaking_news/' . $result[$j]->image : '';
                                    if ($result[$j]->content_type == "video_upload") {
                                        $result[$j]->content_value = ($result[$j]->content_value) ? base_url() . '/public/images/breaking_news_video/' . $result[$j]->content_value : '';
                                    }
                                     $views = $this->db->table('tbl_breaking_news_view')->select('COUNT(id) as total')->where('breaking_news_id', $result[$j]->id)->get()->getResult();
                                    $result[$j]->total_views = (!empty($views)) ? $views[0]->total : "0";
                                }
                                elseif($data[$i]->news_type == 'videos' ){
                                    if($data[$i]->videos_type == 'news' ){
                                        $result[$j]->image = ($result[$j]->image) ? base_url() . '/public/images/news/' . $result[$j]->image : '';
                                    if ($result[$j]->content_type == "video_upload") {
                                        $result[$j]->content_value = ($result[$j]->content_value) ? base_url() . '/public/images/news_video/' . $result[$j]->content_value : '';
                                    }
                                    $img = array();
                                    $img = $this->db->table('tbl_news_image')->select('other_image')->select('id')->where('news_id', $result[$j]->id)->get()->getResult();
                                    for ($k = 0; $k < count($img); $k++) {
                                        $img[$k]->other_image = ($img[$k]->other_image) ? base_url() . '/public/images/news/' . $result[$j]->id . '/' . $img[$k]->other_image : '';
                                        $img[$k]->id = $img[$k]->id;
                                    }
                                    $result[$j]->image_data = $img;

                                    $like = $this->db->table('tbl_news_like')->select('COUNT(id) as total')->where('news_id', $result[$j]->id)->where('status', '1')->get()->getResult();
                                    $result[$j]->total_like = (!empty($like)) ? $like[0]->total : "0";

                                    $dislike = $this->db->table('tbl_news_like')->select('COUNT(id) as total')->where('news_id', $result[$j]->id)->where('status', '2')->get()->getResult();
                                    $result[$j]->total_dislike = (!empty($dislike)) ? $dislike[0]->total : "0";

                                    $bookmark = $this->db->table('tbl_bookmark')->select('COUNT(id) as total')->where('news_id', $data[$i]->id)->get()->getResult();
                                    $result[$j]->total_bookmark = (!empty($bookmark)) ? $bookmark[0]->total : "0";
                                    $ubookmark = $this->db->table('tbl_bookmark')->where('news_id', $data[$i]->id)->where('user_id', $user_id)->get()->getResult();
                                    $result[$j]->bookmark = (!empty($ubookmark)) ? '1' : '0';

                                    $views = $this->db->table('	tbl_news_view')->select('COUNT(id) as total')->where('news_id', $result[$j]->id)->get()->getResult();
                                    $result[$j]->total_views = (!empty($views)) ? $views[0]->total : "0";
                                    if (isset($result[$j]->tag_id) && !empty($result[$j]->tag_id)) {
                                        $query2 = $this->db->query('SELECT GROUP_CONCAT(tag_name) as tag_name, GROUP_CONCAT(id) as tag_id FROM tbl_tag WHERE id IN(' . $result[$j]->tag_id . ')');
                                        $res2 = $query2->getResult();
                                        $result[$j]->tag_name = (!empty($res2)) ? $res2[0]->tag_name : '';
                                        $result[$j]->tag_id = (!empty($res2)) ? $res2[0]->tag_id : $result[$j]->tag_id;
                                    }
                                    }
                                    elseif($data[$i]->videos_type == 'breaking_news'){
                                        $result[$j]->image = ($result[$j]->image) ? base_url() . '/public/images/breaking_news/' . $result[$j]->image : '';
                                        if ($result[$j]->content_type == "video_upload") {
                                            $result[$j]->content_value = ($result[$j]->content_value) ? base_url() . '/public/images/breaking_news_video/' . $result[$j]->content_value : '';
                                        }
                                        $views = $this->db->table('tbl_breaking_news_view')->select('COUNT(id) as total')->where('breaking_news_id', $result[$j]->id)->get()->getResult();
                                        $result[$j]->total_views = (!empty($views)) ? $views[0]->total : "0";
                                    }
                                }
                            }


                            $result1 = $this->db->query('SELECT ' . $select . ' FROM ' . $table . ' ' . $join . '  ' . $where . ' ORDER BY ' . $orderby . '')->getResult();
                            $total1 = count($result1);
                            //news type  = user_choice -- if based_on_users_choice 
                            $data[$i]->news_type = ($data[$i]->is_based_on_user_choice == '1') ? "user_choice" : $data[$i]->news_type;
                            $content = ($data[$i]->is_based_on_user_choice == '1') ? "news" : $data[$i]->news_type ;
                            $content_total = ($data[$i]->is_based_on_user_choice == '1') ? "news_total" : $data[$i]->news_type . '_total';
                            $data[$i]->$content_total = $total1;
                            $data[$i]->$content = $result;
							// if ad_space above featured section
							$section_id = $data[$i]->id;
							$ad_space = $this->db->table('tbl_ad_spaces')->where('ad_featured_section_id', $section_id)->where('status', 1)->get()->getRow();
							if (!empty($ad_space)) {
                                $ad_space->ad_image = ($ad_space->ad_image) ? base_url() . '/public/images/ad_spaces/' . $ad_space->ad_image : '';
                                $ad_space->web_ad_image = ($ad_space->web_ad_image) ? base_url() . '/public/images/ad_spaces/' . $ad_space->web_ad_image : '';
								$data[$i]->ad_spaces = $ad_space;
							}
                        } 
                    else {
                            $content = $data[$i]->news_type;
                            $content_total = $data[$i]->news_type . '_total';
                            $data[$i]->$content_total = 0;
                            $data[$i]->$content = $result;
                        }
                    }
                    }
                    $response['error'] = "false";
                    $response['total'] = count($data);
                    $response['data'] = $data;
                } else {
                    $response['error'] = "true";
                    $response['message'] = "No Data Found";
                }
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    public function get_featured_section_by_id()
    {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            $section_id = $this->request->getVar('section_id');
            $language_id = $this->request->getVar('language_id');
            $user_id = $this->request->getVar('user_id');
            $offset = ($this->request->getVar('offset')) ? $this->request->getVar('offset') : 0;
            $limit = ($this->request->getVar('limit')) ? $this->request->getVar('limit') : 6;
            $data = $this->db->table('tbl_featured_sections')->where('id', $section_id)->get()->getResult();
          
            for ($i = 0; $i < count($data); $i++) {
                 // 1. news type == news or video  (2. breaking_news mate code niche chhe) //
                if ($data[$i]->news_type == 'news' || $data[$i]->news_type == 'videos') {
                    $table = 'tbl_news';
                    if ($data[$i]->filter_type == 'most_commented') {
                        if ($data[$i]->news_type == 'news') {
                            // 1.1 most_commented news, 
                            $join = ' LEFT JOIN tbl_category ON tbl_category.id = ' . $table . '.category_id';
                            $join .= ' LEFT JOIN tbl_subcategory ON tbl_subcategory.id = ' . $table . '.subcategory_id';
                            $join .= ' INNER JOIN ( SELECT news_id, count(*) AS newscount FROM tbl_comment GROUP BY news_id ) as tbl_comment on tbl_news.id = tbl_comment.news_id';
                            $where = ' WHERE ' . $table . '.status = 1';
                            $where .= ' AND ' . $table . '.language_id = ' . $language_id;
                            $where .= " AND (tbl_news.show_till >= '" . $this->toDate . "' OR CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00')";
                            $where .= " AND tbl_news.description != ''";
                            if ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids != NULL) {
                                $where .= ' AND (' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ') OR ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . '))';
                            } elseif ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids == NULL) {
                                $where .= ' AND ' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ')';
                            } elseif ($data[$i]->category_ids == NULL && $data[$i]->subcategory_ids != NULL) {
                                $where .= ' AND ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . ')';
                            } elseif ($data[$i]->category_ids == NULL && $data[$i]->subcategory_ids == NULL) {
                                $where .= ' ';
                            }
                            $select = ' ' . $table . '.*, IFNULL(tbl_category.category_name, "") as category_name, IFNULL(tbl_subcategory.subcategory_name, "") as subcategory_name, tbl_comment.newscount';
                            
                            $orderby = ' tbl_comment.newscount DESC';
                        } elseif ($data[$i]->news_type == 'videos') {
                            if ($data[$i]->videos_type == 'news') {
                                //1.2 most commented news videos (comment feature available only in news)
                                $join = ' LEFT JOIN tbl_category ON tbl_category.id = ' . $table . '.category_id';
                                $join .= ' LEFT JOIN tbl_subcategory ON tbl_subcategory.id = ' . $table . '.subcategory_id';
                                $join .= ' INNER JOIN ( SELECT news_id, count(*) AS newscount FROM tbl_comment GROUP BY news_id ) as tbl_comment on tbl_news.id = tbl_comment.news_id';
                                $where = ' WHERE ' . $table . '.content_type IN ( "video_upload","video_youtube","video_other")';
                                $where .= ' AND ' . $table . '.status = 1';
                                $where .= ' AND ' . $table . '.language_id = ' . $language_id;
                                $where .= " AND (tbl_news.show_till >= '" . $this->toDate . "' OR CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00')";
                                if ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids != NULL) {
                                    $where .= ' AND (' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ') OR ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . '))';
                                } elseif ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids == NULL) {
                                    $where .= ' AND ' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ')';
                                } elseif ($data[$i]->category_ids == NULL && $data[$i]->subcategory_ids != NULL) {
                                    $where .= ' AND ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . ')';
                                } elseif ($data[$i]->category_ids == NULL && $data[$i]->subcategory_ids == NULL) {
                                    $where .= ' ';
                                }
                                $select = ' ' . $table . '.*, IFNULL(tbl_category.category_name, "") as category_name, IFNULL(tbl_subcategory.subcategory_name, "") as subcategory_name , tbl_comment.newscount';
                                $orderby = ' tbl_comment.newscount DESC';
                            }
                        }
                    } elseif ($data[$i]->filter_type == 'recently_added') {
                        if ($data[$i]->news_type == 'news') {
                            //1.3 recently_added news
                            $join = ' LEFT JOIN tbl_category ON tbl_category.id = ' . $table . '.category_id';
                            $join .= ' LEFT JOIN tbl_subcategory ON tbl_subcategory.id = ' . $table . '.subcategory_id';
                            $where = ' WHERE ' . $table . '.status = 1';
                            $where .= ' AND ' . $table . '.language_id = ' . $language_id;
                            $where .= " AND (tbl_news.show_till >= '" . $this->toDate . "' OR CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00')";
                            $where .= " AND tbl_news.description != ''";
                            if ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids != NULL) {
                                $where .= ' AND (' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ') OR ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . '))';
                            } elseif ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids == NULL) {
                                $where .= ' AND ' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ')';
                            } elseif ($data[$i]->category_ids == NULL && $data[$i]->subcategory_ids != NULL) {
                                $where .= ' AND ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . ')';
                            } elseif ($data[$i]->category_ids == NULL && $data[$i]->subcategory_ids == NULL) {
                                $where .= ' ';
                            }
                            $select = ' ' . $table . '.*, IFNULL(tbl_category.category_name, "") as category_name, IFNULL(tbl_subcategory.subcategory_name, "") as subcategory_namee';
                            $orderby = ' ' . $table . '.id DESC';
                        } elseif ($data[$i]->news_type == 'videos') {
                            if ($data[$i]->videos_type == 'news') { 
                                //1.4 recently_added news video
                                $join = ' LEFT JOIN tbl_category ON tbl_category.id = ' . $table . '.category_id';
                                $join .= ' LEFT JOIN tbl_subcategory ON tbl_subcategory.id = ' . $table . '.subcategory_id';
                                $where = ' WHERE ' . $table . '.content_type IN ( "video_upload","video_youtube","video_other")';
                                $where .= ' AND ' . $table . '.language_id = ' . $language_id;
                                $where .= " AND (tbl_news.show_till >= '" . $this->toDate . "' OR CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00')";
                                if ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids != NULL) {
                                    $where .= ' AND (' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ') OR ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . '))';
                                } elseif ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids == NULL) {
                                    $where .= ' AND ' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ')';
                                } elseif ($data[$i]->category_ids == NULL && $data[$i]->subcategory_ids != NULL) {
                                    $where .= ' AND ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . ')';
                                } elseif ($data[$i]->category_ids == NULL && $data[$i]->subcategory_ids == NULL) {
                                    $where .= ' ';
                                }
                                $select = ' ' . $table . '.*, IFNULL(tbl_category.category_name, "") as category_name, IFNULL(tbl_subcategory.subcategory_name, "") as subcategory_name';
                                $orderby = ' ' . $table . '.id DESC';
                            } elseif ($data[$i]->videos_type == 'breaking_news') {
                                //1.5 recently_added breaking_news video
                                $join = '';
                                $table = 'tbl_breaking_news';
                                $where = ' WHERE tbl_breaking_news.content_type IN ( "video_upload","video_youtube","video_other")';
                                $where .= ' AND tbl_breaking_news.language_id = ' . $language_id;
                                $select = ' tbl_breaking_news.* ';
                                $orderby = ' tbl_breaking_news.id DESC';
                            }
                        }
                    } elseif ($data[$i]->filter_type == 'most_viewed') {
                        if ($data[$i]->news_type == 'news') {
                            //1.6 most_viewed news 
                            $join = ' LEFT JOIN tbl_category ON tbl_category.id = ' . $table . '.category_id';
                            $join .= ' LEFT JOIN tbl_subcategory ON tbl_subcategory.id = ' . $table . '.subcategory_id';
                            $join .= ' INNER JOIN ( SELECT news_id, count(*) AS viewcount FROM tbl_news_view GROUP BY news_id ) as tbl_news_view on tbl_news.id = tbl_news_view.news_id';
                            $where = ' WHERE ' . $table . '.status = 1';
                            $where .= ' AND ' . $table . '.language_id = ' . $language_id;
                            $where .= " AND (tbl_news.show_till >= '" . $this->toDate . "' OR CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00')";
                            $where .= " AND tbl_news.description != ''";
                            if ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids != NULL) {
                                $where .= ' AND (' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ') OR ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . '))';
                            } elseif ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids == NULL) {
                                $where .= ' AND ' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ')';
                            } elseif ($data[$i]->category_ids == NULL && $data[$i]->subcategory_ids != NULL) {
                                $where .= ' AND ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . ')';
                            }
                            $select = ' ' . $table . '.*, IFNULL(tbl_category.category_name, "") as category_name, IFNULL(tbl_subcategory.subcategory_name, "") as subcategory_name, tbl_news_view.viewcount';
                            $orderby = ' tbl_news_view.viewcount DESC';
                        } elseif ($data[$i]->news_type == 'videos') {
                            if ($data[$i]->videos_type == 'news') {
                                //1.7 most_viewed news video
                                $join = ' INNER JOIN ( SELECT news_id, count(*) AS viewcount FROM tbl_news_view GROUP BY news_id ) as tbl_news_view on tbl_news.id = tbl_news_view.news_id';
                                $where = ' WHERE ' . $table . '.content_type IN ( "video_upload","video_youtube","video_other")';
                                $where .= ' AND ' . $table . '.language_id = ' . $language_id;
                                $where .= " AND (tbl_news.show_till >= '" . $this->toDate . "' OR CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00')";
                                if ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids != NULL) {
                                    $where .= ' AND (' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ') OR ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . '))';
                                } elseif ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids == NULL) {
                                    $where .= ' AND ' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ')';
                                } elseif ($data[$i]->category_ids == NULL && $data[$i]->subcategory_ids != NULL) {
                                    $where .= ' AND ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . ')';
                                } elseif ($data[$i]->category_ids == NULL && $data[$i]->subcategory_ids == NULL) {
                                    $where .= ' ';
                                }
                                $select = ' ' . $table . '.*, IFNULL(tbl_category.category_name, "") as category_name, IFNULL(tbl_subcategory.subcategory_name, "") as subcategory_name, tbl_news_view.viewcount';
                                $orderby = ' tbl_news_view.viewcount DESC';
                            } elseif ($data[$i]->videos_type == 'breaking_news') {
                                //1.8 most_viewed breaking_news video
                                $table = 'tbl_breaking_news';
                                $join = ' INNER JOIN ( SELECT breaking_news_id, count(*) AS viewcount FROM tbl_breaking_news_view GROUP BY breaking_news_id ) as tbl_breaking_news_view on tbl_breaking_news.id = tbl_breaking_news_view.breaking_news_id';
                                $where = 'WHERE tbl_breaking_news.content_type IN ( "video_upload","video_youtube","video_other")';
                                $where .= ' AND tbl_breaking_news.language_id = ' . $language_id;
                                $select = ' tbl_breaking_news.*, tbl_breaking_news_view.viewcount';
                                $orderby = ' tbl_breaking_news_view.viewcount DESC';
                            }
                        }
                    } elseif ($data[$i]->filter_type == 'most_favorite') {
                        //1.9 most_favorite news, video
                        $join = ' LEFT JOIN tbl_category ON tbl_category.id = ' . $table . '.category_id';
                        $join .= ' LEFT JOIN tbl_subcategory ON tbl_subcategory.id = ' . $table . '.subcategory_id';
                        $join .= ' INNER JOIN ( SELECT news_id, count(*) AS newscount FROM tbl_bookmark GROUP BY news_id ) as tbl_bookmark on tbl_news.id = tbl_bookmark.news_id';
                        $where = ' WHERE ' . $table . '.status = 1';
                        $where .= ' AND ' . $table . '.language_id = ' . $language_id;
                        $where .= " AND (tbl_news.show_till >= '" . $this->toDate . "' OR CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00')";
                        $where .= " AND tbl_news.description != ''";
                        if ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids != NULL) {
                            $where .= ' AND (' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ') OR ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . '))';
                        } elseif ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids == NULL) {
                            $where .= ' AND ' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ')';
                        } elseif ($data[$i]->category_ids == NULL && $data[$i]->subcategory_ids != NULL) {
                            $where .= ' AND ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . ')';
                        }
                        if ($data[$i]->news_type == 'videos') {
                            $where .= ' AND ' . $table . '.content_type IN ( "video_upload","video_youtube","video_other")';
                        }
                        $select = ' ' . $table . '.*, IFNULL(tbl_category.category_name, "") as category_name, IFNULL(tbl_subcategory.subcategory_name, "") as subcategory_name, tbl_bookmark.newscount';
                        $orderby = ' tbl_bookmark.newscount DESC';
                    } elseif ($data[$i]->filter_type == 'most_like') {
                        //1.9 most_favorite like, video
                        $join = ' LEFT JOIN tbl_category ON tbl_category.id = ' . $table . '.category_id';
                        $join .= ' LEFT JOIN tbl_subcategory ON tbl_subcategory.id = ' . $table . '.subcategory_id';
                        $join .= ' INNER JOIN ( SELECT news_id, count(*) AS likecount FROM tbl_news_like WHERE status="1" GROUP BY news_id ) as tbl_news_like on tbl_news.id = tbl_news_like.news_id';
                        $where = ' WHERE ' . $table . '.status = 1';
                        $where .= ' AND ' . $table . '.language_id = ' . $language_id;
                        $where .= " AND (tbl_news.show_till >= '" . $this->toDate . "' OR CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00')";
                        $where .= " AND tbl_news.description != ''";
                        if ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids != NULL) {
                            $where .= ' AND (' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ') OR ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . '))';
                        } elseif ($data[$i]->category_ids != NULL && $data[$i]->subcategory_ids == NULL) {
                            $where .= ' AND ' . $table . '.category_id IN ( ' . $data[$i]->category_ids . ')';
                        } elseif ($data[$i]->category_ids == NULL && $data[$i]->subcategory_ids != NULL) {
                            $where .= ' AND ' . $table . '.subcategory_id IN ( ' . $data[$i]->subcategory_ids . ')';
                        }
                        if ($data[$i]->news_type == 'videos') {
                            $where .= ' AND ' . $table . '.content_type IN ( "video_upload","video_youtube","video_other")';
                        }
                        $select = ' ' . $table . '.*, IFNULL(tbl_category.category_name, "") as category_name, IFNULL(tbl_subcategory.subcategory_name, "") as subcategory_name, tbl_news_like.likecount';
                        $orderby = ' tbl_news_like.likecount DESC';
                    } elseif ($data[$i]->filter_type == 'custom') {
                        //1.10 custom (based on selected category, subcategory)
                        if ($data[$i]->news_type == 'news') {
                            $join = ' LEFT JOIN tbl_category ON tbl_category.id = ' . $table . '.category_id';
                            $join .= ' LEFT JOIN tbl_subcategory ON tbl_subcategory.id = ' . $table . '.subcategory_id';
                            $where = ' WHERE ' . $table . '.status = 1';
                            $where .= ' AND ' . $table . '.id IN ( ' . $data[$i]->news_ids . ')  ';
                            $where .= " AND (tbl_news.show_till >= '" . $this->toDate . "' OR CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00')";
                            $where .= " AND tbl_news.description != ''";
                            $select = ' ' . $table . '.*, IFNULL(tbl_category.category_name, "") as category_name, IFNULL(tbl_subcategory.subcategory_name, "") as subcategory_name';
                            $orderby = ' tbl_news.id DESC';
                            
                        } elseif ($data[$i]->news_type == 'videos') {
                            if ($data[$i]->videos_type == 'news') {
                                $join = ' LEFT JOIN tbl_category ON tbl_category.id = ' . $table . '.category_id';
                                $join .= ' LEFT JOIN tbl_subcategory ON tbl_subcategory.id = ' . $table . '.subcategory_id';
                                $where = ' WHERE ' . $table . '.status = 1';
                                $where .= ' AND ' . $table . '.content_type IN ( "video_upload","video_youtube","video_other")';
                                $where .= ' AND ' . $table . '.id IN ( ' . $data[$i]->news_ids . ')  ';
                                $where .= ' AND ' . $table . '.language_id = ' . $language_id;
                                $where .= " AND (tbl_news.show_till >= '" . $this->toDate . "' OR CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00')";
                                $select = ' ' . $table . '.*, IFNULL(tbl_category.category_name, "") as category_name, IFNULL(tbl_subcategory.subcategory_name, "") as subcategory_name';
                                $orderby = ' tbl_news.id DESC';
                            } elseif ($data[$i]->videos_type == 'breaking_news') {
                                $table = 'tbl_breaking_news';
                                $join = ' ';
                                $where = 'WHERE tbl_breaking_news.content_type IN ( "video_upload","video_youtube","video_other")';
                                $where .= ' AND tbl_breaking_news.language_id = ' . $language_id;
                                $select = ' tbl_breaking_news.*';
                                $orderby = ' tbl_breaking_news.id DESC';
                            }
                        }
                    }
                } elseif ($data[$i]->news_type == 'breaking_news') {
                    //2. Breaking News
                    $table = 'tbl_breaking_news';
                    if ($data[$i]->filter_type == 'recently_added') {
                        //2.1 Breaking News recently_added
                        $join = '';
                        $where = 'WHERE ' . $table . '.language_id = ' . $language_id;
                        $select = ' ' . $table . '.*';
                        $orderby = ' ' . $table . '.id DESC';
                    } elseif ($data[$i]->filter_type == 'most_viewed') {
                        //2.1 Breaking News most_viewed
                        $join = ' INNER JOIN ( SELECT breaking_news_id, count(*) AS viewcount FROM tbl_breaking_news_view GROUP BY breaking_news_id ) as tbl_breaking_news_view on tbl_breaking_news.id = tbl_breaking_news_view.breaking_news_id';
                        $where = 'WHERE ' . $table . '.language_id = ' . $language_id;
                        $select = ' ' . $table . '.*, tbl_breaking_news_view.viewcount';
                        $orderby = ' tbl_breaking_news_view.viewcount DESC';
                    } elseif ($data[$i]->filter_type == 'custom') {
                        //2.1 Breaking News custom(based on selected category, subcategory)
                        $join = ' ';
                        $where = ' WHERE ' . $table . '.id IN ( ' . $data[$i]->news_ids . ')  ';
                        $where .= ' AND ' . $table . '.language_id = ' . $language_id;
                        $select = ' ' . $table . '.*';
                        $orderby = ' ' . $table . '.id DESC';
                    }
                } elseif($data[$i]->is_based_on_user_choice == '1'){
                    // based_on_user's_choice_section code ** different from above all section //
                    $table = 'tbl_news';
                    $user_category = $this->db->table('tbl_users_category')->select('category_id')->where('user_id', $user_id)->get()->getRow();
                    if($user_category != null){
                        $table = 'tbl_news';
                        $user_category = $this->db->table('tbl_users_category')->select('category_id')->where('user_id', $user_id)->get()->getRow()->category_id;;  
                        $join = ' LEFT JOIN tbl_category ON tbl_category.id = ' . $table . '.category_id';
                        $join .= ' LEFT JOIN tbl_subcategory ON tbl_subcategory.id = ' . $table . '.subcategory_id';
                        $where = ' WHERE ' . $table . '.status = 1';
                        $where .= ' AND ' . $table . '.category_id IN ( ' . $user_category . ')';
                        $where .= ' AND ' . $table . '.language_id = ' . $language_id;
                        $where .= " AND (tbl_news.show_till >= '" . $this->toDate . "' OR CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00')";
                        $where .= " AND tbl_news.description != ''";
                        $select = ' ' . $table . '.*, IFNULL(tbl_category.category_name, "") as category_name, IFNULL(tbl_subcategory.subcategory_name, "") as subcategory_name';
                        $orderby = ' tbl_news.id DESC';
                        
                        $table = 'tbl_news';
                    }
                }
                if(isset ($select) && $select != null){
                    $result = $this->db->query('SELECT ' . $select . ' FROM ' . $table . ' ' . $join . '  ' . $where . ' ORDER BY ' . $orderby)->getResult();
                   
                    if ($result) {
                    for ($j = 0; $j < count($result); $j++) {
                        if (($data[$i]->news_type == 'news') || ($data[$i]->is_based_on_user_choice == '1')) {
                            $result[$j]->image = ($result[$j]->image) ? base_url() . '/public/images/news/' . $result[$j]->image : '';
                            if ($result[$j]->content_type == "video_upload") {
                                $result[$j]->content_value = ($result[$j]->content_value) ? base_url() . '/public/images/news_video/' . $result[$j]->content_value : '';
                            }
                            $img = array();
                            $img = $this->db->table('tbl_news_image')->select('other_image')->select('id')->where('news_id', $result[$j]->id)->get()->getResult();
                            for ($k = 0; $k < count($img); $k++) {
                                $img[$k]->other_image = ($img[$k]->other_image) ? base_url() . '/public/images/news/' . $result[$j]->id . '/' . $img[$k]->other_image : '';
                                $img[$k]->id = $img[$k]->id;
                            }
                            $result[$j]->image_data = $img;

                            $like = $this->db->table('tbl_news_like')->select('COUNT(id) as total')->where('news_id', $result[$j]->id)->where('status', '1')->get()->getResult();
                            $result[$j]->total_like = (!empty($like)) ? $like[0]->total : "0";

                            $dislike = $this->db->table('tbl_news_like')->select('COUNT(id) as total')->where('news_id', $result[$j]->id)->where('status', '2')->get()->getResult();
                            $result[$j]->total_dislike = (!empty($dislike)) ? $dislike[0]->total : "0";

                            $bookmark = $this->db->table('tbl_bookmark')->select('COUNT(id) as total')->where('news_id', $data[$i]->id)->get()->getResult();
                            $result[$j]->total_bookmark = (!empty($bookmark)) ? $bookmark[0]->total : "0";
                            $ubookmark = $this->db->table('tbl_bookmark')->where('news_id', $data[$i]->id)->where('user_id', $user_id)->get()->getResult();
                            $result[$j]->bookmark = (!empty($ubookmark)) ? '1' : '0';

                            $views = $this->db->table('	tbl_news_view')->select('COUNT(id) as total')->where('news_id', $result[$j]->id)->get()->getResult();
                            $result[$j]->total_views = (!empty($views)) ? $views[0]->total : "0";
                            if (isset($result[$j]->tag_id) && !empty($result[$j]->tag_id)) {
                                $query2 = $this->db->query('SELECT GROUP_CONCAT(tag_name) as tag_name, GROUP_CONCAT(id) as tag_id FROM tbl_tag WHERE id IN(' . $result[$j]->tag_id . ')');
                                $res2 = $query2->getResult();
                                $result[$j]->tag_name = (!empty($res2)) ? $res2[0]->tag_name : '';
                                $result[$j]->tag_id = (!empty($res2)) ? $res2[0]->tag_id : $result[$j]->tag_id;
                            }
                        }
                        elseif($data[$i]->news_type == 'breaking_news'){
                            $result[$j]->image = ($result[$j]->image) ? base_url() . '/public/images/breaking_news/' . $result[$j]->image : '';
                            if ($result[$j]->content_type == "video_upload") {
                                $result[$j]->content_value = ($result[$j]->content_value) ? base_url() . '/public/images/breaking_news_video/' . $result[$j]->content_value : '';
                            } 
                        }
                        elseif($data[$i]->news_type == 'videos' ){
                            if($data[$i]->videos_type == 'news' ){
                                $result[$j]->image = ($result[$j]->image) ? base_url() . '/public/images/news/' . $result[$j]->image : '';
                            if ($result[$j]->content_type == "video_upload") {
                                $result[$j]->content_value = ($result[$j]->content_value) ? base_url() . '/public/images/news_video/' . $result[$j]->content_value : '';
                            }
                            $img = array();
                            $img = $this->db->table('tbl_news_image')->select('other_image')->select('id')->where('news_id', $result[$j]->id)->get()->getResult();
                            for ($k = 0; $k < count($img); $k++) {
                                $img[$k]->other_image = ($img[$k]->other_image) ? base_url() . '/public/images/news/' . $result[$j]->id . '/' . $img[$k]->other_image : '';
                                $img[$k]->id = $img[$k]->id;
                            }
                            $result[$j]->image_data = $img;

                            $like = $this->db->table('tbl_news_like')->select('COUNT(id) as total')->where('news_id', $result[$j]->id)->where('status', '1')->get()->getResult();
                            $result[$j]->total_like = (!empty($like)) ? $like[0]->total : "0";

                            $dislike = $this->db->table('tbl_news_like')->select('COUNT(id) as total')->where('news_id', $result[$j]->id)->where('status', '2')->get()->getResult();
                            $result[$j]->total_dislike = (!empty($dislike)) ? $dislike[0]->total : "0";

                            $bookmark = $this->db->table('tbl_bookmark')->select('COUNT(id) as total')->where('news_id', $data[$i]->id)->get()->getResult();
                            $result[$j]->total_bookmark = (!empty($bookmark)) ? $bookmark[0]->total : "0";
                            $ubookmark = $this->db->table('tbl_bookmark')->where('news_id', $data[$i]->id)->where('user_id', $user_id)->get()->getResult();
                            $result[$j]->bookmark = (!empty($ubookmark)) ? '1' : '0';

                            $views = $this->db->table('	tbl_news_view')->select('COUNT(id) as total')->where('news_id', $result[$j]->id)->get()->getResult();
                            $result[$j]->total_views = (!empty($views)) ? $views[0]->total : "0";
                            if (isset($result[$j]->tag_id) && !empty($result[$j]->tag_id)) {
                                $query2 = $this->db->query('SELECT GROUP_CONCAT(tag_name) as tag_name, GROUP_CONCAT(id) as tag_id FROM tbl_tag WHERE id IN(' . $result[$j]->tag_id . ')');
                                $res2 = $query2->getResult();
                                $result[$j]->tag_name = (!empty($res2)) ? $res2[0]->tag_name : '';
                                $result[$j]->tag_id = (!empty($res2)) ? $res2[0]->tag_id : $result[$j]->tag_id;
                            }
                            }
                            elseif($data[$i]->videos_type == 'breaking_news'){
                                $result[$j]->image = ($result[$j]->image) ? base_url() . '/public/images/breaking_news/' . $result[$j]->image : '';
                                if ($result[$j]->content_type == "video_upload") {
                                    $result[$j]->content_value = ($result[$j]->content_value) ? base_url() . '/public/images/breaking_news_video/' . $result[$j]->content_value : '';
                                }
                                $views = $this->db->table('tbl_breaking_news_view')->select('COUNT(id) as total')->where('breaking_news_id', $result[$j]->id)->get()->getResult();
                                $result[$j]->total_views = (!empty($views)) ? $views[0]->total : "0";
                            }
                        }
                    }


                    $result1 = $this->db->query('SELECT ' . $select . ' FROM ' . $table . ' ' . $join . '  ' . $where . ' ORDER BY ' . $orderby . '')->getResult();
                    
                    $total1 = count($result1);
                    //news type  = user_choice -- if based_on_users_choice 
                    $data[$i]->news_type = ($data[$i]->is_based_on_user_choice == '1') ? "user_choice" : $data[$i]->news_type;
                    $content = ($data[$i]->is_based_on_user_choice == '1') ? "news" : $data[$i]->news_type ;
                    $content_total = ($data[$i]->is_based_on_user_choice == '1') ? "news_total" : $data[$i]->news_type . '_total';
                    $data[$i]->$content_total = $total1;
                    $data[$i]->$content = $result;


                } else {
                    $content = $data[$i]->news_type;
                    $content_total = $data[$i]->news_type . '_total';
                    $data[$i]->$content_total = 0;
                    $data[$i]->$content = $result;
                }
            }
            }
  
            if (!empty($data)) {
                $response['error'] = "false";
                $response['data'] = $data;
                
            } else {
                $response['error'] = "true";
                $response['message'] = "No data found!";
            }
            return $this->respond($response);
        }
    }
    public function get_like() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('user_id') && $this->request->getVar('language_id')) {
                $user_id = $this->request->getVar('user_id');
                $language_id = $this->request->getVar('language_id');
                $offset = ($this->request->getVar('offset')) ? $this->request->getVar('offset') : 0;
                $limit = ($this->request->getVar('limit')) ? $this->request->getVar('limit') : 10;
                $res = $this->db->table('tbl_news_like l');
                $res->select('l.*,n.category_id,c.category_name,n.title,n.date,n.tag_id,n.content_type,n.content_value,n.image,n.description');
                $res->join('tbl_news n', 'n.id = l.news_id');
                $res->join('tbl_category c', 'c.id = n.category_id');
                $res->where('l.user_id', $user_id)->where('l.status', '1')->where('n.language_id', $language_id)->limit($limit, $offset)->orderBy('id', 'DESC');
                $data = $res->get()->getResult();
                if ($data) {
                    $res = $this->db->table('tbl_news_like l');
                    $res->select('l.*,n.category_id,c.category_name,n.title,n.date,n.content_type,n.content_value,n.image,n.description');
                    $res->join('tbl_news n', 'n.id = l.news_id');
                    $res->join('tbl_category c', 'c.id = n.category_id');
                    $res->where('l.user_id', $user_id)->where('l.status', '1')->where('n.language_id', $language_id);
                    $data1 = $res->get()->getResult();
                    $total = count($data1);
                    for ($i = 0; $i < count($data); $i++) {
                        $data[$i]->image = ($data[$i]->image) ? base_url() . '/public/images/news/' . $data[$i]->image : '';
                        if ($data[$i]->content_type == "video_upload") {
                            $data[$i]->content_value = ($data[$i]->content_value) ? base_url() . '/public/images/news_video/' . $data[$i]->content_value : '';
                        }
                        $img = array();
                        $img = $this->db->table('tbl_news_image')->select('other_image')->where('news_id', $data[$i]->news_id)->get()->getResult();
                        for ($j = 0; $j < count($img); $j++) {
                            $img[$j]->other_image = ($img[$j]->other_image) ? base_url() . '/public/images/news/' . $data[$i]->news_id . '/' . $img[$j]->other_image : '';
                        }
                        $data[$i]->image_data = $img;
                        $like = $this->db->table('tbl_news_like')->select('COUNT(id) as total')->where('news_id', $data[$i]->news_id)->where('status', '1')->get()->getResult();
                        $data[$i]->total_like = (!empty($like)) ? $like[0]->total : "0";
                        $dislike = $this->db->table('tbl_news_like')->select('COUNT(id) as total')->where('news_id', $data[$i]->news_id)->where('status', '2')->get()->getResult();
                        $data[$i]->total_dislike = (!empty($dislike)) ? $dislike[0]->total : "0";
                        $ulike = $this->db->table('tbl_news_like')->where('news_id', $data[$i]->news_id)->where('user_id', $user_id)->where('status', '1')->get()->getResult();
                        $data[$i]->like = (!empty($ulike)) ? '1' : '0';
                        $udislike = $this->db->table('tbl_news_like')->where('news_id', $data[$i]->news_id)->where('user_id', $user_id)->where('status', '2')->get()->getResult();
                        $data[$i]->dislike = (!empty($udislike)) ? '1' : '0';
                        if(isset($data[$i]->tag_id) && !empty($data[$i]->tag_id)){
                            $query2 = $this->db->query('SELECT GROUP_CONCAT(distinct(tag_name)) as tag_name FROM tbl_tag WHERE id IN(' . $data[$i]->tag_id . ')');
                            $res2 = $query2->getResult();
                            $data[$i]->tag_name = (!empty($res2)) ? $res2[0]->tag_name : '';
                        }
                    }
                    $response['error'] = "false";
                    $response['total'] = "$total";
                    $response['data'] = $data;
                } else {
                    $response['error'] = "true";
                    $response['message'] = "No Data Found";
                }
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
    
    public function get_breaking_news_by_id() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            $today =$this->toDate;
            if ($this->request->getVar('breaking_news_id') && $this->request->getVar('user_id') != '' && $this->request->getVar('language_id')) {
                $breaking_news_id = $this->request->getVar('breaking_news_id');
                $user_id = $this->request->getVar('user_id');
                $res = $this->db->table('tbl_breaking_news');
                $res->select('tbl_breaking_news.*');
               
                $res->where('tbl_breaking_news.language_id', $this->request->getVar('language_id'));
                $res->where('tbl_breaking_news.id', $breaking_news_id);
               
                $data = $res->get()->getResult();
                if ($data) {
                    for ($i = 0; $i < count($data); $i++) {
                        $data[$i]->image = ($data[$i]->image) ? base_url() . '/public/images/breaking_news/' . $data[$i]->image : '';
                        if ($data[$i]->content_type == "video_upload") {
                            $data[$i]->content_value = ($data[$i]->content_value) ? base_url() . '/public/images/breaking_news_video/' . $data[$i]->content_value : '';
                        }
                        $views = $this->db->table('	tbl_breaking_news_view')->select('COUNT(id) as total')->where('breaking_news_id', $data[$i]->id)->get()->getResult();
                        $data[$i]->total_views = (!empty($views)) ? $views[0]->total : "0";
                    }
                    $response['error'] = "false";
                    $response['data'] = $data;
                } else {
                    $response['error'] = "true";
                    $response['message'] = "No Data Found";
                }
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }

    public function generate_token_api() {
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else{
        $payload = [
            'iat' => time(), /* issued at time */
            'iss' => 'WRTEAM',
            'exp' => time() + (30 * 60 * 60 * 24), /* expires after 1 minute */
            'sub' => 'WRTEAM Authentication'
        ];
        $token = JWT::encode($payload, $this->JWT_KEY, 'HS256');
        if($token) {
            $response['error'] = "false";
            $response['data'] = $token;
        } else {
            $response['error'] = "true";
            $response['message'] = "Payload mismatch.";
        }
        return $this->respond($response);
        }
    }

    public function get_web_settings() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            $data = $this->db->table('tbl_web_settings')->get()->getResult();
            if ($data) {
                for ($i = 0; $i < count($data); $i++) {
                    $data1[$data[$i]->type] = $data[$i]->message;
                    if($data[$i]->type == 'web_header_logo'){$data1['web_header_logo'] = base_url() . '/public/images/' . $data[$i]->message; }
                    if($data[$i]->type == 'web_footer_logo'){$data1['web_footer_logo'] = base_url() . '/public/images/' . $data[$i]->message; }
                    if($data[$i]->type == 'favicon_icon'){$data1['favicon_icon'] = base_url() . '/public/images/' . $data[$i]->message; }
                }
                $response['error'] = "false";
                $response['data'] = $data1;
            } else {
                $response['error'] = "true";
                $response['message'] = "No data found!";
            }
        }
        return $this->respond($response);
    }

    public function get_ad_space_news_details() {
        if ($this->verify_token()) {
            return $this->verify_token();
        }
        if ($this->access_key != $this->request->getVar('access_key')) {
            $response['error'] = "true";
            $response['message'] = "Invalid Access Key";
        } else {
            if ($this->request->getVar('language_id')) {
                
                 // Ads in news details  - Top section
                 $language_id = $this->request->getVar('language_id');      
                 $ad_space = $this->db->table('tbl_ad_spaces')->where('ad_space', 'news_details-top')->where('status', 1)->where('language_id', $language_id)->get()->getRow();
                 if (!empty($ad_space)) {
                    $ad_space->ad_image = ($ad_space->ad_image) ? base_url() . '/public/images/ad_spaces/' . $ad_space->ad_image : '';
                    $ad_space->web_ad_image = ($ad_space->web_ad_image) ? base_url() . '/public/images/ad_spaces/' . $ad_space->web_ad_image : '';
                    $ad_space->position = 'top';
                    $data['ad_spaces_top'] = $ad_space;
                 }
                 // Ads in news details  - Bottom section
                 $ad_space = $this->db->table('tbl_ad_spaces')->where('ad_space', 'news_details-bottom')->where('status', 1)->where('language_id', $language_id)->get()->getRow();
                 if (!empty($ad_space)) {
                    $ad_space->ad_image = ($ad_space->ad_image) ? base_url() . '/public/images/ad_spaces/' . $ad_space->ad_image : '';
                    $ad_space->web_ad_image = ($ad_space->web_ad_image) ? base_url() . '/public/images/ad_spaces/' . $ad_space->web_ad_image : '';
                    $ad_space->position = 'bottom';
                    $data['ad_spaces_bottom'] = $ad_space;
                 }
                    $response['error'] = "false";
                    $response['data'] = $data;
                if(empty($data)){
                    $response['error'] = "true";
                    unset($response['data']);
                    $response['message'] = "No data found";
                }
                
            } else {
                $response['error'] = "true";
                $response['message'] = "Please fill all the data and submit!";
            }
        }
        return $this->respond($response);
    }
}

