<?php namespace App\Models;
 
use CodeIgniter\Model;
 
class Login_Model extends Model {
    
  protected $table = 'admin';
  
  protected $allowedFields = ['username', 'email', 'password', 'forgot_unique_code', 'forgot_at'];


  
  
 
}