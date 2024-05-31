<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Table extends ResourceController
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        $this->db = \Config\Database::connect();
        $this->helpers = helper('News');
        $this->helpers = helper('SystemSettings_helper');
    }
    public function tag()
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';
        $where = '';
        if ($this->request->getVar('offset'))
            $offset = $this->request->getVar('offset');
        if ($this->request->getVar('limit'))
            $limit = $this->request->getVar('limit');
        if ($this->request->getVar('sort'))
            $sort = $this->request->getVar('sort');
        if ($this->request->getVar('order'))
            $order = $this->request->getVar('order');
        if ($this->request->getVar('search')) {
            $search = $this->request->getVar('search');
            $where = "where (t.id like '%" . $search . "%' OR t.tag_name like '%" . $search . "%')";
        }
        $join = ' JOIN tbl_languages l ON  l.id = t.language_id';
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_tag t $join $where ");
        $res = $query->getResult();
        foreach ($res as $row1) {
            $total = $row1->total;
        }
        $query1 = $this->db->query("SELECT t.*, l.language  FROM tbl_tag t $join $where ORDER BY $sort $order LIMIT $offset , $limit");
        $res1 = $query1->getResult();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $count = 1;
        foreach ($res1 as $row) {
            $operate = '<a class="btn btn-icon btn-sm btn-primary text-white edit-data" data-id=' . $row->id . ' data-toggle="modal" data-target="#editDataModal" title="Edit"><em class="fa fa-edit"></em></a>&nbsp;&nbsp;';
            $operate .= '<a class="btn btn-icon btn-sm btn-danger text-white delete-data" data-id=' . $row->id . ' title="Delete"><em class="fa fa-trash"></em></a>';
            $tempRow['id'] = $row->id;
            $tempRow['tag_name'] = $row->tag_name;
            $tempRow['language'] = $row->language;
            $tempRow['language_id'] = $row->language_id;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
            $count++;
        }
        $bulkData['rows'] = $rows;
        echo json_encode($bulkData, JSON_UNESCAPED_UNICODE);
    }
    public function user_roles()
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';
        $where = '';
        if ($this->request->getVar('offset'))
            $offset = $this->request->getVar('offset');
        if ($this->request->getVar('limit'))
            $limit = $this->request->getVar('limit');
        if ($this->request->getVar('sort'))
            $sort = $this->request->getVar('sort');
        if ($this->request->getVar('order'))
            $order = $this->request->getVar('order');
        if ($this->request->getVar('search')) {
            $search = $this->request->getVar('search');
            $where = "where (id like '%" . $search . "%' OR tag_name like '%" . $search . "%')";
        }
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_user_roles $where ");
        $res = $query->getResult();
        foreach ($res as $row1) {
            $total = $row1->total;
        }
        $query1 = $this->db->query("SELECT * FROM tbl_user_roles $where ORDER BY $sort $order LIMIT $offset , $limit");
        $res1 = $query1->getResult();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $count = 1;
        foreach ($res1 as $row) {
            $operate = '<a class="btn btn-icon btn-sm btn-primary text-white edit-data" data-id=' . $row->id . ' data-toggle="modal" data-target="#editDataModal" title="Edit"><em class="fa fa-edit"></em></a>&nbsp;&nbsp;';
            $operate .= '<a class="btn btn-icon btn-sm btn-danger text-white delete-data" data-id=' . $row->id . ' title="Delete"><em class="fa fa-trash"></em></a>';
            $tempRow['id'] = $row->id;
            $tempRow['role'] = $row->role;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
            $count++;
        }
        $bulkData['rows'] = $rows;
        echo json_encode($bulkData, JSON_UNESCAPED_UNICODE);
    }
    public function breaking_news()
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';
        $where = '';
        if ($this->request->getVar('offset'))
            $offset = $this->request->getVar('offset');
        if ($this->request->getVar('limit'))
            $limit = $this->request->getVar('limit');
        if ($this->request->getVar('sort'))
            $sort = $this->request->getVar('sort');
        if ($this->request->getVar('order'))
            $order = $this->request->getVar('order');
        if ($this->request->getVar('search')) {
            $search = $this->request->getVar('search');
            $where = "where (b.id like '%" . $search . "%' OR b.title like '%" . $search . "%')";
        }
        $join = ' JOIN tbl_languages l ON  l.id = b.language_id';
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_breaking_news b $join $where ");
        $res = $query->getResult();
        foreach ($res as $row1) {
            $total = $row1->total;
        }
        $query1 = $this->db->query("SELECT b.*, l.language FROM tbl_breaking_news b $join  $where  ORDER BY  $sort  $order  LIMIT  $offset , $limit");
        $res1 = $query1->getResult();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $count = 1;
        foreach ($res1 as $row) {
            $con_v = '';
            $image = (!empty($row->image)) ? 'public/images/breaking_news/' . $row->image : '';
            $operate = '<a class="btn btn-icon btn-sm btn-primary text-white edit-data" data-id=' . $row->id . ' data-toggle="modal" data-target="#editDataModal" title="Edit"><em class="fa fa-edit"></em></a>&nbsp;&nbsp;';
            if ($row->content_type != 'standard_post') {
                if ($row->content_type == 'video_upload') {
                    $con_value = 'public/images/breaking_news_video/' . $row->content_value;
                    $con_v = 'public/images/breaking_news_video/' . $row->content_value;
                    $operate .= '<a class="btn btn-icon btn-sm btn-warning text-white" data-toggle="lightbox" data-title="Video" data-type="video" href="' . $con_value . '" title="view video"><em class="fas fa-eye"></em></a>&nbsp;&nbsp;';
                } else {
                    $con_value = $row->content_value;
                    $con_v = '';
                    $operate .= '<a class="btn btn-icon btn-sm btn-warning text-white" data-toggle="lightbox" data-title="Video" data-type="youtube" href="' . $con_value . '" title="view video"><em class="fas fa-eye"></em></a>&nbsp;&nbsp;';
                }
            } else {
                $con_value = '';
            }
            $views = $this->db->table('tbl_breaking_news_view')->select('COUNT(id) as total')->where('breaking_news_id', $row->id)->get()->getResult();
            $row->total_views = (!empty($views)) ? $views[0]->total : "0";
            $operate .= '<a class="btn btn-icon btn-sm btn-danger text-white delete-data" data-id=' . $row->id . ' data-image="' . $image . '" data-cvalue="' . $con_v . '" title="Delete"><em class="fa fa-trash"></em></a>';
            $tempRow['con_v'] = $con_v;
            $tempRow['content_value'] = $con_value;
            $tempRow['image_url'] = $image;
            $tempRow['id'] = $row->id;
            $tempRow['title'] = $row->title;
            $tempRow['short_title'] = mb_strimwidth($row->title, 0, 50, "...");
            $tempRow['content'] = $row->content_type;
            $tempRow['content_type'] = str_replace('_', ' ', $row->content_type);
            $tempRow['language'] = $row->language;
            $tempRow['language_id'] = $row->language_id;
            $tempRow['image'] = (!empty($row->image)) ? '<a href=' . APP_URL . $image . '  data-toggle="lightbox" data-title="Image" data-gallery="gallery"><img src=' . APP_URL . $image . ' height=50, width=50 >' : 'No Image';
            $tempRow['description'] = $row->description;
            $tempRow['short_description'] = mb_strimwidth($row->description, 0, 70, "...");
            $tempRow['views'] = $row->total_views;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
            $count++;
        }
        $bulkData['rows'] = $rows;
        echo json_encode($bulkData, JSON_UNESCAPED_UNICODE);
    }
    public function live_streaming()
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';
        $where = '';
        if ($this->request->getVar('offset'))
            $offset = $this->request->getVar('offset');
        if ($this->request->getVar('limit'))
            $limit = $this->request->getVar('limit');
        if ($this->request->getVar('sort'))
            $sort = $this->request->getVar('sort');
        if ($this->request->getVar('order'))
            $order = $this->request->getVar('order');
        if ($this->request->getVar('search')) {
            $search = $this->request->getVar('search');
            $where = "where (ls.id like '%" . $search . "%' OR ls.title like '%" . $search . "%')";
        }
        $join = ' JOIN tbl_languages l ON  l.id = ls.language_id';
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_live_streaming ls $join $where ");
        $res = $query->getResult();
        foreach ($res as $row1) {
            $total = $row1->total;
        }
        $query1 = $this->db->query("SELECT ls.*, l.language FROM tbl_live_streaming ls $join $where ORDER BY  $sort  $order  LIMIT  $offset , $limit");
        $res1 = $query1->getResult();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $count = 1;
        foreach ($res1 as $row) {
            $image = (!empty($row->image)) ? 'public/images/liveStreaming/' . $row->image : '';
            $operate = '<a class="btn btn-icon btn-sm btn-primary text-white edit-data" data-id=' . $row->id . ' data-toggle="modal" data-target="#editDataModal" title="Edit"><em class="fa fa-edit"></em></a>&nbsp;&nbsp;';
            $operate .= '<a class="btn btn-icon btn-sm btn-danger text-white delete-data" data-id=' . $row->id . ' data-image="' . $image . '" title="Delete"><em class="fa fa-trash"></em></a>';
            $tempRow['image_url'] = $image;
            $tempRow['id'] = $row->id;
            $tempRow['title'] = $row->title;
            $tempRow['type'] = str_replace('_', ' ', $row->type);
            $tempRow['type1'] = $row->type;
            $tempRow['url'] = $row->url;
            $tempRow['language'] = $row->language;
            $tempRow['language_id'] = $row->language_id;
            $tempRow['image'] = (!empty($row->image)) ? '<a href=' . APP_URL . $image . '  data-toggle="lightbox" data-title="Image" data-gallery="gallery"><img src=' . APP_URL . $image . ' height=50, width=50 >' : 'No Image';
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
            $count++;
        }
        $bulkData['rows'] = $rows;
        echo json_encode($bulkData, JSON_UNESCAPED_UNICODE);
    }
    public function subcategory()
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';
        $where = '';
        if ($this->request->getVar('offset'))
            $offset = $this->request->getVar('offset');
        if ($this->request->getVar('limit'))
            $limit = $this->request->getVar('limit');
        if ($this->request->getVar('sort'))
            $sort = $this->request->getVar('sort');
        if ($this->request->getVar('order'))
            $order = $this->request->getVar('order');
        if ($this->request->getVar('search')) {
            $search = $this->request->getVar('search');
            $where = "where (s.id like '%" . $search . "%' OR c.category_name like '%" . $search . "%')";
        }
        $join = ' JOIN tbl_category c ON c.id = s.category_id';
        $join .= ' JOIN tbl_languages l ON  l.id = s.language_id';
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_subcategory s $join $where ");
        $res = $query->getResult();
        foreach ($res as $row1) {
            $total = $row1->total;
        }
        $query1 = $this->db->query("SELECT s.*, c.category_name, l.language FROM tbl_subcategory s $join $where ORDER BY $sort $order LIMIT $offset , $limit");
        $res1 = $query1->getResult();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $count = 1;
        foreach ($res1 as $row) {
            $image = (!empty($row->image)) ? 'public/images/subcategory/' . $row->image : '';
            $operate = '<a class="btn btn-icon btn-sm btn-primary text-white edit-data" data-id=' . $row->id . ' data-toggle="modal" data-target="#editDataModal" title="Edit"><em class="fa fa-edit"></em></a>&nbsp;&nbsp;';
            $operate .= '<a class="btn btn-icon btn-sm btn-danger text-white delete-data" data-id=' . $row->id . ' data-image="' . $image . '" title="Delete"><em class="fa fa-trash"></em></a>';
            $tempRow['image_url'] = $image;
            $tempRow['id'] = $row->id;
            $tempRow['category_id'] = $row->category_id;
            $tempRow['category_name'] = $row->category_name;
            $tempRow['subcategory_name'] = $row->subcategory_name;
            $tempRow['language'] = $row->language;
            $tempRow['language_id'] = $row->language_id;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
            $count++;
        }
        $bulkData['rows'] = $rows;
        echo json_encode($bulkData, JSON_UNESCAPED_UNICODE);
    }
    public function category()
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';
        $where = '';
        if ($this->request->getVar('offset'))
            $offset = $this->request->getVar('offset');
        if ($this->request->getVar('limit'))
            $limit = $this->request->getVar('limit');
        if ($this->request->getVar('sort'))
            $sort = $this->request->getVar('sort');
        if ($this->request->getVar('order'))
            $order = $this->request->getVar('order');
        if ($this->request->getVar('search')) {
            $search = $this->request->getVar('search');
            $where = "where (c.id like '%" . $search . "%' OR c.category_name like '%" . $search . "%')";
        }
        $join = ' JOIN tbl_languages l ON  l.id = c.language_id';
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_category c $join $where ");
        $res = $query->getResult();
        foreach ($res as $row1) {
            $total = $row1->total;
        }
        $query1 = $this->db->query("SELECT c.*, l.language FROM tbl_category c $join  $where  ORDER BY  $sort  $order  LIMIT  $offset , $limit");
        $res1 = $query1->getResult();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $count = 1;
        foreach ($res1 as $row) {
            $image = (!empty($row->image)) ? 'public/images/category/' . $row->image : '';
            $operate = '<a class="btn btn-icon btn-sm btn-primary text-white edit-data" data-id=' . $row->id . ' data-toggle="modal" data-target="#editDataModal" title="Edit"><em class="fa fa-edit"></em></a>&nbsp;&nbsp;';
            $operate .= '<a class="btn btn-icon btn-sm btn-danger text-white delete-data" data-id=' . $row->id . ' data-image="' . $image . '" title="Delete"><em class="fa fa-trash"></em></a>';
            $tempRow['image_url'] = $image;
            $tempRow['id'] = $row->id;
            $tempRow['category_name'] = $row->category_name;
            $tempRow['language'] = $row->language;
            $tempRow['language_id'] = $row->language_id;
            $tempRow['image'] = (!empty($row->image)) ? '<a href=' . APP_URL . $image . '  data-toggle="lightbox" data-title="Image" data-gallery="gallery"><img src=' . APP_URL . $image . ' height=50, width=50 >' : 'No Image';
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
            $count++;
        }
        $bulkData['rows'] = $rows;
        echo json_encode($bulkData, JSON_UNESCAPED_UNICODE);
    }
    public function users()
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';
        $where = '';
        if ($this->request->getVar('offset'))
            $offset = $this->request->getVar('offset');
        if ($this->request->getVar('limit'))
            $limit = $this->request->getVar('limit');
        if ($this->request->getVar('sort'))
            $sort = $this->request->getVar('sort');
        if ($this->request->getVar('order'))
            $order = $this->request->getVar('order');
        if ($this->request->getVar('search')) {
            $search = $this->request->getVar('search');
            $where = "where (u.id like '%" . $search . "%' OR u.name like '%" . $search . "%' OR u.email like '%" . $search . "%' OR u.mobile like '%" . $search . "%' OR ur.role like '%" . $search . "%')";
        }
        $join = ' LEFT JOIN tbl_user_roles ur ON ur.id = u.role';
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_users u $join $where ");
        $res = $query->getResult();
        foreach ($res as $row1) {
            $total = $row1->total;
        }
        $query1 = $this->db->query("SELECT u.*, ur.role, ur.id as role_id FROM tbl_users u $join $where  ORDER BY  $sort  $order  LIMIT  $offset , $limit");
        $res1 = $query1->getResult();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $count = 1;
        $icon = array(
            'email'  => 'far fa-envelope-open',
            'gmail'  => 'fab fa-google-plus-square text-danger',
            'fb'     => 'fab fa-facebook-square text-primary',
            'apple'  => 'fab fa-apple',
            'mobile' => 'fas fa-phone-square',
        );
        foreach ($res1 as $row) {
            $operate = '<a class="btn btn-icon btn-sm btn-primary text-white edit-data" data-id=' . $row->id . ' data-toggle="modal" data-target="#editDataModal" title="Edit"><em class="fa fa-edit"></em></a>&nbsp;&nbsp;';
            if (filter_var($row->profile, FILTER_VALIDATE_URL) === FALSE) {
                // Not a valid URL. Its a image only or empty
                $profile = (!empty($row->profile)) ? APP_URL . 'public/images/profile/' . $row->profile : '';
            } else {
                /* if it is a ur than just pass url as it is */
                $profile = $row->profile;
            }
            $tempRow['status1'] = $row->status;
            $tempRow['id'] = $row->id;
            $tempRow['name'] = $row->name;
            $tempRow['email'] = hideEmailAddress($row->email); //'****@gmail.com'; //$row->email;
            $tempRow['mobile'] = hideMobileNumber($row->mobile); //'****@gmail.com'; //$row->email; //$row->mobile;
            $tempRow['type'] = (isset($row->type) && $row->type != '') ? '<em class="' . $icon[trim($row->type)] . ' fa-2x"></em>' : '<em class="' . $icon['email'] . ' fa-2x"></em>';
            $tempRow['profile'] = (!empty($row->profile)) ? '<a href=' . $profile . '  data-toggle="lightbox" data-title="Image" data-gallery="gallery"><img src=' . $profile . ' height=50, width=50 >' : 'No Image';
            $tempRow['status'] = ($row->status) ? "<span class='badge badge-success'>Active</span>" : "<span class='badge badge-danger'>Deactive</span>";
            $tempRow['date'] = date('d-M-Y h:i A', strtotime($row->date));
            $tempRow['role'] = $row->role;
            $tempRow['role_id'] = $row->role_id;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
            $count++;
        }
        $bulkData['rows'] = $rows;
        echo json_encode($bulkData, JSON_UNESCAPED_UNICODE);
    }
    public function comments()
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';
        $where = ' WHERE c.parent_id=0';
        if ($this->request->getVar('offset'))
            $offset = $this->request->getVar('offset');
        if ($this->request->getVar('limit'))
            $limit = $this->request->getVar('limit');
        if ($this->request->getVar('sort'))
            $sort = $this->request->getVar('sort');
        if ($this->request->getVar('order'))
            $order = $this->request->getVar('order');
        if ($this->request->getVar('search')) {
            $search = $this->request->getVar('search');
            $where .= " AND (c.id like '%" . $search . "%' OR LOWER(c.message) like '%" . strtolower($search) . "%' OR LOWER(n.title) like '%" . strtolower($search) . "%' OR LOWER(u.name) like '%" . strtolower($search) . "%')";
        }
        $join = ' JOIN tbl_news n ON n.id = c.news_id';
        $join .= ' JOIN tbl_users u ON u.id = c.user_id';
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_comment c $join $where ");
        $res = $query->getResult();
        foreach ($res as $row1) {
            $total = $row1->total;
        }
        $query1 = $this->db->query("SELECT c.*, n.title, u.name FROM tbl_comment c $join $where  ORDER BY  $sort  $order  LIMIT  $offset , $limit");
       
        $res1 = $query1->getResult();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $count = 1;
        foreach ($res1 as $row) {
            $operate = '<a class="btn btn-icon btn-sm btn-danger text-white delete-data" data-id=' . $row->id . ' title="Delete"><em class="fa fa-trash"></em></a>';
            $tempRow['id'] = $row->id;
            $tempRow['user_id'] = $row->user_id;
            $tempRow['name'] = $row->name;
            $tempRow['title'] = $row->title;
            $tempRow['message'] = $row->message;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
            $count++;
        }
        $bulkData['rows'] = $rows;
        echo json_encode($bulkData, JSON_UNESCAPED_UNICODE);
    }
    public function comments_flag()
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';
        $where = '';
        if ($this->request->getVar('offset'))
            $offset = $this->request->getVar('offset');
        if ($this->request->getVar('limit'))
            $limit = $this->request->getVar('limit');
        if ($this->request->getVar('sort'))
            $sort = $this->request->getVar('sort');
        if ($this->request->getVar('order'))
            $order = $this->request->getVar('order');
        $where = " WHERE cf.status=1";
        if ($this->request->getVar('search')) {
            $search = $this->request->getVar('search');
            $where .= " AND (cf.id like '%" . $search . "%' OR cf.message like '%" . $search . "%')";
        }
        $join = ' JOIN tbl_comment c ON c.id = cf.comment_id';
        $join .= ' JOIN tbl_news n ON n.id = cf.news_id';
        $join .= ' JOIN tbl_users u ON u.id = cf.user_id';
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_comment_flag cf $join $where ");
        $res = $query->getResult();
        foreach ($res as $row1) {
            $total = $row1->total;
        }
        $query1 = $this->db->query("SELECT cf.*,c.message as comment,n.title,u.name FROM tbl_comment_flag cf $join $where  ORDER BY  $sort  $order  LIMIT  $offset , $limit");
        $res1 = $query1->getResult();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $count = 1;
        foreach ($res1 as $row) {
            $operate = '<a class="btn btn-icon btn-sm btn-warning text-white delete-comment" data-id=' . $row->comment_id . ' title="Delete Comment" ><em class="fa fa-trash"></em></a>&nbsp;&nbsp;';
            $operate .= '<a class="btn btn-icon btn-sm btn-danger text-white delete-flag" data-id=' . $row->id . ' title="Delete flag"><em class="fa fa-trash"></em></a>';
            $tempRow['id'] = $row->id;
            $tempRow['comment_id'] = $row->comment_id;
            $tempRow['user_id'] = $row->user_id;
            $tempRow['news_id'] = $row->news_id;
            $tempRow['name'] = $row->name;
            $tempRow['message'] = $row->message;
            $tempRow['comment'] = $row->comment;
            $tempRow['title'] = $row->title;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
            $count++;
        }
        $bulkData['rows'] = $rows;
        echo json_encode($bulkData, JSON_UNESCAPED_UNICODE);
    }
    public function notification()
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';
        $where = '';
        if ($this->request->getVar('offset'))
            $offset = $this->request->getVar('offset');
        if ($this->request->getVar('limit'))
            $limit = $this->request->getVar('limit');
        if ($this->request->getVar('sort'))
            $sort = $this->request->getVar('sort');
        if ($this->request->getVar('order'))
            $order = $this->request->getVar('order');
        if ($this->request->getVar('search')) {
            $search = $this->request->getVar('search');
            $where = "where (tn.id like '%" . $search . "%' OR u.name like '%" . $search . "%' OR n.title like '%" . $search . "%')";
        }
        $join = ' LEFT JOIN tbl_category c ON tn.category_id=c.id';
        $join .= ' LEFT JOIN tbl_subcategory s ON tn.subcategory_id=s.id';
        $join .= ' LEFT JOIN tbl_news n ON tn.news_id=n.id';
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_notifications tn $join $where ");
        $res = $query->getResult();
        foreach ($res as $row1) {
            $total = $row1->total;
        }
        $query1 = $this->db->query("SELECT tn.*, c.category_name, s.subcategory_name, n.title as news_title FROM tbl_notifications tn $join $where  ORDER BY  $sort  $order  LIMIT  $offset , $limit");
        $res1 = $query1->getResult();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $count = 1;
        foreach ($res1 as $row) {
            $image = (!empty($row->image)) ? 'public/images/notification/' . $row->image : '';
            $operate = '<a class="btn btn-icon btn-sm btn-danger text-white delete-data" data-id=' . $row->id . ' data-image="' . $image . '" title="Delete"><em class="fa fa-trash"></em></a>';
            $tempRow['id'] = $row->id;
            $tempRow['category_id'] = $row->category_id;
            $tempRow['category_name'] = ($row->category_name) ? $row->category_name : '';
            $tempRow['subcategory_id'] = $row->subcategory_id;
            $tempRow['subcategory_name'] = ($row->subcategory_name) ? $row->subcategory_name : '';
            $tempRow['news_title'] = ($row->news_title) ? $row->news_title : '';
            $tempRow['title'] = $row->title;
            $tempRow['message'] = $row->message;
            $tempRow['image'] = (!empty($row->image)) ? '<a href=' . APP_URL . $image . '  data-toggle="lightbox" data-title="Image" data-gallery="gallery"><img src=' . APP_URL . $image . ' height=50, width=50 >' : 'No Image';
            $tempRow['date'] = date('d-M-Y h:i A', strtotime($row->date_sent));
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
            $count++;
        }
        $bulkData['rows'] = $rows;
        echo json_encode($bulkData, JSON_UNESCAPED_UNICODE);
    }
    public function news()
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';
        $where = '';
        if ($this->request->getVar('offset'))
            $offset = $this->request->getVar('offset');
        if ($this->request->getVar('limit'))
            $limit = $this->request->getVar('limit');
        if ($this->request->getVar('sort'))
            $sort = $this->request->getVar('sort');
        if ($this->request->getVar('order'))
            $order = $this->request->getVar('order');
        $where = " WHERE n.title IS NOT NULL";
        if ($this->request->getVar('category')) {
            $category = $this->request->getVar('category');
            $where .= " AND n.category_id=" . $category;
            if ($this->request->getVar('subcategory')) {
                $subcategory = $this->request->getVar('subcategory');
                $where .= " AND n.subcategory_id=" . $subcategory;
            }
        }
        if ($this->request->getVar('language')) {
            $language = $this->request->getVar('language');
            $where .= " AND n.language_id=" . $language;
        }
        if ($this->request->getVar('user')) {
            $user = $this->request->getVar('user');
            $where .= " AND n.user_id=" . $user;
        }
        if ($this->request->getVar('role')) {
            $role = $this->request->getVar('role');
            $where .= " AND u.role=" . $role;
        }
        if ($this->request->getVar('status') or $this->request->getVar('status') == '0') {
            $status = $this->request->getVar('status');
            $where .= " AND n.status=" . $status;
        }
        if ($this->request->getVar('search')) {
            $search = $this->request->getVar('search');
            $where = " WHERE (n.id like '%" . $search . "%' OR c.category_name like '%" . $search . "%' OR n.title like '%" . $search . "%')";
            if ($this->request->getVar('category')) {
                $category = $this->request->getVar('category');
                $where .= " AND n.category_id=" . $category;
                if ($this->request->getVar('subcategory')) {
                    $subcategory = $this->request->getVar('subcategory');
                    $where .= " AND n.subcategory_id=" . $subcategory;
                }
                if ($this->request->getVar('user')) {
                    $user = $this->request->getVar('user');
                    $where .= " AND n.user_id=" . $user;
                }
            }
        }
        $join = ' LEFT JOIN tbl_category c ON c.id = n.category_id';
        $join .= ' LEFT JOIN tbl_subcategory s ON s.id = n.subcategory_id';
        $join .= ' LEFT JOIN tbl_users u ON u.id = n.user_id';
        $join .= ' LEFT JOIN tbl_languages l ON  l.id = n.language_id';
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_news n $join $where ");
        $res = $query->getResult();
        foreach ($res as $row1) {
            $total = $row1->total;
        }
        $query1 = $this->db->query("SELECT n.*, c.category_name, s.subcategory_name, u.name, l.language FROM tbl_news n $join $where  ORDER BY  $sort  $order  LIMIT  $offset , $limit");
        $res1 = $query1->getResult();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $count = 1;
        foreach ($res1 as $row) {
            $con_v = '';
            $image = (!empty($row->image)) ? 'public/images/news/' . $row->image : '';
            $operate = '<a class="btn btn-icon btn-sm btn-success text-white edit-data-des" data-id=' . $row->id . ' data-toggle="modal" data-target="#editDataDesModal" title="Description"><em class="fa fa-eye"></em></a>&nbsp;&nbsp;';
            if ($row->content_type != 'standard_post') {
                if ($row->content_type == 'video_upload') {
                    $con_value = 'public/images/news_video/' . $row->content_value;
                    $con_v = 'public/images/news_video/' . $row->content_value;
                    $operate .= '<a class="btn btn-icon btn-sm btn-warning text-white" data-toggle="lightbox" data-title="Video" data-type="video" href="' . $con_value . '" title="view video"><em class="fas fa-eye"></em></a>&nbsp;&nbsp;';
                } else {
                    $con_value = $row->content_value;
                    $con_v = '';
                    $operate .= '<a class="btn btn-icon btn-sm btn-warning text-white" data-toggle="lightbox" data-title="Video" data-type="youtube" href="' . $con_value . '" title="view video"><em class="fas fa-eye"></em></a>&nbsp;&nbsp;';
                }
            } else {
                $con_value = "";
            }
            $views = $this->db->table('tbl_news_view')->select('COUNT(id) as total')->where('news_id', $row->id)->get()->getResult();
            $row->total_views = (!empty($views)) ? $views[0]->total : "0";
            $likes = $this->db->table('tbl_news_like')->select('COUNT(id) as total')->where('news_id', $row->id)->get()->getResult();
            $row->total_likes = (!empty($likes)) ? $likes[0]->total : "0";
            $operate .= '<a class="btn btn-icon btn-sm btn-primary text-white edit-data" data-id=' . $row->id . ' data-toggle="modal" data-target="#editDataModal" title="Edit"><em class="fa fa-edit"></em></a>&nbsp;&nbsp;';
            $operate .= '<a class="btn btn-icon btn-sm btn-danger text-white delete-data" data-id=' . $row->id . ' data-image="' . $image . '" data-cvalue="' . $con_v . '" title="Delete"><em class="fa fa-trash"></em></a>&nbsp;&nbsp;';
            $operate .= '<a class="btn btn-icon btn-sm btn-info text-white clone-data" data-id=' . $row->id . ' data-image="' . $image . '" data-cvalue="' . $con_v . '" title="Clone News"><em class="fa fa-clone"></em></a>';
            $total_image = $this->db->table('tbl_news_image')->where('news_id', $row->id)->countAllResults();
            if (!empty($row->tag_id)) {
                $query2 = $this->db->query('SELECT GROUP_CONCAT(distinct(tag_name)) as tag_name FROM tbl_tag WHERE id IN(' . $row->tag_id . ')');
                $res2 = $query2->getResult();
            }
            $tag_name = (!empty($res2)) ? $res2[0]->tag_name : '';
            $tempRow['con_v'] = $con_v;
            $tempRow['content_value'] = $con_value;
            $tempRow['image_url'] = $image;
            $tempRow['date1'] = $row->date;
            $tempRow['content'] = $row->content_type;
            $tempRow['id'] = $row->id;
            $tempRow['category_id'] = $row->category_id;
            $tempRow['category_name'] = ($row->category_name) ? $row->category_name : '';
            $tempRow['subcategory_id'] = $row->subcategory_id;
            $tempRow['subcategory_name'] = ($row->subcategory_name) ? $row->subcategory_name : '';
            $tempRow['tag_id'] = $row->tag_id;
            $tempRow['tag_name'] = ($tag_name) ? $tag_name : '';
            $tempRow['title'] = $row->title;
            $tempRow['short_title'] = mb_strimwidth($row->title, 0, 25, "...");
            $tempRow['content_type'] = str_replace('_', ' ', $row->content_type);
            $tempRow['image'] = (!empty($row->image)) ? '<a href=' . APP_URL . $image . '  data-toggle="lightbox" data-title="Image" data-gallery="gallery"><img src=' . APP_URL . $image . ' height=50, width=50 >' : 'No Image';
            $tempRow['description'] = $row->description;
            $tempRow['date'] = date('d-M-Y', strtotime($row->date));
            $tempRow['total_image'] = '<a href=' . APP_URL . "news-image/" . $row->id . ' class="btn btn-icon btn-sm btn-warning" title="other image">' . $total_image . '</a>';
            $tempRow['user'] = ($row->name) ? $row->name : '-';
            $tempRow['show_till'] = $row->show_till;
            $tempRow['is_expire'] = is_expire($row->show_till);
            $tempRow['status'] = is_news_status($row->status);
            $tempRow['language'] = $row->language;
            $tempRow['language_id'] = $row->language_id;
            $tempRow['views'] = $row->total_views;
            $tempRow['likes'] = $row->total_likes;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
            $count++;
        }
        $bulkData['rows'] = $rows;
        echo json_encode($bulkData, JSON_UNESCAPED_UNICODE);
    }
    public function newsImage()
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';
        $where = '';
        if ($this->request->getVar('offset'))
            $offset = $this->request->getVar('offset');
        if ($this->request->getVar('limit'))
            $limit = $this->request->getVar('limit');
        if ($this->request->getVar('sort'))
            $sort = $this->request->getVar('sort');
        if ($this->request->getVar('order'))
            $order = $this->request->getVar('order');
        $news_id = $this->request->getVar('news_id');
        $where = " WHERE tni.news_id='" . $news_id . "' ";
        if ($this->request->getVar('search')) {
            $search = $this->request->getVar('search');
            $where .= " AND (tni.id like '%" . $search . "%' OR  OR tn.title like '%" . $search . "%')";
        }
        $join = ' JOIN tbl_news tn ON tn.id = tni.news_id';
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_news_image tni $join $where ");
        $res = $query->getResult();
        foreach ($res as $row1) {
            $total = $row1->total;
        }
        $query1 = $this->db->query("SELECT tni.*, tn.title FROM tbl_news_image tni $join $where  ORDER BY  $sort  $order  LIMIT  $offset , $limit");
        $res1 = $query1->getResult();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $count = 1;
        foreach ($res1 as $row) {
            $image = (!empty($row->other_image)) ? 'public/images/news/' . $row->news_id . '/' . $row->other_image : '';
            $operate = '<a class="btn btn-icon btn-sm btn-danger text-white delete-data" data-id=' . $row->id . ' data-image="' . $image . '" title="Delete"><em class="fa fa-trash"></em></a>';
            $tempRow['image_url'] = $image;
            $tempRow['id'] = $row->id;
            $tempRow['news_id'] = $row->news_id;
            $tempRow['title'] = $row->title;
            $tempRow['image'] = (!empty($image)) ? '<a href=' . APP_URL . $image . '  data-toggle="lightbox" data-title="Image" data-gallery="gallery"><img src=' . APP_URL . $image . ' height=50, width=50 >' : 'No Image';
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
            $count++;
        }
        $bulkData['rows'] = $rows;
        echo json_encode($bulkData, JSON_UNESCAPED_UNICODE);
    }
    public function survey()
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';
        $where = '';
        if ($this->request->getVar('offset'))
            $offset = $this->request->getVar('offset');
        if ($this->request->getVar('limit'))
            $limit = $this->request->getVar('limit');
        if ($this->request->getVar('sort'))
            $sort = $this->request->getVar('sort');
        if ($this->request->getVar('order'))
            $order = $this->request->getVar('order');
        if ($this->request->getVar('search')) {
            $search = $this->request->getVar('search');
            $where = "where (id like '%" . $search . "%' OR question like '%" . $search . "%')";
        }
        $join = ' JOIN tbl_languages l ON  l.id = sq.language_id';
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_survey_question sq $where ");
        $res = $query->getResult();
        foreach ($res as $row1) {
            $total = $row1->total;
        }
        $query1 = $this->db->query("SELECT sq.*, l.language FROM tbl_survey_question sq $join $where ORDER BY $sort $order LIMIT $offset , $limit");
        $res1 = $query1->getResult();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $count = 1;
        foreach ($res1 as $row) {
            $operate = '<a href="survey_option/' . $row->id . '" class="btn btn-icon btn-sm btn-success text-white view-data" data-id=' . $row->id . ' title="View"><em class="fa fa-eye"></em></a>&nbsp;&nbsp;';
            $operate .= '<a class="btn btn-icon btn-sm btn-primary text-white edit-data" data-id=' . $row->id . ' data-toggle="modal" data-target="#editDataModal" title="Edit"><em class="fa fa-edit"></em></a>&nbsp;&nbsp;';
            $operate .= '<a class="btn btn-icon btn-sm btn-danger text-white delete-data" data-id=' . $row->id . ' title="Delete"><em class="fa fa-trash"></em></a>';
            $total_ans = $this->db->table('tbl_survey_result')->where('question_id', $row->id)->get()->getResult();
            $tempRow['id'] = $row->id;
            $tempRow['question'] = $row->question;
            $tempRow['status'] = ($row->status) ? "<span class='badge badge-success'>Enable</span>" : "<span class='badge badge-danger'>Disable</span>";
            $tempRow['counter'] = count($total_ans);
            $tempRow['language'] = $row->language;
            $tempRow['language_id'] = $row->language_id;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
            $count++;
        }
        $bulkData['rows'] = $rows;
        echo json_encode($bulkData, JSON_UNESCAPED_UNICODE);
    }
    public function surveyoption()
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';
        $where = '';
        if ($this->request->getVar('offset'))
            $offset = $this->request->getVar('offset');
        if ($this->request->getVar('limit'))
            $limit = $this->request->getVar('limit');
        if ($this->request->getVar('sort'))
            $sort = $this->request->getVar('sort');
        if ($this->request->getVar('order'))
            $order = $this->request->getVar('order');
        $question_id = $this->request->getVar('question_id');
        $where = " WHERE tbl_survey_option.question_id='" . $question_id . "' ";
        if ($this->request->getVar('search')) {
            $search = $this->request->getVar('search');
            $where .= " AND (tbl_survey_option.options like '%" . $search . "%')";
        }
        $join = ' JOIN tbl_survey_question ON tbl_survey_question.id = tbl_survey_option.question_id';
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_survey_option $join $where ");
        $res = $query->getResult();
        foreach ($res as $row1) {
            $total = $row1->total;
        }
        $query1 = $this->db->query("SELECT tbl_survey_option.*, tbl_survey_question.question, tbl_survey_question.id as question_id FROM tbl_survey_option  $join $where  ORDER BY  $sort  $order  LIMIT  $offset , $limit");
        $res1 = $query1->getResult();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $count = 1;
        foreach ($res1 as $row) {
            $operate = '<a class="btn btn-icon btn-sm btn-primary text-white edit-data" data-id=' . $row->id . ' data-toggle="modal" data-target="#editDataModal" title="Edit"><em class="fa fa-edit"></em></a>&nbsp;&nbsp;';
            $operate .= '<a class="btn btn-icon btn-sm btn-danger text-white delete-data" data-id=' . $row->id . ' title="Delete"><em class="fa fa-trash"></em></a>';
            $get_user = $this->db->table('tbl_survey_result')->where('question_id', $question_id)->get()->getResult();
            $per = '0';
            if ($get_user) {
                $total_user = count($get_user);
                $per = $row->counter * 100 / $total_user;
            }
            $tempRow['id'] = $row->id;
            $tempRow['question'] = $row->question;
            $tempRow['question_id'] = $question_id;
            $tempRow['counter'] = ($row->counter) ? ($row->counter) : '0';
            $tempRow['options'] = $row->options;
            $tempRow['percentage'] = ($per) ? (round($per, 2) . ' %') : '0 %';
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
            $count++;
        }
        $bulkData['rows'] = $rows;
        echo json_encode($bulkData, JSON_UNESCAPED_UNICODE);
    }
    public function get_all_language()
    {
        $offset = 0;
        $limit = 10;
        $sort = 'status';
        $order = 'DESC';
        $where = '';
        if ($this->request->getVar('offset')) {
            $offset = $this->request->getVar('offset');
        }
        if ($this->request->getVar('limit')) {
            $limit = $this->request->getVar('limit');
        }
        if ($this->request->getVar('sort')) {
            $sort = $this->request->getVar('sort');
        }
        if ($this->request->getVar('order')) {
            $order = $this->request->getVar('order');
        }
        if ($this->request->getVar('search')) {
            $search = $this->request->getVar('search');
            $where = "where (id like '%" . $search . "%' OR category_name like '%" . $search . "%')";
        }
        //$where = $where . "where status = 1";
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_languages $where ");
        $res = $query->getResult();
        foreach ($res as $row1) {
            $total = $row1->total;
        }
        $query1 = $this->db->query("SELECT * FROM tbl_languages  $where  ORDER BY  $sort  $order  LIMIT  $offset , $limit");
        $res1 = $query1->getResult();
        $default_language = $this->db->table('tbl_settings')->where('type', 'default_language')->get()->getResult();
        $default_language = json_decode($default_language[0]->message, true);
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $count = 1;
        foreach ($res1 as $row) {
            $image = (!empty($row->image)) ? 'public/images/flags/' . $row->image : '';
            $operate = '<a class="btn btn-icon btn-sm btn-primary text-white edit-data" data-id=' . $row->id . ' data-toggle="modal" data-target="#editDataModal" title="Edit"><em class="fa fa-edit"></em></a>&nbsp;&nbsp;';
            $operate .= '<a class="btn btn-icon btn-sm btn-danger text-white delete-data" title="Delete" data-id=' . $row->id . ' title="Delete"><em class="fa fa-trash"></em></a>';
            $tempRow['id'] = $row->id;
            $tempRow['language'] = $row->language;
            $tempRow['display_name'] = $row->display_name;
            $tempRow['code'] = $row->code;
            $tempRow['status'] = is_language_status($row->status);
            $tempRow['isRTL'] = $row->isRTL;
            $tempRow['image'] = (!empty($row->image)) ? '<a href=' . APP_URL . $image . '  data-toggle="lightbox" data-title="Image" data-gallery="gallery"><img src=' . APP_URL . $image . ' height=50, width=50 >' : 'No Image';
            $tempRow['default'] = ($default_language == $row->id) ? '<span class="badge badge-secondary"><em class="fa fa-check"></em> Default</span>' : '<a class="btn btn-icon btn-sm btn-info text-white store_default_language" data-id="' . $row->id . '"><em class="fa fa-ellipsis-h"></em> Set as Default</a>';
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
            $count++;
        }
        $bulkData['rows'] = $rows;
        echo json_encode($bulkData, JSON_UNESCAPED_UNICODE);
    }
    public function pages()
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';
        $where = '';
        if ($this->request->getVar('offset'))
            $offset = $this->request->getVar('offset');
        if ($this->request->getVar('limit'))
            $limit = $this->request->getVar('limit');
        if ($this->request->getVar('sort'))
            $sort = $this->request->getVar('sort');
        if ($this->request->getVar('order'))
            $order = $this->request->getVar('order');
        if ($this->request->getVar('search')) {
            $search = $this->request->getVar('search');
            $where = "where (p.id like '%" . $search . "%' OR p.title  like '%" . $search . "%')";
        }
        if ($this->request->getVar('language')) {
            $language = $this->request->getVar('language');
            $where .= " AND p.language_id=" . $language;
        }
        if ($this->request->getVar('policy_type') && $this->request->getVar('policy_type') == 'terms_policy') {
            $where .= " AND p.is_termspolicy='1'";
        }
        if ($this->request->getVar('policy_type') && $this->request->getVar('policy_type') == 'privacy_policy') {
            $where .= " AND p.is_privacypolicy='1'";
        }
        if ($this->request->getVar('status') || $this->request->getVar('status') == '0') {
            $status = $this->request->getVar('status');
            $where .= " AND p.status=" . $status;
        }
        $join = ' JOIN tbl_languages l ON  l.id = p.language_id';
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_pages p $join $where ");
        $res = $query->getResult();
        foreach ($res as $row1) {
            $total = $row1->total;
        }
        $query1 = $this->db->query("SELECT p.*, l.language FROM tbl_pages p $join  $where  ORDER BY  $sort  $order  LIMIT  $offset , $limit");
        $res1 = $query1->getResult();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $count = 1;
        foreach ($res1 as $row) {
            $policy = 'none';
            if($row->is_termspolicy == '1'){
                $policy = 'terms-policy';
            }elseif($row->is_privacypolicy == '1'){
                $policy = 'privacy-policy';
            }
            $operate = '<a class="btn btn-icon btn-sm btn-primary text-white edit-data" data-id=' . $row->id . ' data-toggle="modal" data-target="#editDataModal" title="Edit"><em class="fa fa-edit"></em></a>&nbsp;&nbsp;';
            $operate .= '<a class="btn btn-icon btn-sm btn-danger text-white delete-data" data-id=' . $row->id . ' data-iscustom=' . $row->is_custom . ' title="Delete"><em class="fa fa-trash"></em></a>';
            $image = (!empty($row->page_icon)) ? 'public/images/pages/' . $row->page_icon : '';
            $tempRow['id'] = $row->id;
            $tempRow['title'] = $row->title;
            $tempRow['slug'] = $row->slug;
            $tempRow['meta_description'] = $row->meta_description;
            $tempRow['meta_keywords'] = $row->meta_keywords;
            $tempRow['language'] = $row->language;
            $tempRow['language_id'] = $row->language_id;
            $tempRow['page_content'] = $row->page_content;
            $tempRow['image'] = (!empty($image)) ? '<a href=' . APP_URL . $image . '  data-toggle="lightbox" data-title="Image" data-gallery="gallery"><img src=' . APP_URL . $image . ' height=50, width=50 >' : 'No Image';
            $tempRow['page_type'] = ($row->is_custom) ? "<span class='badge badge-info'>Custom</span>" : "<span class='badge badge-secondary'>Default</span>";
            $tempRow['status'] = ($row->status) ? "<span class='badge badge-success'>Enable</span>" : "<span class='badge badge-danger'>Disable</span>";
            $tempRow['is_termspolicy'] = $row->is_termspolicy;
            $tempRow['is_privacypolicy'] = $row->is_privacypolicy;
            $tempRow['is_policy'] = is_policy_page($policy);
            
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
            $count++;
        }
        $bulkData['rows'] = $rows;
        echo json_encode($bulkData, JSON_UNESCAPED_UNICODE);
    }
    public function featured_sections()
    {
        $sort = 'row_order';
        $order = 'ASC';
        $where = 'where 1=1';

        if ($this->request->getVar('offset'))
            $offset = $this->request->getVar('offset');
        if ($this->request->getVar('limit'))
            $limit = $this->request->getVar('limit');
        if ($this->request->getVar('sort'))
            $sort = $this->request->getVar('sort');
        if ($this->request->getVar('order'))
            $order = $this->request->getVar('order');
        if ($this->request->getVar('search')) {
            $search = $this->request->getVar('search');
            $where = " AND (fs.id like '%" . $search . "%' OR fs.title  like '%" . $search . "%')";
        }
        if ($this->request->getVar('language')) {
            $language = $this->request->getVar('language');
            $where .= " AND fs.language_id=" . $language;
        }
        if ($this->request->getVar('status') or $this->request->getVar('status') == '0') {
            $status = $this->request->getVar('status');
            $where .= " AND fs.status=" . $status;
        }
        $join = ' JOIN tbl_languages l ON  l.id = fs.language_id';
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_featured_sections fs $join $where ");
        $res = $query->getResult();
        foreach ($res as $row1) {
            $total = $row1->total;
        }
        $query1 = $this->db->query("SELECT fs.*, l.language FROM tbl_featured_sections fs $join  $where  ORDER BY  $sort  $order ");
        $res1 = $query1->getResult();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $count = 1;
        foreach ($res1 as $row) {
            $operate = '<a class="btn btn-icon btn-sm btn-primary text-white edit-data" data-id=' . $row->id . ' data-toggle="modal" data-target="#editDataModal" title="Edit"><em class="fa fa-edit"></em></a>&nbsp;&nbsp;';
            $operate .= '<a class="btn btn-icon btn-sm btn-danger text-white delete-data" data-id=' . $row->id . ' title="Delete"><em class="fa fa-trash"></em></a>';
            $tempRow['id'] = $row->id;
            $tempRow['title'] = $row->title;
            $tempRow['short_description'] = mb_strimwidth($row->short_description, 0, 40, "...");
            $tempRow['news_type'] = $row->news_type;
            $tempRow['videos_type'] = $row->videos_type;
            $tempRow['filter_type'] = $row->filter_type;
            $tempRow['style_app'] = style_app($row->style_app);
            $tempRow['style_app_edit'] = $row->style_app;
            $tempRow['style_web'] = style_web($row->style_web);
            $tempRow['style_web_edit'] = $row->style_web;
            $tempRow['date'] = date('d-M-Y', strtotime($row->created_at));
            $tempRow['status'] = is_featured_section_status($row->status);
            $tempRow['language'] = $row->language;
            $tempRow['language_id'] = $row->language_id;
            $tempRow['news_ids'] = $row->news_ids;
            $tempRow['category_ids'] = $row->category_ids;
            $tempRow['subcategory_ids'] = $row->subcategory_ids;
            $tempRow['is_based_on_user_choice'] = $row->is_based_on_user_choice;
            $tempRow['operate'] = $operate;
            $tempRow['row_order'] = '<span class="btn btn-icon btn-sm btn-warning move" alt="Move" >' . $row->row_order . '</span>';
            $rows[] = $tempRow;
            $count++;
        }
        $bulkData['rows'] = $rows;
        echo json_encode($bulkData, JSON_UNESCAPED_UNICODE);
    }
	public function ad_spaces()
    {
        $offset = 0;
        $limit = 10;
        $sort = 'ad.id';
        $order = 'DESC';
        $where = 'where 1=1';
        if ($this->request->getVar('offset'))
            $offset = $this->request->getVar('offset');
        if ($this->request->getVar('limit'))
            $limit = $this->request->getVar('limit');
        if ($this->request->getVar('sort'))
            $sort = $this->request->getVar('sort');
        if ($this->request->getVar('order'))
            $order = $this->request->getVar('order');
        if ($this->request->getVar('search')) {
            $search = $this->request->getVar('search');
            $where = "AND (ad.id like '%" . $search . "%' OR ad.ad_space  like '%" . $search . "%')";
        }
        if ($this->request->getVar('language')) {
            $language = $this->request->getVar('language');
            $where .= " AND ad.language_id=" . $language;
        }
        if ($this->request->getVar('status') or $this->request->getVar('status') == '0') {
            $status = $this->request->getVar('status');
            $where .= " AND ad.status=" . $status;
        }
        $join = ' LEFT JOIN tbl_featured_sections fs ON  fs.id = ad.ad_featured_section_id';
        $join .= ' LEFT JOIN tbl_languages l ON  l.id = ad.language_id';
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_ad_spaces ad  $join $where ");
        $res = $query->getResult();
        foreach ($res as $row1) {
            $total = $row1->total;
        }
        $query1 = $this->db->query("SELECT ad.*, fs.title, l.language FROM tbl_ad_spaces ad  $join $where  ORDER BY  $sort  $order  LIMIT  $offset , $limit");
        $res1 = $query1->getResult();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $count = 1;
        foreach ($res1 as $row) {
            $image = (!empty($row->ad_image)) ? 'public/images/ad_spaces/' . $row->ad_image : '';
            $web_image = (!empty($row->web_ad_image)) ? 'public/images/ad_spaces/' . $row->web_ad_image : '';
            $operate = '<a class="btn btn-icon btn-sm btn-primary text-white edit-data" data-id=' . $row->id . ' data-toggle="modal" data-target="#editDataModal" title="Edit"><em class="fa fa-edit"></em></a>&nbsp;&nbsp;';
            $operate .= '<a class="btn btn-icon btn-sm btn-danger text-white delete-data" data-id=' . $row->id . '  data-image="' . $row->ad_image . '" title="Delete"><em class="fa fa-trash"></em></a>';
            $tempRow['id'] = $row->id;
			$tempRow['date'] = date('d-M-Y', strtotime($row->created_at));
            $tempRow['language_id'] = $row->language_id;
            $tempRow['ad_language'] = $row->language;
			$tempRow['ad_space'] = $row->ad_space;
			$tempRow['ad_featured_section_id'] = $row->ad_featured_section_id;
            $tempRow['ad_featured_section'] = (!empty($row->title)) ? 'Above '.$row->title : '';
            $tempRow['ad_image'] = (!empty($row->ad_image)) ? '<a href=' . APP_URL . $image . '  data-toggle="lightbox" data-title="Image" data-gallery="gallery"><img src=' . APP_URL . $image . ' height=75, width=400 >' : 'No Image';
            $tempRow['web_ad_image'] = (!empty($row->web_ad_image)) ? '<a href=' . APP_URL . $web_image . '  data-toggle="lightbox" data-title="Image" data-gallery="gallery"><img src=' . APP_URL . $web_image . ' height=45, width=400 >' : 'No Image';
            $tempRow['ad_image_url'] = $image;
            $tempRow['web_ad_image_url'] = $web_image;
			$tempRow['ad_url'] = $row->ad_url;
            $tempRow['status'] = ($row->status) ? "<span class='badge badge-success'>Enable</span>" : "<span class='badge badge-danger'>Disable</span>";
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
            $count++;
        }
        $bulkData['rows'] = $rows;
        echo json_encode($bulkData, JSON_UNESCAPED_UNICODE);
    }
}
?>