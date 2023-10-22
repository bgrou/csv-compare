<?php

namespace App\Helpers;

class ComparisonData
{
    private $newRecord = []; //New CSV file current record
    private $oldRecord = []; //Old CSV file current record
    private $newRecordIndex = 1; //New CSV file currently compared index
    private $oldRecordIndex = 1; //Old CSV file currently compared index
    private $newCount = 0; //New records count
    private $alterCount = 0; //Altered records count
    private $equalCount = 0; //Equal records count
    private $output = []; //Output to be returned

    public function __construct($headers)
    {
        $this->output = [$headers];
    }

    public function getNewRecord(): array
    {
        return $this->newRecord;
    }

    public function setNewRecord(array $newRecord): void
    {
        unset($newRecord['']);
        $this->newRecord = $newRecord;
    }

    public function getOldRecord(): array
    {

        return $this->oldRecord;
    }

    public function setOldRecord(array $oldRecord): void
    {
        unset($oldRecord['']);
        $this->oldRecord = $oldRecord;
    }

    public function incrementNewCounter(): void
    {
        $this->newCount++;
    }

    public function incrementAlterCounter(): void
    {
        $this->alterCount++;
    }

    public function incrementEqualCounter(): void
    {
        $this->equalCount++;
    }

    public function incrementNewIndex(): void
    {
        $this->newRecordIndex++;
    }

    public function incrementOldIndex(): void
    {
        $this->oldRecordIndex++;
    }

    public function getOutput(): array
    {
        return $this->output;
    }

    public function getNewRecordIndex(): int
    {
        return $this->newRecordIndex;
    }

    public function getOldRecordIndex(): int
    {
        return $this->oldRecordIndex;
    }

    public function addOutput($data): void
    {
        $this->output[] = $data;
    }

    public function mergeOutput(): array
    {
        return [
            'records' => $this->output,
            'new_count' => $this->newCount,
            'alter_count' => $this->alterCount,
            'equal_count' => $this->equalCount,
        ];
    }


}
