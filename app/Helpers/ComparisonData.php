<?php

namespace App\Helpers;

/**
 * Class ComparisonData
 *
 * Helper class for comparing data from two CSV files.
 */
class ComparisonData
{
    /**
     * @var array New CSV file current record
     */
    private $newRecord = [];

    /**
     * @var array Old CSV file current record
     */
    private $oldRecord = [];

    /**
     * @var int New CSV file currently compared index
     */
    private $newRecordIndex = 1;

    /**
     * @var int Old CSV file currently compared index
     */
    private $oldRecordIndex = 1;

    /**
     * @var int New records count
     */
    private $newCount = 0;

    /**
     * @var int Altered records count
     */
    private $alterCount = 0;

    /**
     * @var int Equal records count
     */
    private $equalCount = 0;

    /**
     * @var array Output to be returned
     */
    private $output = [];

    /**
     * ComparisonData constructor.
     *
     * @param array $headers The headers of the CSV files.
     */
    public function __construct($headers)
    {
        $this->output = [$headers];
    }

    /**
     * Get the current record from the new CSV file.
     *
     * @return array The current new CSV record.
     */
    public function getNewRecord(): array
    {
        return $this->newRecord;
    }

    /**
     * Set the current record for the new CSV file.
     *
     * @param array $newRecord The new CSV record.
     */
    public function setNewRecord(array $newRecord): void
    {
        unset($newRecord['']);
        $this->newRecord = $newRecord;
    }

    /**
     * Get the current record from the old CSV file.
     *
     * @return array The current old CSV record.
     */
    public function getOldRecord(): array
    {
        return $this->oldRecord;
    }

    /**
     * Set the current record for the old CSV file.
     *
     * @param array $oldRecord The old CSV record.
     */
    public function setOldRecord(array $oldRecord): void
    {
        unset($oldRecord['']);
        $this->oldRecord = $oldRecord;
    }

    /**
     * Increment the counter for new records.
     */
    public function incrementNewCounter(): void
    {
        $this->newCount++;
    }

    /**
     * Increment the counter for altered records.
     */
    public function incrementAlterCounter(): void
    {
        $this->alterCount++;
    }

    /**
     * Increment the counter for equal records.
     */
    public function incrementEqualCounter(): void
    {
        $this->equalCount++;
    }

    /**
     * Increment the index for new records.
     */
    public function incrementNewIndex(): void
    {
        $this->newRecordIndex++;
    }

    /**
     * Increment the index for old records.
     */
    public function incrementOldIndex(): void
    {
        $this->oldRecordIndex++;
    }

    /**
     * Get the output data.
     *
     * @return array The output data.
     */
    public function getOutput(): array
    {
        return $this->output;
    }

    /**
     * Get the index of the current record in the new CSV file.
     *
     * @return int The index of the current new CSV record.
     */
    public function getNewRecordIndex(): int
    {
        return $this->newRecordIndex;
    }

    /**
     * Get the index of the current record in the old CSV file.
     *
     * @return int The index of the current old CSV record.
     */
    public function getOldRecordIndex(): int
    {
        return $this->oldRecordIndex;
    }

    /**
     * Add data to the output.
     *
     * @param mixed $data The data to add to the output.
     */
    public function addOutput($data): void
    {
        $this->output[] = $data;
    }

    /**
     * Merge the output data and counts for new, altered, and equal records.
     *
     * @return array The merged output data and counts.
     */
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
