<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class RedisTestController extends Controller
{
    public function testConnection()
    {
        try {
            Redis::set('test_key', 'Test value');
            $value = Redis::get('test_key');
            return response("Redis connection test successful. Value retrieved: $value");
        } catch (\Exception $e) {
            return response("Redis connection test failed. Error: " . $e->getMessage(), 500);
        }
    }
}
