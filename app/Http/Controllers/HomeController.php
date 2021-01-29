<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use JD\Cloudder\Facades\Cloudder;
use Illuminate\Support\Facades\Http;

use ZipArchive;

class HomeController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    
    public function index(){
        return view('welcome');
    }

    public function getLogs(){
        $this->cronJob();
        $logs = Logs::where(['user_id' => Auth::id()])->get();
        return view('logs')->with(['logs' => $logs]);
    }

    public function cronJob(){
        // Fetch all jobIds that are not finished yet
        $jobs = Logs::where(['status' => 'started'])->get();
        if ($jobs->count() > 0) {
            foreach ($jobs as $job) {
                $id = $job->id;
                $log = Logs::find($job->id);
                $res = Http::get("http://dehbaiyor.herokuapp.com/ocr/results/{$job->jobId}")->json();
                if ($res['status'] == 'finished') {
                    $log->status = $res['status'];
                    $log->processed_data = $res['text'][$job->id];
                    $log->processed_at = Carbon::now();
                    $log->save();
                } else if($res['status'] == 'finished') {
                    $log->status = 'failed';
                    $log->save();
                } 
            }
        }
        // return response(['response' => $res]);
    }

    public function searchLogs(Request $request){
        $this->validate($request, [
            'queryString' => 'required',
        ]);
        $searchString = $request->query('queryString');
        $logs = Logs::where('processed_data', 'LIKE', "%{$searchString}%")->get();
        return view('search')->with(['logs' => $logs, 'queryString' => $searchString]);
    }

    public function uploadforPrediction(Request $request){
        $this->validate($request, [
            'images.*' => 'required',
            // 'format' => 'required',
        ],[
            'images.*.required' => 'Please upload an image only',
            'images.*.mimes' => 'Only jpeg, png, jpg and bmp images are allowed',
            'images.*.max' => 'Sorry! Maximum allowed size for an image is 2MB',
        ]);

        $imagesData = [];
        foreach ($request->file() as $img) {
            $image = new Logs();
            Cloudder::upload($img);
            $imageResult = Cloudder::getResult();
            // Save Image upload to cloudinary
            $image->user_id = Auth::id();
            $image->image_url = $imageResult['url'];
            $image->format = 'json';
            $image->save();
            $response = Http::get('https://dehbaiyor.herokuapp.com/ocr/'.$image->id.','.$imageResult['url']);
            $imagesData[] =  $response->json();
            $saveProcessed = Logs::find($image->id);

            $saveProcessed->jobId = $response->json();
            // $saveProcessed->processed_at = Carbon::now();
            $saveProcessed->save();
        }
        return response(['messsage' => $imagesData], 200);
        // return response(['messsage' => 'Saved Successfully'  ], 200);


        // $extension = $request->file('image')->getClientOriginalExtension();
        // $imageExt = 'image.'.$extension;
        // $path = $request->file('image')->storeAs('images', $imageExt, 'public');

        // $message = $this->zipImage($path, $imageExt);
        // return response($message);
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
        return ['messsage' => 'File successfully zipped'];
    } 
}
