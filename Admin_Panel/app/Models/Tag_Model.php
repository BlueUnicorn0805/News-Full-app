<?php

namespace App\Models;

use CodeIgniter\Model;

class Tag_Model extends Model
{

    protected $table = 'tbl_tag';
    protected $allowedFields = ['tag_name', 'language_id'];

}