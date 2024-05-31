<?php

namespace App\Models;

use CodeIgniter\Model;

class Survey_Result_Model extends Model {

    protected $table = 'tbl_survey_result';
    protected $allowedFields = ['question_id', 'option_id', 'user_id'];

}

