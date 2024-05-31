<?php

namespace App\Models;

use CodeIgniter\Model;

class LiveStreaming_Model extends Model
{

    protected $table = 'tbl_live_streaming';
    protected $allowedFields = ['title', 'image', 'type', 'url', 'language_id'];

}