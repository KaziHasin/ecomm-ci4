<?php

namespace App\Models;

use CodeIgniter\Model;

class Product extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'products';
    protected $primaryKey       = 'id';
 
    protected $allowedFields = ['product_name', 'product_description', 'product_price', 'product_quantity', 'product_image'];
    protected $useTimestamps = true;
}
