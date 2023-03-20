<?php

namespace App\Models;

use CodeIgniter\Model;

class User extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'users';
    protected $primaryKey       = 'id';
 
    protected $allowedFields = ['username', 'email', 'image', 'password'];
    protected $useTimestamps = true;
}
