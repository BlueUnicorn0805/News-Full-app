<?php

namespace App\Models;

use CodeIgniter\Model;

class UserRoles_Model extends Model {

    protected $table = 'tbl_user_roles';
    protected $allowedFields = ['role'];

}
