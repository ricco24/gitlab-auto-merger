<?php

use Phinx\Migration\AbstractMigration;

class Initialization extends AbstractMigration
{
    public function change()
    {
        $this->table('settings')
            ->addColumn('gitlab_url', 'string', ['limit' => 1000])
            ->addColumn('gitlab_merger_url', 'string', ['null' => true, 'limit' => 1000])
            ->addColumn('token', 'string')
            ->create();

        $this->table('project')
            ->addColumn('gitlab_id', 'integer')
            ->addColumn('url', 'string', ['limit' => 1000])
            ->addColumn('name', 'string')
            ->addColumn('positive_votes', 'integer')
            ->addColumn('delete_source_branch', 'boolean', ['default' => false])
            ->create();
    }
}
