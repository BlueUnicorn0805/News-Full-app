<?php
namespace App\Models;

use CodeIgniter\Model;

class Language_Model extends Model
{
    protected $table = 'tbl_languages';
    protected $allowedFields = ['language', 'code', 'status', 'type', 'isRTL', 'image', 'display_name'];
}