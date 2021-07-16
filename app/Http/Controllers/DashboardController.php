<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id', "desc")->take(6)->get();
        $total_users = User::count();
        $count_users['day'] = User::where('created_at','>=',Carbon::today())->count();
        $count_users['week'] = User::where('created_at','>=',Carbon::today()->subDays(7))->count();
        $count_users['month'] = User::where('created_at','>=',Carbon::today()->subDays(30))->count();

        return response()->json([
            'users' => $users,
            'total_users' => $total_users,
            'count_users' => $count_users
        ], 200);
    }
}
