<?php

use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/chat', [ChatController::class, 'generateContent']);


Route::get('/test', function () {
    return response()->json([
        'code' => '00',
        'data' => [
            'id' => 1,
            'text' => 'Hello World From here',
            'type' => 'response'
        ]
    ]);
});



Route::get('/messages', function () {
    return response()->json([
        'code' => '00',
        'data' => [
            [
                'id' => 1,
                'text' => 'Hello World',
                'type' => "request"
            ],
            [
                'id' => 1,
                'text' => 'Hello World',
                'type' => "response"

            ],
            [
                'id' => 3,
                'text' => 'Hello World',
                'type' => "request"

            ],
            [
                'id' => 4,
                'text' => 'Hello World',
                'type' => "response"

            ]
        ]
    ]);
});
