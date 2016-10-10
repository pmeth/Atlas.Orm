<?php
/**
 *
 * This file is part of Atlas for PHP.
 *
 * @package atlas/orm
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Atlas\Orm\Relationship;

use Atlas\Orm\Mapper\RecordInterface;

class OneToMany extends AbstractRelationship
{
    protected function stitchIntoRecord(
        RecordInterface $nativeRecord,
        array $foreignRecords
    ) {
        $nativeRecord->{$this->name} = [];
        $matches = [];
        foreach ($foreignRecords as $foreignRecord) {
            if ($this->recordsMatch($nativeRecord, $foreignRecord)) {
                $matches[] = $foreignRecord;
            }
        }
        if ($matches) {
            $nativeRecord->{$this->name} = $this->foreignMapper->newRecordSet($matches);
        }
    }
}
