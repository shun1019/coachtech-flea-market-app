<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        $profile = $user->profile ?? $user->profile()->create([
            'zipcode' => '',
            'address' => '',
            'building' => '',
            'profile_image' => null,
        ]);

        if ($profileRequest->hasFile('profile_image')) {
            $path = $profileRequest->file('profile_image')->store('profile_images', 'public');

            if ($profile->profile_image) {
                Storage::disk('public')->delete($profile->profile_image);
            }

            $profileData['profile_image'] = $path;
        }

        $profile->update([
            'profile_image' => $profileData['profile_image'] ?? $profile->profile_image,
            'zipcode' => $addressData['zipcode'],
            'address' => $addressData['address'],
            'building' => $addressData['building'],
        ]);

        $user->update([
            'username' => $addressData['username'],
        ]);

        return redirect()->route('profile.index')->with('success', 'プロフィールが更新されました！');
    }
}
