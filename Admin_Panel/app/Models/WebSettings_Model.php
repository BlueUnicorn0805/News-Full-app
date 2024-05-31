<?php

namespace App\Models;

use CodeIgniter\Model;

class WebSettings_Model extends Model
{

    protected $table = 'tbl_web_settings';
    protected $allowedFields = ['type', 'message'];

}