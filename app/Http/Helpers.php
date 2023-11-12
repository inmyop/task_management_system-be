<?php

if (!function_exists('generateCode')) {
    function generateCode($str)
    {
        $date = now()->format('YmdHis');
        $randomNumber = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        return strtoupper($str) . $date . $randomNumber;
    }
}

if (!function_exists('uploadFile')) {
    function uploadFile($file, $userCode)
    {
        $extension = $file->getClientOriginalExtension();
        $fileName = time() . '_' . uniqid() . '.' . $extension;
        $filePath = 'public/documents/' . $userCode;

        $file->storeAs($filePath, $fileName);

        return $filePath . '/' . $fileName;
    }
}

if (!function_exists('downloadFile')) {
    function downloadFile($filePath)
    {
        $pathToFile = storage_path('app/' . $filePath);

        if (file_exists($pathToFile)) {
            return response()->download($pathToFile);
        } else {
            return response()->json(['error' => 'File not found'], 404);
        }
    }
}


?>