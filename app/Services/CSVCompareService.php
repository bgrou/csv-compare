<?php

namespace App\Services;

use App\Helpers\ComparisonData;
use Illuminate\Http\UploadedFile;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\Reader;
use League\Csv\UnavailableStream;

class CSVCompareService
{
    /**
     * Compare two CSV files and return their differences.
     *
     * @param UploadedFile $oldFile The old CSV file.
     * @param UploadedFile $newFile The new CSV file.
     *
     * @return array|null The differences between the two CSV files or null if headers are invalid.
     *
     * @throws UnavailableStream If the stream is unavailable.
     * @throws InvalidArgument If an invalid argument is provided.
     * @throws Exception For other generic exceptions.
     */
    public function compare(UploadedFile $oldFile, UploadedFile $newFile): ?array
    {
        if ($oldFile->getClientOriginalExtension() !== 'csv' || $newFile->getClientOriginalExtension() !== 'csv') {
            throw new Exception('Only CSV files are allowed.');
        }

        $streamOldFile = $this->getStream($oldFile->getPathname());
        $streamNewFile = $this->getStream($newFile->getPathname());
        [$headersOldFile, $headersNewFile] = $this->getHeaders($streamOldFile, $streamNewFile);

        if (!$this->areHeadersValid($headersOldFile, $headersNewFile)) {
            return null;
        }

        $comparisonData = new ComparisonData($headersOldFile);

        return $this->findDifferences($comparisonData, $streamOldFile, $streamNewFile);
    }

    /**
     * Get the CSV Reader object for a given file path.
     *
     * @param string $filePathname The path of the CSV file.
     *
     * @return Reader The Reader object for the CSV file.
     *
     * @throws UnavailableStream If the stream is unavailable.
     * @throws InvalidArgument If an invalid argument is provided.
     * @throws Exception If the CSV file is empty.
     */
    private function getStream(string $filePathname): Reader
    {
        $reader = Reader::createFromPath($filePathname)
            ->setHeaderOffset(0)
            ->setDelimiter(';');

        if (!$reader->count()) {
            throw new Exception('The CSV file is empty.');
        }

        return $reader;
    }

    /**
     * Extract and return the headers from the CSV readers.
     *
     * @param Reader ...$readers CSV Reader Objects
     *
     * @return array The headers extracted from the CSV files.
     *
     * @throws Exception If the CSV file does not have a header.
     */
    private function getHeaders(Reader ...$readers): array
    {
        $headers = [];
        foreach ($readers as $reader) {
            $header = $reader->getHeader();

            if (empty($header)) {
                throw new Exception('The CSV file does not have a header.');
            }
            unset($header['']);
            $headers[] = $header;
        }
        return $headers;
    }

    /**
     * Validate the headers of both CSV files.
     *
     * @param array $headersOldFile The headers of the old CSV file.
     * @param array $headersNewFile The headers of the new CSV file.
     *
     * @return bool True if headers are valid, otherwise an exception is thrown.
     *
     * @throws Exception If the number of columns or headers in the CSV files do not match.
     */
    private function areHeadersValid(array $headersOldFile, array $headersNewFile): bool
    {
        if (count($headersOldFile) !== count($headersNewFile)) {
            throw new Exception('The number of columns in the CSV files do not match.');
        }

        if (count(array_diff($headersOldFile, $headersNewFile)) !== 0) {
            throw new Exception('The headers in the CSV files do not match.');
        }

        return true;
    }

    /**
     * Find and return the differences between the records of two CSV files.
     *
     * @param ComparisonData $comparisonData The data structure to hold the comparison data.
     * @param Reader $streamOldFile The reader object for the old CSV file.
     * @param Reader $streamNewFile The reader object for the new CSV file.
     *
     * @return array The differences between the two CSV files.
     */
    private function findDifferences(
        ComparisonData $comparisonData,
        Reader $streamOldFile,
        Reader $streamNewFile
    ): array {
        foreach ($streamOldFile as $index => $oldRecord) {
            $comparisonData->setOldRecord($oldRecord);
            $newRecord = $streamNewFile->fetchOne($comparisonData->getNewRecordIndex() - 1);
            $comparisonData->setNewRecord($newRecord);
            $diffs = array_diff($comparisonData->getNewRecord(), $comparisonData->getOldRecord());

            // If all values are different, consider it a new record.
            if (count($diffs) === count($comparisonData->getOldRecord())) {
                $this->handleNewRecords($comparisonData, $diffs, $streamNewFile);
            } else {
                // Otherwise, consider the records as modified or equal.
                $this->handleAlteredAndEqualRecords($comparisonData, $diffs);
            }

            $comparisonData->incrementNewIndex();
            $comparisonData->incrementOldIndex();
        }

        return $comparisonData->mergeOutput();
    }

    /**
     * Handle new records and add them to the comparison data.
     *
     * @param ComparisonData $comparisonData The data structure to hold the comparison data.
     * @param array $diffs The differences between the old and new records.
     * @param Reader $streamNewFile The reader object for the new CSV file.
     */
    private function handleNewRecords(ComparisonData $comparisonData, array $diffs, Reader $streamNewFile): void
    {
        do {
            $this->addOutput($comparisonData, $diffs, 'new');
            $comparisonData->incrementNewCounter();
            $comparisonData->incrementNewIndex();
            $newRecord = $streamNewFile->fetchOne($comparisonData->getNewRecordIndex() - 1);
            $comparisonData->setNewRecord($newRecord);
            $diffs = array_diff($comparisonData->getNewRecord(), $comparisonData->getOldRecord());
        } while (count($diffs) !== 0); // Continue until the old record is found in the new file

        $this->addOutput($comparisonData, $diffs, 'equal'); // Add that old record to output
        $comparisonData->incrementEqualCounter();
    }

    /**
     * Handle altered and equal records and add them to the comparison data.
     *
     * @param ComparisonData $comparisonData The data structure to hold the comparison data.
     * @param array $diffs The differences between the old and new records.
     */
    private function handleAlteredAndEqualRecords(ComparisonData $comparisonData, array $diffs): void
    {
        if (count($diffs) === 0) {
            $type = "equal";
            $comparisonData->incrementEqualCounter();
        } else {
            $type = "alter";
            $comparisonData->incrementAlterCounter();
        }

        $this->addOutput($comparisonData, $diffs, $type);
    }

    /**
     * Add the output of the comparison to the comparison data.
     *
     * @param ComparisonData $comparisonData The data structure to hold the comparison data.
     * @param array $diffs The differences between the old and new record.
     * @param string $type The type of record (new, altered, or equal).
     */
    private function addOutput(ComparisonData $comparisonData, array $diffs, string $type): void
    {
        $comparisonData->addOutput([
            'old_index' => $type === 'new' ? ' -' : $comparisonData->getOldRecordIndex(),
            'new_index' => $comparisonData->getNewRecordIndex(),
            'record' => $type === 'new' ? $comparisonData->getNewRecord() : $comparisonData->getOldRecord(),
            'diffs' => $type === 'new' ? [] : $diffs,
            'type' => $type
        ]);
    }
}
