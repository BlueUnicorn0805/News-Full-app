<?php

namespace App\Models;

use CodeIgniter\Model;

class News_Model extends Model
{

    protected $table = 'tbl_news';
    protected $allowedFields = ['category_id', 'subcategory_id', 'tag_id', 'title', 'date', 'content_type', 'content_value', 'image', 'description', 'user_id', 'admin_id', 'show_till', 'status', 'language_id', 'is_clone'];

}