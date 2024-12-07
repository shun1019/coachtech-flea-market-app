<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public Function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->withErrors(['message' => 'User not authenticated']);
        }

        $tab = $request->query('tab', 'sell');
        $listedItems = $user->items;
        $purchasedItems = $user->purchases;

        return view('mypage.show', compact('user', 'listedItems', 'purchasedItems', 'tab'));
    }

    public function edit()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->withErrors(['message' => 'User not authenticated']);
        }

        return view('mypage.edit', compact('user'));
    }
}
