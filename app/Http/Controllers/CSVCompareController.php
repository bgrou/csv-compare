<?php

namespace App\Http\Controllers;

use App\Http\Requests\CSVUploadRequest;
use App\Services\CSVCompareService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use League\Csv\Exception;

class CSVCompareController extends Controller
{

    public function __construct(
        protected CSVCompareService $service
    ) {
    }

    /**
     * Show the initial index page.
     */
    public function index()
    {
        return view('index');
    }

    /**
     * Handle the uploaded CSV files, compare them and show the comparison results.
     *
     * @param CSVUploadRequest $request The validated request containing the uploaded CSV files.
     *
     * @return View|RedirectResponse
     *         The result view with the comparison data or a redirect back with error messages.
     */
    public function uploadFiles(CSVUploadRequest $request): View|RedirectResponse
    {
        try {
            $oldCsv = $request->file('old_csv');
            $newCsv = $request->file('new_csv');

            $csvComparison = $this->service->compare($oldCsv, $newCsv);

            return view('result', compact('csvComparison'));
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            return redirect()->back()->withErrors($errorMessage);
        }
    }
}
