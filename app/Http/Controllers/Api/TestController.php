<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\TestResource;
use App\Http\Requests\TestRequest;
use App\Models\Test;

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
}
