<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function show(Event $event)
    {
        $categories = Category::all();

        return view('event-detail', compact('event', 'categories'));
    }

    public function checkout(Event $event)
    {
        return view('checkout', compact('event'));
    }

    public function processCheckout(Request $request, Event $event)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:25',
        ]);

        $transaction = Transaction::create([
            'event_id' => $event->id,
            'order_id' => 'TRX-' . strtoupper(Str::random(8)),
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'total_price' => $event->price + 5000,
            'status' => 'pending',
        ]);

        return redirect()->route('ticket', $transaction)->with('success', 'Transaksi berhasil dibuat.');
    }

    public function ticket(Transaction $transaction = null)
    {
        if (!$transaction) {
            $transaction = Transaction::with('event')->latest()->first();
        }

        if (!$transaction) {
            abort(404, 'Transaksi tidak ditemukan.');
        }

        $transaction->load('event');

        return view('ticket', compact('transaction'));
    }
}
