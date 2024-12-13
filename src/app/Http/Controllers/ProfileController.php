<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $tab = request()->query('tab', 'sell');
        $listedItems = $user->items;
        $purchasedItems = $user->purchases;

        return view('mypage.profile', compact('user', 'listedItems', 'purchasedItems', 'tab'));
    }

    public function edit()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        return view('mypage.edit', compact('user'));
    }

    public function update(ProfileRequest $profileRequest, AddressRequest $addressRequest)
    {
        $profileData = $profileRequest->validated();
        $addressData = $addressRequest->validated();

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if (isset($profileData['profile_image'])) {
            $path = $profileRequest->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }

        $user->update([
            'name' => $addressData['username'],
            'zipcode' => $addressData['zipcode'],
            'address' => $addressData['address'],
            'building' => $addressData['building'],
        ]);

        return redirect()->route('profile.index');
    }
}
