<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log; 

class ApiController extends Controller
{
    public function publicRoute()
    {
        return response()->json(['message' => 'Hello World!'], 200);
    }

    public function protectedRoute()
    {
        return response('Hello from protected world');
    }
}
