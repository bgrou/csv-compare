<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\Reader;
use League\Csv\UnavailableStream;

class CSVCompareService
{
    /**
     * @throws UnavailableStream
     * @throws InvalidArgument
     * @throws Exception
     */
    public function compare(UploadedFile $oldFile, UploadedFile $newFile): ?array
    {
        $streamOldFile = $this->getStream($oldFile->getPathname());
        $streamNewFile = $this->getStream($newFile->getPathname());
        [$headersOldFile, $headersNewFile] = $this->getHeaders($streamOldFile, $streamNewFile);

        if (!$this->validateHeaders($headersOldFile, $headersNewFile)) {
            return null;
        }

        return $this->findDifferences($headersOldFile, $streamOldFile, $streamNewFile);
    }

    /**
     * @throws Exception
     */
    private function getHeaders(Reader ...$readers): array
    {
        $headers = [];
        foreach ($readers as $reader) {
            $header = $reader->getHeader();
            if (!empty($header)) {
                $headers[] = $header;
            }
        }
        return $headers;
    }

    private function validateHeaders(array $headersOldFile, array $headersNewFile): bool
    {
        return !empty($headersOldFile) &&
            !empty($headersNewFile) &&
            count(array_diff($headersOldFile, $headersNewFile)) === 0;
    }

    /**
     * @throws UnavailableStream
     * @throws InvalidArgument
     * @throws Exception
     */
    private function getStream($filePathname): Reader
    {
        return Reader::createFromPath($filePathname)
            ->setHeaderOffset(0)
            ->setDelimiter(';');
    }

    /**
     */
    private function findDifferences(array $headers, Reader $streamOldFile, Reader $streamNewFile): array
    {
        $newRecordIndex = 1;
        $newCount = 0;
        $alterCount = 0;
        $equalCount = 0;
        $output[] = $headers;

        foreach ($streamOldFile as $index => $oldRecord) {
            $newRecord = $streamNewFile->fetchOne($newRecordIndex - 1);
            unset($newRecord['']);
            unset($oldRecord['']);
            $tempDiffs = array_diff($newRecord, $oldRecord);
            if (count($tempDiffs) === count($oldRecord)) {
                do {
                    $output[] = [
                        'old_index' => ' -',
                        'new_index' => $newRecordIndex,
                        'record' => $newRecord,
                        'diffs' => [],
                        'type' => 'new'
                    ];

                    $newRecord = $streamNewFile->fetchOne(++$newRecordIndex - 1);
                    unset($newRecord['']);
                    $tempDiffs = array_diff($newRecord, $oldRecord);
                    $newCount++;
                } while (count($tempDiffs) !== 0);

                $output[] = [
                    'old_index' => $index,
                    'new_index' => $newRecordIndex,
                    'record' => $oldRecord,
                    'diffs' => $tempDiffs,
                    'type' => 'equal'
                ];
            } else {
                if (count($tempDiffs) === 0) {
                    $equalCount++;
                    $type = "equal";
                } else {
                    $alterCount++;
                    $type = "alter";
                }

                $output[] = array_merge(
                    [
                        'old_index' => $index,
                        'new_index' => $newRecordIndex,
                        'record' => $oldRecord,
                        'diffs' => $tempDiffs,
                        'type' => $type
                    ]
                );
            }
            $newRecordIndex++;
        }

        return array_merge(
            ['records' => $output, 'new_count' => $newCount, 'alter_count' => $alterCount, 'equal_count' => $equalCount]
        );
    }
}
