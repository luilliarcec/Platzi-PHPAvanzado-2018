<?php

use Phinx\Migration\AbstractMigration;

class CreateProjectsTecnologysTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('projects_details');
        $table
            ->addColumn('projects_id', 'string', [
                'null' => true
            ])
            ->addColumn('tecnologys_id', 'string', [
                'null' => true
            ])
            ->addForeignKey('projects_id', 'projects', 'id', ['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION'])
            ->addForeignKey('tecnologys_id', 'tecnologys', 'id', ['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION'])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime')
            ->create();
    }
}
