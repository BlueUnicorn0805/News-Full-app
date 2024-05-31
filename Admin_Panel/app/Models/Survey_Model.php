<?php

namespace App\Models;

use CodeIgniter\Model;

class Survey_Model extends Model
{

    protected $table = 'tbl_survey_question';
    protected $allowedFields = ['question', 'status', 'language_id'];

}