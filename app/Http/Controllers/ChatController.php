<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class ChatController extends Controller
{
    public function generateContent(Request $request)
    {
        $request = $request->text;

        $type = $this->classifyContent($request);
        if ($type == 'statement') {
            $sample = "Instruction: Extract the Intent from the prompt below, and identify if any parameter is empty or not present.\n\nPrompt: Can you tell me the current balance in account number 000253000?\nResponse: {\"Intent\": \"acct_bal\", \"AccountNumber\": \"000253000\"}##\nPrompt: What's the current balance for account 89800872829?\nResponse: {\"Intent\": \"acct_bal\", \"AccountNumber\": \"89800872829\"}##\nPrompt: What's the current balance for account?\nResponse: {\"Intent\": \"acct_bal\", \"AccountNumber\": \"\"}##\nPrompt: I would like to know the balance in account 1239087536, can you provide me with that information?\nResponse: {\"Intent\": \"acct_bal\", \"AccountNumber\": \"1239087536\"}##\nPrompt: Could you give me an update on the balance of account 000253000?\nResponse: {\"Intent\": \"acct_bal\", \"AccountNumber\": \"000253000\"}\nPrompt: I'm wondering if you could inform me of the balance in account.\nResponse: {\"Intent\": \"acct_bal\", \"AccountNumber\": \"\"}\nPrompt: I'm curious about the balance in account ?#???. Can you provide me with that information?\nResponse: {\"Intent\": \"acct_bal\", \"AccountNumber\": \"\"}\nPrompt: What is the balance for account 3119025601 presently?\nResponse: {\"Intent\": \"acct_bal\", \"AccountNumber\": \"3119025601\"}\nPrompt: Please provide me with the current balance of account number 000253000.\nResponse: {\"Intent\": \"acct_bal\", \"AccountNumber\": \"000253000\"}\nPrompt: Account 000253000 balance?\nResponse: {\"Intent\": \"acct_bal\", \"AccountNumber\": \"000253000\"}\nPrompt: Balance inquiry for 565789772 account?\nResponse: {\"Intent\": \"acct_bal\", \"AccountNumber\": \"565789772\"}\nPrompt: How much in 9870908771 account?\nResponse: {\"Intent\": \"acct_bal\", \"AccountNumber\": \"9870908771\"}\nPrompt: Account, how much?\nResponse: {\"Intent\": \"acct_bal\", \"AccountNumber\": \"\"}\nPrompt: ";
            $prompt = $sample . $request . "\n";
            Log::info($prompt);
            $response = OpenAI::completions()->create([
                'model' => 'text-davinci-003',
                'top_p' => 1,
                'max_tokens' => 256,
                'frequency_penalty' => 0,
                'presence_penalty' => 0,
                'temperature' => 0,
                'stop' => ['##'],
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
        } else if ($type == 'balance') {
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
        $statement_keywords = [
            'statement',
            'account statement',
        ];

        foreach ($statement_keywords as $keyword) {
            if (strpos($request, $keyword) !== false) {
                return 'statement';
            }
        }

        $balance_keywords = [
            'balance',
            'account balance',
        ];

        foreach ($balance_keywords as $keyword) {
            if (strpos($request, $keyword) !== false) {
                return 'balance';
            }
        }
    }
}
