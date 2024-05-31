<?php

namespace App\Models;

use CodeIgniter\Model;

class Notification_Model extends Model {

    protected $table = 'tbl_notifications';
    protected $allowedFields = ['title', 'message', 'type', 'category_id', 'subcategory_id', 'news_id', 'image', 'date_sent', 'language_id'];

}
