<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEvents = Event::count();
        $events = Event::with('category')->latest()->take(5)->get();
        $recentTransactions = Transaction::with('event')->latest()->take(5)->get();

        return view('admin.dashboard', compact('totalEvents', 'events', 'recentTransactions'));
    }
}
