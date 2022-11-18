<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                => ['type' => 'bigint', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'account_id'        => ['type' => 'bigint', 'constraint' => 20, 'unsigned' => true],
            'first_name'        => ['type' => 'varchar', 'constraint' => 25],
            'last_name'         => ['type' => 'varchar', 'constraint' => 25],
            'email'             => ['type' => 'varchar', 'constraint' => 50],
            'password'          => ['type' => 'varchar', 'constraint' => 255],
            'email_verified_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'owner'             => ['type' => 'boolean', 'default' => false],
            'photo_path'        => ['type' => 'varchar', 'constraint' => 100, 'null' => true],
            'remember_token'    => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'created_at'        => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'        => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_at'        => ['type' => 'TIMESTAMP', 'null' => true],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('account_id');
        $this->forge->addUniqueKey('email');

        $this->forge->createTable('users', true);
    }

    public function down()
    {
        $this->forge->dropTable('users', true);
    }
}
