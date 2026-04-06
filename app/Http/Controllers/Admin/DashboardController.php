<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Interest;
use App\Models\NotificationLog;
use App\Models\Post;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users'              => User::count(),
            'interests'          => Interest::count(),
            'posts'              => Post::count(),
            'notifications_sent' => NotificationLog::where('status', 'success')->count(),
            'notifications_failed' => NotificationLog::where('status', 'failed')->count(),
        ];

        $recentPosts    = Post::with('tags')->latest()->take(5)->get();
        $recentLogs     = NotificationLog::with('post', 'user')->latest()->take(10)->get();
        $topicStats     = Interest::withCount('users')->orderBy('users_count', 'desc')->get(['id', 'name']);

        return view('admin.dashboard', compact('stats', 'recentPosts', 'recentLogs', 'topicStats'));

    }
}
