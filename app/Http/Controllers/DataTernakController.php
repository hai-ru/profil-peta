<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DataTernakController extends Controller
{
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required|integer|min:2010|max:2099',
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 422);
        }

        // Handle file upload
        if ($request->hasFile('csv_file')) {
            $file = $request->file('csv_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads', $fileName, 'public');

            // Optional: Process CSV file
            $this->processCsv($filePath);

            return response()->json(['success' => 'File uploaded successfully']);
        }

        return response()->json(['error' => 'File upload failed'], 500);
    }

    private function processCsv($filePath)
    {
        // read CSV 
        $path = storage_path('app/public/' . $filePath);
        $file = fopen($path, 'r');

        // Process each row of CSV 
        while (($row = fgetcsv($file)) !== FALSE) {
            // Handle each row's data
        }

        fclose($file);
    }
}
