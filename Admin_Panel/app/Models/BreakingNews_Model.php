<?php

namespace App\Models;

use CodeIgniter\Model;

class BreakingNews_Model extends Model {

    protected $table = 'tbl_breaking_news';
    protected $allowedFields = ['title', 'image', 'content_type', 'content_value', 'description','language_id'];

}
