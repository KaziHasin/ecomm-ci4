<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'product_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'product_description' => [
                'type' => 'TEXT',
            ],
            'product_price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'product_quantity' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'product_image' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
             
           'product_category' => [
            'type' => 'VARCHAR',
            'constraint' => 255,
            'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('products');
    }

    public function down()
    {
        $this->forge->dropTable('products');
    }
}
