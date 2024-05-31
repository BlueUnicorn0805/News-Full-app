<?php

namespace App\Models;

use CodeIgniter\Model;

class Category_Model extends Model
{

    protected $table = 'tbl_category';
    protected $allowedFields = ['category_name', 'image', 'language_id'];



}