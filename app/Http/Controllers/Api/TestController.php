<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\TestResource;
use App\Http\Requests\TestRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Test;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;


use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\File;
use Illuminate\Support\Facades\Response;

class TestController extends Controller
{
    public function index()
    {
        return TestResource::collection(Test::all());
    }

    public function show(Test $test)
    {
        return new TestResource($test);
    }

    public function store(TestRequest $request)
    {
        $test = new Test;
        $test->name = $request->input('test.name');
        $test->link = $request->input('test.link');
        $test->base_attempts = $request->input('test.base_attempts');
        $test->save();
        return new TestResource($test);
    }

    public function update(Test $test, TestRequest $request)
    {
        $test->name = $request->input('test.name');
        $test->link = $request->input('test.link');
        $test->base_attempts = $request->input('test.base_attempts');
        $test->save();
        return new TestResource($test);
    }

    public function destroy(Test $test)
    {
        $test->delete();
        return $test->id;
    }

    public function store_photo(Test $id,Request $request)
    {
        $base64File = $request->photo;
        // decode the base64 file
        $fileData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64File));
        $tmpFilePath = sys_get_temp_dir() . '/' . Str::uuid()->toString();
        file_put_contents($tmpFilePath, $fileData);
        $tmpFile = new File($tmpFilePath);
        $file = new UploadedFile(
            $tmpFile->getPathname(),
            $tmpFile->getFilename(),
            $tmpFile->getMimeType(),
            0,
            true // Mark it as test, since the file isn't from real HTTP POST.
        );
        $path = $file->store('photos');

        //$path = $file->storeAs('photos/', Auth::user()->id.'_'.$id.$extension);
        Auth::user()->tests()->updateExistingPivot($id, ['photo_file' => $path], false);
        return Response::json([
            'status' => 'ok'
        ], 200);
    }

    public function start_test(Request $request) {
        $token = $request->code;
        $user_id = Crypt::decryptString($token);

        return view('iframe', ['user_id' => $user_id]);
    }
}
