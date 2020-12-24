<?php

namespace App\Http\Controllers;

use Carbon\Traits\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class HomeController extends Controller
{
    public function index(){
        return view('home');
    }

    public function uploadforPrediction(Request $request){
        $this->validate($request, [
            'image' => 'required|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file();
        }
    }

    public function uploadToS3($image){
        if (!empty($image)) {
            $name = time() . $image->getClientOriginalName();
            $filePath = 'processed/'. $name;
            Storage::disk('s3')->put($filePath, file_get_contents($image));
            return back()->withSuccess('Image uploaded successfully');
        }
    }

    public function zipImage(){
        $zip = new ZipArchive;

        $zipFile = __DIR__ . '/images/'. time() .'.zip';
        $zipStatus = $zip->open($zipFile, ZipArchive::CREATE);
        if ($zipStatus !== true) {
            throw new \RuntimeException(sprintf('Failed to create zip archive. (Status code: %s)', $zipStatus));
        }
        $password = 'password';
        $zip->setPassword($password);

        // // compress file
        // $baseName = basename($image);

        // if (!$zip->addFile($image, $baseName)) {
        //     throw new \RuntimeException(sprintf('Add file failed: %s', $image));
        // }

         // compress file
        $fileName = __DIR__ . '/images/shopping.png';
        $baseName = basename($fileName);

        if (!$zip->addFile($fileName, $baseName)) {
            throw new \RuntimeException(sprintf('Add file failed: %s', $fileName));
        }

        // encrypt the file with AES-256
        if (!$zip->setEncryptionName($baseName, ZipArchive::EM_AES_256)) {
            throw new \RuntimeException(sprintf('Set encryption failed: %s', $baseName));
        }

        $zip->close();
        return response()->download($zipFile);
    } 
}
