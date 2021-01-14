<?php

namespace App\Http\Controllers;

use App\Models\Images;
use Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use JD\Cloudder\Facades\Cloudder;

use ZipArchive;

class HomeController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    
    public function index(){
        return view('welcome');
    }

    public function uploadforPrediction(Request $request){
        $this->validate($request, [
            'image' => 'required|file',
            'format' => 'required',
        ]);
        $image = new Images();
        Cloudder::upload($request->image);
        $imageResult = Cloudder::getResult();
        // Save Image upload to cloudinary
        $image->user_id = Auth::id();
        $image->image_url = $imageResult['url'];
        $image->format = $request->format;
        $image->save();


        $extension = $request->file('image')->getClientOriginalExtension();
        $imageExt = 'image.'.$extension;
        $path = $request->file('image')->storeAs('images', $imageExt, 'public');

        $message = $this->zipImage($path, $imageExt);
        return response($message);
    }

    public function uploadToS3($image){
        if (!empty($image)) {
            $name = time() . $image->getClientOriginalName();
            $filePath = 'processed/'. $name;
            Storage::disk('s3')->put($filePath, file_get_contents($image));
            return back()->withSuccess('Image uploaded successfully');
        }
    }

    public function zipImage($imagePath, $imageName){
        // return ['message' => $imagePath];
        $public_dir=public_path().'/uploads';
        $zipFileName = 'encrypted.zip';
        $zip = new ZipArchive;

        if($zip->open($public_dir . '/' . $zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE ) === TRUE) {
            $zip->addFile(public_path('storage/'.$imagePath), $imageName);
            // Add password 
            $password = Str::random(10);
            $zip->setPassword($password);
            $name = basename($imageName);
             // encrypt the file with AES-256
            if (!$zip->setEncryptionName($name, ZipArchive::EM_AES_256)) {
                throw new \RuntimeException(sprintf('Set encryption failed: %s', $name));
            }
            $zip->close();
        }
        // Upload zip file to cloudinary
        Cloudder::upload(public_path('uploads/'.$zipFileName));
        $imageResult = Cloudder::getResult();
        // return response(['image' => $imageResult]);
        return response(['image' => public_path('uploads/'.$zipFileName)]);
        // $headers = array(
        //     'Content-Type' => 'application/octet-stream',
        // );
        // $filetopath=$public_dir.'/'.$zipFileName;
        // if(file_exists($filetopath)){
        //     return response()->download($filetopath,$zipFileName,$headers);
        // } 
        return ['messsage' => 'File successfully zipped'];
    } 
}
