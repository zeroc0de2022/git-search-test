<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class MyProject extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change():void
    {
        // Создание таблицы projects
        $table = $this->table('projects', ['id' => false, 'primary_key' => 'id', 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
        $table->addColumn('id', 'integer', ['identity' => true])
              ->addColumn('keyword', 'string', ['limit' => 255, 'collation' => 'utf8mb4_unicode_ci'])
              ->addColumn('json', 'json')
              ->addColumn('hash', 'char', ['limit' => 32, 'collation' => 'utf8mb4_unicode_ci'])
              ->addIndex(['hash'], ['unique' => true, 'name' => 'idx_unique_json_hash'])
              ->create();
    }
}
