<?php

namespace App\Models;

use CodeIgniter\Model;

class Pages_Model extends Model
{

    protected $table = 'tbl_pages';
    protected $allowedFields = ['title', 'slug', 'meta_description', 'meta_keywords', 'language_id', 'page_content', 'is_custom', 'page_icon', 'is_termspolicy', 'is_privacypolicy', 'status'];

}