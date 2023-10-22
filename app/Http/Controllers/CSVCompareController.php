<?php

namespace App\Http\Controllers;

use App\Http\Requests\CSVUploadRequest;
use App\Services\CSVCompareService;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\UnavailableStream;

class CSVCompareController extends Controller
{
    protected $service;

    public function __construct(CSVCompareService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return view('index');
    }

    /**
     * @throws InvalidArgument
     * @throws UnavailableStream
     * @throws Exception
     */
    public function uploadFiles(CSVUploadRequest $request)
    {
        $oldCsv = $request->file('old_csv');
        $newCsv = $request->file('new_csv');
        $csvComparison = $this->service->compare($oldCsv, $newCsv);
        if ($csvComparison == null) {
            return redirect()->back()->withErrors(['Problem validating the CSV. Please check your headers']);
        }
        return view('result', compact('csvComparison'));
    }
}
