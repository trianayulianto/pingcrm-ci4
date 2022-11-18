<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContactsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                => ['type' => 'bigint', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'account_id'        => ['type' => 'bigint', 'constraint' => 20, 'unsigned' => true],
            'organization_id'   => ['type' => 'bigint', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'first_name'        => ['type' => 'varchar', 'constraint' => 25],
            'last_name'         => ['type' => 'varchar', 'constraint' => 25],
            'email'             => ['type' => 'varchar', 'constraint' => 50],
            'phone'             => ['type' => 'varchar', 'constraint' => 50],
            'address'           => ['type' => 'varchar', 'constraint' => 150],
            'city'              => ['type' => 'varchar', 'constraint' => 50],
            'region'            => ['type' => 'varchar', 'constraint' => 50],
            'country'           => ['type' => 'varchar', 'constraint' => 2],
            'postal_code'       => ['type' => 'varchar', 'constraint' => 25],
            'created_at'        => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'        => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_at'        => ['type' => 'TIMESTAMP', 'null' => true],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('account_id');

        $this->forge->createTable('contacts', true);
    }

    public function down()
    {
        $this->forge->dropTable('contacts', true);
    }
}
