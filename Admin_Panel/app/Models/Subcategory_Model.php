<?php

namespace App\Models;

use CodeIgniter\Model;

class Subcategory_Model extends Model
{
    protected $table = 'tbl_subcategory';
    protected $allowedFields = ['category_id', 'subcategory_name', 'image', 'language_id'];

}