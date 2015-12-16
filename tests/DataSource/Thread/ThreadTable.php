<?php
namespace Atlas\Orm\DataSource\Thread;

use Atlas\Orm\Table\AbstractTable;

class ThreadTable extends AbstractTable
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'threads';
    }

    /**
     * @inheritdoc
     */
    public function getColNames()
    {
        return [
            'thread_id',
            'author_id',
            'subject',
            'body',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getCols()
    {
        return [
            'thread_id' => (object) [
                'name' => 'thread_id',
                'type' => 'integer',
                'size' => null,
                'scale' => null,
                'notnull' => false,
                'default' => null,
                'autoinc' => true,
                'primary' => true,
            ],
            'author_id' => (object) [
                'name' => 'author_id',
                'type' => 'integer',
                'size' => null,
                'scale' => null,
                'notnull' => true,
                'default' => null,
                'autoinc' => false,
                'primary' => false,
            ],
            'subject' => (object) [
                'name' => 'subject',
                'type' => 'varchar',
                'size' => 255,
                'scale' => null,
                'notnull' => true,
                'default' => null,
                'autoinc' => false,
                'primary' => false,
            ],
            'body' => (object) [
                'name' => 'body',
                'type' => 'text',
                'size' => null,
                'scale' => null,
                'notnull' => true,
                'default' => null,
                'autoinc' => false,
                'primary' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getPrimaryKey()
    {
        return 'thread_id';
    }

    /**
     * @inheritdoc
     */
    public function getAutoinc()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getColDefaults()
    {
        return [
            'thread_id' => null,
            'author_id' => null,
            'subject' => null,
            'body' => null,
        ];
    }
}
