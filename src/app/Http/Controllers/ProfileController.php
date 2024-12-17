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

        // プロファイルの作成または更新
        $profile = $user->profile;

        if (!$profile) {
            $user->profile()->create([
                'zipcode' => $addressData['zipcode'],
                'address' => $addressData['address'],
                'building' => $addressData['building'],
                'profile_image' => $profileData['profile_image'] ?? null,
            ]);
        } else {
            $profile->update(array_merge($addressData, $profileData));
        }

        // ユーザー名の更新
        $user->update([
            'username' => $addressData['username'],
        ]);

        return redirect()->route('profile.index')->with('success', 'プロフィールを更新しました！');
    }
}
