<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class MyProject extends AbstractMigration
{

    public function change(): void
    {
        // Create table without hash column
        $table = $this->table('routim_project', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci']);
        $table->addColumn('keyword', 'string', ['limit' => 255, 'collation' => 'utf8mb4_unicode_ci', 'null' => true])
            ->addColumn('json', 'json', ['null' => true])
            ->create();
        // Create hash column and unique index
        $this->execute('ALTER TABLE `routim_project` ADD `hash` CHAR(32) COLLATE utf8mb4_unicode_ci GENERATED ALWAYS AS (md5(`json`)) VIRTUAL');
        $this->execute('ALTER TABLE `routim_project` ADD UNIQUE INDEX `idx_unique_json_hash` (`hash`)');
    }
}
