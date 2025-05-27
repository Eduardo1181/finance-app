<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;

class StockController extends Controller
{
    public function getQuotes(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;
        $apiKey = $this->getDecryptedApiKey($user);

        $symbols = trim($request->input('symbols'));

        if (empty($symbols)) {
            $symbols = $user->favorite_symbols;

            if (empty($symbols)) {
                return response()->noContent();
            }
        }

        $symbols = strtoupper(preg_replace('/\s+/', '', $symbols));

        $validator = Validator::make(['symbols' => $symbols], [
            'symbols' => ['required', 'string', 'regex:/^[A-Z,]+$/'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Símbolos inválidos.'], 422);
        }

        $dailyKey        = "user:{$userId}:daily_requests";
        $lastRequestKey  = "user:{$userId}:last_request_at";
        $lastResponseKey = "user:{$userId}:last_response_data";

        $now            = Carbon::now();
        $lastRequest    = Cache::get($lastRequestKey);
        $dailyRequests  = Cache::get($dailyKey, 0);
        $lastResponse   = Cache::get($lastResponseKey);

        $maxRequests      = config('services.api.daily_limit', 8);
        $minIntervalHours = config('services.api.min_interval', 3);

        if (
            ($lastRequest && Carbon::parse($lastRequest)->diffInHours($now) < $minIntervalHours)
            || ($dailyRequests >= $maxRequests)
        ) {
            if ($lastResponse) {
                return response()->json($lastResponse);
            }

            return response()->json([
                'error' => 'Limite de requisições atingido e sem dados disponíveis.'
            ], 429);
        }

        try {
            $response = Http::timeout(5)->get("https://financialmodelingprep.com/api/v3/quote/{$symbols}", [
                'apikey' => $apiKey
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                if (!is_array($responseData) || empty(array_filter($responseData))) {
                    return response()->json([
                        'error' => "Nenhum dado encontrado para os símbolos: {$symbols}"
                    ], 404);
                }

                foreach ($responseData as &$item) {
                    if (isset($item['changesPercentage'])) {
                        $variation = (float) $item['changesPercentage'];
                        if ($variation >= 3) {
                            $item['alert'] = "📣 Alta de " . number_format($variation, 2) . "%";
                        } elseif ($variation <= -3) {
                            $item['alert'] = "🚨 Queda de " . number_format(abs($variation), 2) . "%";
                        }
                    }
                }

                Cache::put($lastRequestKey, $now, now()->endOfDay());
                Cache::put($dailyKey, $dailyRequests + 1, now()->endOfDay());
                Cache::put($lastResponseKey, $responseData, now()->endOfDay());

                return response()->json($responseData);
            }

            if ($lastResponse) {
                return response()->json($lastResponse);
            }

            return response()->json(['error' => 'Erro na API externa'], $response->status());
        } catch (\Exception $e) {
            if ($lastResponse) {
                return response()->json($lastResponse);
            }

            return response()->json(['error' => 'Erro interno: ' . $e->getMessage()], 500);
        }
    }

    public function searchCompany(Request $request)
    {
        $request->validate([
            'query' => ['required', 'string', 'min:2']
        ]);

        $user = Auth::user();
        $apiKey = $this->getDecryptedApiKey($user);

        try {
            $response = Http::timeout(5)->get('https://financialmodelingprep.com/api/v3/search', [
                'query'    => $request->input('query'),
                'limit'    => 10,
                'exchange' => 'NASDAQ',
                'apikey'   => $apiKey
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json(['error' => 'Erro ao buscar empresas.'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro interno: ' . $e->getMessage()], 500);
        }
    }

    private function getDecryptedApiKey($user)
    {
        if (!$user->api_key) {
            abort(403, 'Você precisa configurar sua chave de API.');
        }

        try {
            return Crypt::decryptString($user->api_key);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(500, 'Chave de API inválida ou corrompida.');
        }
    }
}