<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\News_Model;
use App\Models\Category_Model;
use App\Models\BreakingNews_Model;
use App\Models\FeaturedSections_Model;

class FeaturedSections extends Controller
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        $this->session = \Config\Services::session();
        $this->session->start();
        $this->helpers = helper('SystemSettings');
        $this->helpers = helper('News');
        date_default_timezone_set(get_system_timezone());
        $this->toDate = date('Y-m-d H:i:s');
        $this->News_Model = new News_Model();
        $this->Category_Model = new Category_Model();
        $this->BreakingNews_Model = new BreakingNews_Model();
        $this->FeaturedSections_Model = new FeaturedSections_Model();
        $this->db = \Config\Database::connect();
        $this->data['app_name'] = $this->db->table('tbl_settings')->where('type', 'app_name')->get()->getResult();
        $this->data['app_logo'] = $this->db->table('tbl_settings')->where('type', 'app_logo')->get()->getResult();
        $this->data['languages'] = $this->db->table('tbl_languages')->where('status', 1)->get()->getResult();
    }
    public function get_categories_tree()
    {
        $categories = array();
        $category = array();
        $subcategories = array();
        $subcategory = array();
        $res = $this->db->table('tbl_category');
        if ($this->request->getVar('language_id')) {
            $language_id = $this->request->getVar('language_id');
            $res->where('language_id', $language_id);
        }
        $results = $res->get()->getResult();
        foreach ($results as $mainCat) {
            $results2 = $this->db->table('tbl_subcategory')->where('category_id', $mainCat->id)->get()->getResult();
            foreach ($results2 as $key => $subCat) {
                $subcategory['id'] = $subCat->id ?? '';
                $subcategory['name'] = $subCat->subcategory_name ?? '';
                $subcategory['parentid'] = $mainCat->id ?? '';
                $subcategories[$subCat->id] = $subcategory;
            }
            $category['id'] = $mainCat->id;
            $category['name'] = $mainCat->category_name;
            $category['subcat'] = $subcategories;
            $categories[$mainCat->id] = $category;
            $subcategories = array();
            $subcategory = array();
        }
        $option = '<option value="0">Select category</option>';
        if (!empty($categories)) {
            foreach ($categories as $cate1) {
                $option .= '<option value="cat-' . $cate1['id'] . '">' . $cate1['name'] . '</option>';
                foreach ($cate1['subcat'] as $subcate1) {
                    $option .= '<option value="subcat-' . $subcate1['id'] . '">---' . $subcate1['name'] . '</option>';
                }
            }
        }
        return $option;
    }
    public function get_custom_news()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            if ($this->request->getVar('language_id')) {
                $language_id = $this->request->getVar('language_id');
                if ($this->request->getVar('news_type') == 'news') {
                    $results = $this->db->table('tbl_news')->where('language_id', $language_id)->where('status', '1')->get()->getResult();
                } elseif ($this->request->getVar('news_type') == 'breaking_news') {
                    $results = $this->db->table('tbl_breaking_news')->where('language_id', $language_id)->get()->getResult();
                } elseif ($this->request->getVar('news_type') == 'videos') {
                    if ($this->request->getVar('videos_type') == 'news') {
                        $results = $this->db->table('tbl_news')->where('language_id', $language_id)->whereIn('content_type', explode(",", 'video_upload,video_youtube,video_other'))->get()->getResult();
                    }
                    if ($this->request->getVar('videos_type') == 'breaking_news') {
                        $results = $this->db->table('tbl_breaking_news')->where('language_id', $language_id)->whereIn('content_type', explode(",", 'video_upload,video_youtube,video_other'))->get()->getResult();
                    }
                }
            }
            $option = '<option value="0">Select</option>';
            if (!empty($results)) {
                foreach ($results as $res) {
                    $option .= '<option value="' . $res->id . '">' . $res->title . '</option>';
                }
            }
            return $option;
        }
    }
    public function index()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            $this->data['news'] = $this->News_Model->orderBy('id', 'DESC')->findAll();
            $this->data['cats'] = $this->Category_Model->orderBy('id', 'DESC')->findAll();
            return view('featured_sections', $this->data);
        }
    }
    public function store()
    {
        if (is_modification_allowed()) {
            $this->session->setFlashdata('error', DEMO_VERSION_MSG);
        } else {
            $ids = $this->request->getVar('category_ids');
            $cat = array();
            $subcat = array();
            if (!empty($ids)) {
                foreach ($ids as $id) {
                    $string = explode("-", $id);
                    if ($string[0] == 'cat') {
                        array_push($cat, $string[1]);
                    } elseif ($string[0] == 'subcat') {
                        array_push($subcat, $string[1]);
                    }
                }
            }
            if (!empty($cat)) {
                $cat = implode(',', $cat);
            } else {
                $cat = '';
            }
            if (!empty($subcat)) {
                $subcat = implode(',', $subcat);
            } else {
                $subcat = '';
            }
            $news_ids = $this->request->getVar('news_ids');
            if (!empty($news_ids)) {
                $news_id = implode(',', $news_ids);
            }

            $data = [
                'language_id'       => $this->request->getVar('language_id'),
                'title'             => $this->request->getVar('title'),
                'short_description' => $this->request->getVar('short_description'),
                'news_type'         => $this->request->getVar('news_type') ?? '',
                'videos_type'       => $this->request->getVar('videos_type') ?? '',
                'filter_type'       => $this->request->getVar('filter_type') ?? '',
                'category_ids'      => $cat ?? '',
                'subcategory_ids'   => $subcat ?? '',
                'news_ids'          => $news_id ?? '',
                'style_app'         => $this->request->getVar('style_app'),
                'style_web'         => $this->request->getVar('style_web'),
                'row_order'         => '',
                'created_at'        => $this->toDate,
                'status'            => 1,
                'is_based_on_user_choice'  => $this->request->getVar('based_on_user_choice_mode') ?? '',
            ];
           
            $this->FeaturedSections_Model->insert($data);
            $this->session->setFlashdata('success', 'Section inserted successfully.!');
        }
    return redirect('featured_sections');
    }
    public function update()
    {
        if (is_modification_allowed()) {
            $this->session->setFlashdata('error', DEMO_VERSION_MSG);
        } else {
            $edit_id = $this->request->getVar('edit_id');
            $category_ids = $this->request->getVar('category_ids');
            print_r($category_ids);
            $cat = array();
            $subcat = array();
            
            if (!empty($category_ids)) {
                foreach ($category_ids as $id) {
                    $string = explode("-", $id);
                    
                    if ($string[0] == 'cat') {
                        array_push($cat, $string[1]);
                    } elseif ($string[0] == 'subcat') {
                        array_push($subcat, $string[1]);
                    }
                }
            }
            if (!empty($cat)) {
                $cat = implode(',', $cat);
            } else {
                $cat = '';
            }
            if (!empty($subcat)) {
                $subcat = implode(',', $subcat);
            } else {
                $subcat = '';
            }
            $news_ids = $this->request->getVar('news_ids');
            if (!empty($news_ids)) {
                $news_id = implode(',', $news_ids);
            }
            $data = [
                'language_id'       => $this->request->getVar('language_id'),
                'title'             => $this->request->getVar('title'),
                'short_description' => $this->request->getVar('short_description'),
                'news_type'         => $this->request->getVar('news_type'),
                'videos_type'       => $this->request->getVar('videos_type') ?? '',
                'filter_type'       => $this->request->getVar('filter_type'),
                'category_ids'      => $cat ?? '',
                'subcategory_ids'   => $subcat ?? '',
                'news_ids'          => $news_id ?? '',
                'style_app'         => $this->request->getVar('style_app'),
                'style_web'         => $this->request->getVar('style_web'),
                'created_at'        => $this->toDate,
                'status'            => $this->request->getVar('status'),
                'is_based_on_user_choice'  => $this->request->getVar('edit_based_on_user_choice_mode') ?? '',
            ];
           
            $this->FeaturedSections_Model->update($edit_id, $data);
            $this->session->setFlashdata('success', 'Data Update successfully.!');
        }
        return redirect('featured_sections');
    }
    public function delete()
    {
        if (is_modification_allowed()) {
            return $this->response->setJSON(FALSE);
        } else {
            $id = $this->request->getVar('id');
            $this->FeaturedSections_Model->where('id', $id)->delete();
            return $this->response->setJSON(TRUE);
        }
    }
    public function featured_sections_order()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            return view('featured_sections_order', $this->data);
        }
    }
    public function update_featured_sections_order()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            $i = 0;
            $temp = array();
            $flag = false;
            $allData = $this->request->getVar('allData');
            print_r($allData);
            foreach ($allData as $row) {
                $temp[$row] = $i;
                $data = [
                    'row_order' => $i
                ];
                
                $this->FeaturedSections_Model->update($row, $data);
                $i++;
                $flag = true;
            }
        }
    }

    public function get_featured_sections_by_language()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect('/');
        } else {
            $language_id = $this->request->getVar('language_id');
            $res = $this->FeaturedSections_Model->where('language_id', $language_id)->orderBy('id', 'DESC')->findAll();
            $option = '';
            if (!empty($res)) {
                foreach ($res as $value) {
                    $option .= '<option value="featuredsection-' . $value['id'] . '">Above ' . $value['title'] . '</option>';
                }
            }
            $option .= '<option value="news_details-top">News Details (Top)</option>';
            $option .= '<option value="news_details-bottom">News Details (Bottom)</option>';
            return $option;
        }
    }
}