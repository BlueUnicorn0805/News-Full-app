<?php

namespace App\Models;

use CodeIgniter\Model;

class Survey_Option_Model extends Model {

    protected $table = 'tbl_survey_option';
    protected $allowedFields = ['question_id', 'options','counter'];

}
