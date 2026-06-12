<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
class TestController extends Controller
{
    public function index()
    {
        $start = microtime(true);

        $users = DB::table('custom_users')
            ->where('email', 'user500@test.com')
            ->first();

        $time = microtime(true) - $start;

        return dd([
            'user' => $users,
            'time_taken' => $time . ' seconds'
        ]);
    }
}