<?php

namespace App\Http\Controllers;

use App\Services\SatuSehatService;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    protected $satuSehatService;

    public function __construct(SatuSehatService $satuSehatService)
    {
        $this->satuSehatService = $satuSehatService;
    }

    public function getAccessToken()
    {
        $accessToken = $this->satuSehatService->getAccessToken();

        if ($accessToken) {
            return response()->json([
                'access_token' => $accessToken,
            ]);
        } else {
            return response()->json([
                'message' => 'Failed to retrieve access token'
            ], 500);
        }
    }
}
