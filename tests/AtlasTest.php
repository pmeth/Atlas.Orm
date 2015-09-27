<?php
namespace Atlas;

use Atlas\Fake\Author\AuthorMapper;
use Atlas\Fake\Reply\ReplyMapper;
use Atlas\Fake\Summary\SummaryMapper;
use Atlas\Fake\Summary\SummaryTable;
use Atlas\Fake\Tag\TagMapper;
use Atlas\Fake\Thread2Tag\Thread2TagMapper;
use Atlas\Fake\Thread\ThreadMapper;
use Aura\Sql\ExtendedPdo;

class AtlasTest extends \PHPUnit_Framework_TestCase
{
    protected $atlas;

    protected function setUp()
    {
        $atlasContainer = new AtlasContainer('sqlite');
        $atlasContainer->setDefaultConnection(function () {
            return new ExtendedPdo('sqlite::memory:');
        });
        $atlasContainer->setMappers([
            AuthorMapper::CLASS,
            ReplyMapper::CLASS,
            SummaryMapper::CLASS,
            TagMapper::CLASS,
            ThreadMapper::CLASS,
            Thread2TagMapper::CLASS,
        ]);

        $connection = $atlasContainer->getConnectionLocator()->getDefault();
        $fixture = new SqliteFixture($connection);
        $fixture->exec();

        $this->atlas = $atlasContainer->getAtlas();
    }

    public function testSelect()
    {
        $select = $this->atlas->select(ThreadMapper::CLASS);
        $this->assertInstanceOf('Atlas\AtlasSelect', $select);
    }

    public function testFetchRecord()
    {
        $actual = $this->atlas->fetchRecord(
            ThreadMapper::CLASS,
            1,
            [
                'author', // manyToOne
                'summary', // oneToOne
                'replies' => function ($select) {
                    $select->with(['author']);
                }, // oneToMany
                'threads2tags', // oneToMany,
                'tags', // manyToMany
            ]
        );

        $expect = [
            'thread_id' => '1',
            'author_id' => '1',
            'subject' => 'Thread subject 1',
            'body' => 'Thread body 1',
            'author' => [
                'author_id' => '1',
                'name' => 'Anna',
                'replies' => null,
                'threads' => null,
            ],
            'summary' => [
                'thread_id' => '1',
                'reply_count' => '5',
                'view_count' => '0',
                'thread' => null,
            ],
            'replies' => [
                0 => [
                    'reply_id' => '1',
                    'thread_id' => '1',
                    'author_id' => '2',
                    'body' => 'Reply 1 on thread 1',
                    'author' => [
                        'author_id' => '2',
                        'name' => 'Betty',
                        'replies' => null,
                        'threads' => null,
                    ],
                ],
                1 => [
                    'reply_id' => '2',
                    'thread_id' => '1',
                    'author_id' => '3',
                    'body' => 'Reply 2 on thread 1',
                    'author' => [
                        'author_id' => '3',
                        'name' => 'Clara',
                        'replies' => null,
                        'threads' => null,
                    ],
                ],
                2 => [
                    'reply_id' => '3',
                    'thread_id' => '1',
                    'author_id' => '4',
                    'body' => 'Reply 3 on thread 1',
                    'author' => [
                        'author_id' => '4',
                        'name' => 'Donna',
                        'replies' => null,
                        'threads' => null,
                    ],
                ],
                3 => [
                    'reply_id' => '4',
                    'thread_id' => '1',
                    'author_id' => '5',
                    'body' => 'Reply 4 on thread 1',
                    'author' => [
                        'author_id' => '5',
                        'name' => 'Edna',
                        'replies' => null,
                        'threads' => null,
                    ],
                ],
                4 => [
                    'reply_id' => '5',
                    'thread_id' => '1',
                    'author_id' => '6',
                    'body' => 'Reply 5 on thread 1',
                    'author' => [
                        'author_id' => '6',
                        'name' => 'Fiona',
                        'replies' => null,
                        'threads' => null,
                    ],
                ],
            ],
            'threads2tags' => [
                0 => [
                    'thread2tag_id' => '1',
                    'thread_id' => '1',
                    'tag_id' => '1',
                ],
                1 => [
                    'thread2tag_id' => '2',
                    'thread_id' => '1',
                    'tag_id' => '2',
                ],
                2 => [
                    'thread2tag_id' => '3',
                    'thread_id' => '1',
                    'tag_id' => '3',
                ],
            ],
            'tags' => [
                0 => [
                    'tag_id' => '1',
                    'name' => 'foo',
                    'threads2tags' => null,
                    'threads' => null,
                ],
                1 => [
                    'tag_id' => '2',
                    'name' => 'bar',
                    'threads2tags' => null,
                    'threads' => null,
                ],
                2 => [
                    'tag_id' => '3',
                    'name' => 'baz',
                    'threads2tags' => null,
                    'threads' => null,
                ],
            ],
        ];

        $this->assertSame($expect, $actual->getArrayCopy());
    }

    public function testFetchRecordSet()
    {
        $actual = $this->atlas->fetchRecordSet(
            ThreadMapper::CLASS,
            [1, 2, 3],
            [
                'author', // manyToOne
                'summary', // oneToOne
                'replies' => function ($select) {
                    $select->with(['author']);
                }, // oneToMany
                'threads2tags', // oneToMany,
                'tags', // manyToMany
            ]
        );

        $expect = [
            0 => [
                'thread_id' => '1',
                'author_id' => '1',
                'subject' => 'Thread subject 1',
                'body' => 'Thread body 1',
                'author' => [
                    'author_id' => '1',
                    'name' => 'Anna',
                    'replies' => null,
                    'threads' => null,
                ],
                'summary' => [
                    'thread_id' => '1',
                    'reply_count' => '5',
                    'view_count' => '0',
                    'thread' => null,
                ],
                'replies' => [
                    0 => [
                        'reply_id' => '1',
                        'thread_id' => '1',
                        'author_id' => '2',
                        'body' => 'Reply 1 on thread 1',
                        'author' => [
                            'author_id' => '2',
                            'name' => 'Betty',
                            'replies' => null,
                            'threads' => null,
                        ],
                    ],
                    1 => [
                        'reply_id' => '2',
                        'thread_id' => '1',
                        'author_id' => '3',
                        'body' => 'Reply 2 on thread 1',
                        'author' => [
                            'author_id' => '3',
                            'name' => 'Clara',
                            'replies' => null,
                            'threads' => null,
                        ],
                    ],
                    2 => [
                        'reply_id' => '3',
                        'thread_id' => '1',
                        'author_id' => '4',
                        'body' => 'Reply 3 on thread 1',
                        'author' => [
                            'author_id' => '4',
                            'name' => 'Donna',
                            'replies' => null,
                            'threads' => null,
                        ],
                    ],
                    3 => [
                        'reply_id' => '4',
                        'thread_id' => '1',
                        'author_id' => '5',
                        'body' => 'Reply 4 on thread 1',
                        'author' => [
                            'author_id' => '5',
                            'name' => 'Edna',
                            'replies' => null,
                            'threads' => null,
                        ],
                    ],
                    4 => [
                        'reply_id' => '5',
                        'thread_id' => '1',
                        'author_id' => '6',
                        'body' => 'Reply 5 on thread 1',
                        'author' => [
                            'author_id' => '6',
                            'name' => 'Fiona',
                            'replies' => null,
                            'threads' => null,
                        ],
                    ],
                ],
                'threads2tags' => [
                    0 => [
                        'thread2tag_id' => '1',
                        'thread_id' => '1',
                        'tag_id' => '1',
                    ],
                    1 => [
                        'thread2tag_id' => '2',
                        'thread_id' => '1',
                        'tag_id' => '2',
                    ],
                    2 => [
                        'thread2tag_id' => '3',
                        'thread_id' => '1',
                        'tag_id' => '3',
                    ],
                ],
                'tags' => [
                    0 => [
                        'tag_id' => '1',
                        'name' => 'foo',
                        'threads2tags' => null,
                        'threads' => null,
                    ],
                    1 => [
                        'tag_id' => '2',
                        'name' => 'bar',
                        'threads2tags' => null,
                        'threads' => null,
                    ],
                    2 => [
                        'tag_id' => '3',
                        'name' => 'baz',
                        'threads2tags' => null,
                        'threads' => null,
                    ],
                ],
            ],
            1 => [
                'thread_id' => '2',
                'author_id' => '2',
                'subject' => 'Thread subject 2',
                'body' => 'Thread body 2',
                'author' => [
                    'author_id' => '2',
                    'name' => 'Betty',
                    'replies' => null,
                    'threads' => null,
                ],
                'summary' => [
                    'thread_id' => '2',
                    'reply_count' => '5',
                    'view_count' => '0',
                    'thread' => null,
                ],
                'replies' => [
                    0 => [
                        'reply_id' => '6',
                        'thread_id' => '2',
                        'author_id' => '3',
                        'body' => 'Reply 1 on thread 2',
                        'author' => [
                            'author_id' => '3',
                            'name' => 'Clara',
                            'replies' => null,
                            'threads' => null,
                        ],
                    ],
                    1 => [
                        'reply_id' => '7',
                        'thread_id' => '2',
                        'author_id' => '4',
                        'body' => 'Reply 2 on thread 2',
                        'author' => [
                            'author_id' => '4',
                            'name' => 'Donna',
                            'replies' => null,
                            'threads' => null,
                        ],
                    ],
                    2 => [
                        'reply_id' => '8',
                        'thread_id' => '2',
                        'author_id' => '5',
                        'body' => 'Reply 3 on thread 2',
                        'author' => [
                            'author_id' => '5',
                            'name' => 'Edna',
                            'replies' => null,
                            'threads' => null,
                        ],
                    ],
                    3 => [
                        'reply_id' => '9',
                        'thread_id' => '2',
                        'author_id' => '6',
                        'body' => 'Reply 4 on thread 2',
                        'author' => [
                            'author_id' => '6',
                            'name' => 'Fiona',
                            'replies' => null,
                            'threads' => null,
                        ],
                    ],
                    4 => [
                        'reply_id' => '10',
                        'thread_id' => '2',
                        'author_id' => '7',
                        'body' => 'Reply 5 on thread 2',
                        'author' => [
                            'author_id' => '7',
                            'name' => 'Gina',
                            'replies' => null,
                            'threads' => null,
                        ],
                    ],
                ],
                'threads2tags' => [
                    0 => [
                        'thread2tag_id' => '4',
                        'thread_id' => '2',
                        'tag_id' => '2',
                    ],
                    1 => [
                        'thread2tag_id' => '5',
                        'thread_id' => '2',
                        'tag_id' => '3',
                    ],
                    2 => [
                        'thread2tag_id' => '6',
                        'thread_id' => '2',
                        'tag_id' => '4',
                    ],
                ],
                'tags' => [
                    0 => [
                        'tag_id' => '2',
                        'name' => 'bar',
                        'threads2tags' => null,
                        'threads' => null,
                    ],
                    1 => [
                        'tag_id' => '3',
                        'name' => 'baz',
                        'threads2tags' => null,
                        'threads' => null,
                    ],
                    2 => [
                        'tag_id' => '4',
                        'name' => 'dib',
                        'threads2tags' => null,
                        'threads' => null,
                    ],
                ],
            ],
            2 => [
                'thread_id' => '3',
                'author_id' => '3',
                'subject' => 'Thread subject 3',
                'body' => 'Thread body 3',
                'author' => [
                    'author_id' => '3',
                    'name' => 'Clara',
                    'replies' => null,
                    'threads' => null,
                ],
                'summary' => [
                    'thread_id' => '3',
                    'reply_count' => '5',
                    'view_count' => '0',
                    'thread' => null,
                ],
                'replies' => [
                    0 => [
                        'reply_id' => '11',
                        'thread_id' => '3',
                        'author_id' => '4',
                        'body' => 'Reply 1 on thread 3',
                        'author' => [
                            'author_id' => '4',
                            'name' => 'Donna',
                            'replies' => null,
                            'threads' => null,
                        ],
                    ],
                    1 => [
                        'reply_id' => '12',
                        'thread_id' => '3',
                        'author_id' => '5',
                        'body' => 'Reply 2 on thread 3',
                        'author' => [
                            'author_id' => '5',
                            'name' => 'Edna',
                            'replies' => null,
                            'threads' => null,
                        ],
                    ],
                    2 => [
                        'reply_id' => '13',
                        'thread_id' => '3',
                        'author_id' => '6',
                        'body' => 'Reply 3 on thread 3',
                        'author' => [
                            'author_id' => '6',
                            'name' => 'Fiona',
                            'replies' => null,
                            'threads' => null,
                        ],
                    ],
                    3 => [
                        'reply_id' => '14',
                        'thread_id' => '3',
                        'author_id' => '7',
                        'body' => 'Reply 4 on thread 3',
                        'author' => [
                            'author_id' => '7',
                            'name' => 'Gina',
                            'replies' => null,
                            'threads' => null,
                        ],
                    ],
                    4 => [
                        'reply_id' => '15',
                        'thread_id' => '3',
                        'author_id' => '8',
                        'body' => 'Reply 5 on thread 3',
                        'author' => [
                            'author_id' => '8',
                            'name' => 'Hanna',
                            'replies' => null,
                            'threads' => null,
                        ],
                    ],
                ],
                'threads2tags' => [
                    0 => [
                        'thread2tag_id' => '7',
                        'thread_id' => '3',
                        'tag_id' => '3',
                    ],
                    1 => [
                        'thread2tag_id' => '8',
                        'thread_id' => '3',
                        'tag_id' => '4',
                    ],
                    2 => [
                        'thread2tag_id' => '9',
                        'thread_id' => '3',
                        'tag_id' => '5',
                    ],
                ],
                'tags' => [
                    0 => [
                        'tag_id' => '3',
                        'name' => 'baz',
                        'threads2tags' => null,
                        'threads' => null,
                    ],
                    1 => [
                        'tag_id' => '4',
                        'name' => 'dib',
                        'threads2tags' => null,
                        'threads' => null,
                    ],
                    2 => [
                        'tag_id' => '5',
                        'name' => 'zim',
                        'threads2tags' => null,
                        'threads' => null,
                    ],
                ],
            ],
        ];

        $this->assertSame($expect, $actual->getArrayCopy());
    }
}