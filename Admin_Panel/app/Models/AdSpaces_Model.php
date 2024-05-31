<?php

namespace App\Models;

use CodeIgniter\Model;

class AdSpaces_Model extends Model
{

    protected $table = 'tbl_ad_spaces';
    protected $allowedFields = ['ad_space', 'ad_featured_section_id', 'ad_image', 'web_ad_image', 'ad_url', 'language_id', 'created_at', 'status'];

}