<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('generateCode')) {
    function generateCode($str)
    {
        $date = now()->format('YmdHis');
        $randomNumber = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        return strtoupper($str) . $date . $randomNumber;
    }
}

// if (!function_exists('uploadFile')) {
//     function uploadFile($file, $userCode, $extension)
//     {
//         $fileName = time() . '_' . uniqid() . '.' . $extension;
//         $filePath = 'public/documents/' . $userCode;

//         $file->storeAs($filePath, $fileName);
//         dd($file);

//         return $filePath . '/' . $fileName;
//     }
// }

if (!function_exists('uploadFile')) {
    function uploadFile($file, $userCode, $extension)
    {
        // Pastikan $file adalah objek file dengan content valid
        // Pastikan $filePath memiliki path yang benar
        // Pastikan $fileName memiliki nama file yang valid dan unik
        $fileName = time() . '_' . uniqid() . '.' . $extension;
        $filePath = 'public/documents/' . $userCode;

        // Ubah base64 yang di-decode menjadi objek file dan simpan dengan storeAs()
        Storage::put($filePath . '/' . $fileName, $file);

        return $filePath . '/' . $fileName;
    }
}

if (!function_exists('downloadFile')) {
    function downloadFile($filePath)
    {
        $pathToFile = storage_path('app/' . $filePath);
        $file = storage_path($filePath);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        if (file_exists($pathToFile)) {
            $headers = [
                'Content-Type' => 'application/' . $extension,
            ];
            
            return response()->download($pathToFile, basename($file), $headers);
        } else {
            return response()->json(['error' => 'File not found'], 404);
        }
    }
}


?>