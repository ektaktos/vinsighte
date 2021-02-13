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
use Spatie\PdfToImage\Pdf;
use ZipArchive;

class HomeController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    
    public function index(){
        return view('welcome');
    }

    public function Logs(){
        $this->cronJob();
        $pending = Logs::where(['status' => 'started'])->count();
        $logs = Logs::where(['user_id' => Auth::id()])->get();
        return ['logs' => $logs, 'pendingJobs' => $pending];
    }

    public function getLogs(){
        $logs = $this->Logs();
        return view('logs')->with($logs);
    }

    public function cronJob(){
        // Fetch all jobIds that are not finished yet
        $jobs = Logs::where(['status' => 'started'])->get();
        if ($jobs->count() > 0) {
            foreach ($jobs as $job) {
                $id = $job->id;
                $log = Logs::find($id);
                $res = Http::get("http://dehbaiyor.herokuapp.com/ocr/results/{$job->jobId}")->json();
                if ($res['job_status'] == 'finished') {
                    $log->status = $res['job_status'];
                    $log->processed_data = $res['results'][$id]['correct_text'];
                    $log->raw_data = $res['results'][$id];
                    $log->processed_at = Carbon::now();
                    $log->save();
                } else if($res['job_status'] == 'failed') {
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
        $unScannedExtensions = array('docx', 'xlsx', 'pptx', 'html', 'pdf', 'txt', 'csv', 'java','c', 'h', 'ipynb', 'py', 'cpp');
        $scannedExtensions = array('jpg', 'jpeg', 'gif', 'png');
        $format = $request->format;
        $imagesData = [];
        foreach ($request->file() as $img) {
            $image = new Logs();
            $ext = $img->getClientOriginalExtension();
            if ($format == 'scanned') {
                if (!in_array($ext, $scannedExtensions)) {
                    return response(['status' => 'error'], 400);
                }
            } else {
                if (!in_array($ext, $unScannedExtensions)) {
                    return response(['status' => 'error'], 400);
                }
            }
            $filename = Str::random(12).'.'.$ext;
            Cloudder::upload($img->getPathname(), null, array("resource_type" => "auto", "public_id" => $filename));
            $imageResult = Cloudder::getResult();
            // Save Image upload to cloudinary
            $image->user_id = Auth::id();
            $image->image_url = $imageResult['url'];
            $image->format = $format;
            $image->save();

            $saveProcessed = Logs::find($image->id);
            if ($format == 'scanned') {
                $response = Http::get('https://dehbaiyor.herokuapp.com/ocr/'.$image->id.','.$imageResult['url']);
                $imagesData[] =  $response->json();
                $saveProcessed->jobId = $response->json();
            } else {
                $response = Http::get('https://vs-text-extract.herokuapp.com/'.$image->id.','.$imageResult['url']);
                $imagesData[] =  $response->json();
                $res = $response->json();
                $saveProcessed->status = 'finished';
                $saveProcessed->raw_data = $res;
                $saveProcessed->processed_data = $res['extracted'][$filename];
            }
            
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

    public function pdfToImage(){
        $pdf = new Pdf('');
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
