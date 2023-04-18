<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;

class ChatController extends Controller
{
    public function generateContent(Request $request)
    {
        $request = $request->text;
        $type = $this->classifyContent($request);
        if ($type == 'code') {
            $prompt = "$request";
            $response = OpenAI::completions()->create([
                'model' => 'code-davinci-002',
                'max_tokens' => 60,
                'prompt' => $prompt,
            ]);
            $content = $response->choices;
            return response()->json([
                'code' => '00',
                'data' => [
                    'id' => 1,
                    'text' => $content,
                    'type' => 'response'
                ]
            ]);
        } else if ($type == 'image') {
            $prompt = "$request";
            $response = OpenAI::images()->create([
                'prompt' => $prompt,
                'n' => 1,
                'size' => '256x256',
                'response_format' => 'url',
            ]);
            $content = $response->data[0]->url;
            return response()->json([
                'code' => '00',
                'data' => [
                    'id' => 1,
                    'text' => $content,
                    'type' => 'response'
                ]
            ]);
        } else if ($type == 'edit') {
            $prompt = "Edit the following text: $request";
            $response = OpenAI::completions()->create([
                'model' => 'text-davinci-003',
                'prompt' => $prompt,
            ]);
            $content = $response->choices[0]->text;
            return response()->json([
                'code' => '00',
                'data' => [
                    'id' => 1,
                    'text' => $content,
                    'type' => 'response'
                ]
            ]);
        } else {
            $prompt = "$request";
            $response = OpenAI::completions()->create([
                'model' => 'text-davinci-003',
                'prompt' => $prompt,
                'max_tokens' => 60,
                'temperature' => 0
            ]);
            $content = $response->choices[0]->text;
            return response()->json([
                'id' => 1,
                'text' => $content,
                'type' => 'response'
            ]);
        }
    }

    private function classifyContent(string $request)
    {
        if (strpos($request, 'code') !== false || strpos($request, 'programming') !== false) {
            return 'code';
        } else if (strpos($request, 'image') !== false || strpos($request, 'picture') !== false) {
            return 'image';
        } else if (strpos($request, 'edit') !== false) {
            return 'edit';
        } else {
            return 'text';
        }
    }
}
