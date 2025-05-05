<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Trade;

class ProfileController extends Controller
{
    /**
     * マイページの表示
     */
    public function index()
    {
        if (!$user = Auth::user()) {
            return redirect()->route('login');
        }

        $tab = request()->query('tab', 'sell');

        $listedItems = $user->items()->paginate(8);
        $purchasedItems = $user->purchases()->with('item')->paginate(8);

        $tradingTrades = Trade::with(['item', 'chatMessages'])
            ->where(function ($query) use ($user) {
                $query->where('buyer_id', $user->id)
                    ->orWhere('seller_id', $user->id);
            })
            ->whereIn('status', ['processing', 'pending'])
            ->withMax('chatMessages as last_message_at', 'updated_at')
            ->orderByDesc('last_message_at')
            ->get();

        return view('mypage.profile', [
            'user'            => $user,
            'listedItems'     => $listedItems,
            'purchasedItems'  => $purchasedItems,
            'tradingTrades'   => $tradingTrades,
            'tab'             => $tab,
        ]);
    }

    /**
     * プロフィール編集画面の表示
     */
    public function edit()
    {
        if (!$user = Auth::user()) {
            return redirect()->route('login');
        }

        return view('mypage.edit', compact('user'));
    }

    /**
     * プロフィール情報の更新
     */
    public function update(ProfileRequest $profileRequest, AddressRequest $addressRequest)
    {
        if (!$user = Auth::user()) {
            return redirect()->route('login');
        }

        $profileData = $profileRequest->validated();
        $addressData = $addressRequest->validated();

        $profile = $user->profile ?? $user->profile()->create([
            'zipcode'       => '',
            'address'       => '',
            'building'      => '',
            'profile_image' => null,
        ]);

        if ($profileRequest->hasFile('profile_image')) {
            $newImagePath = $profileRequest->file('profile_image')->store('profile_images', 'public');

            if ($profile->profile_image) {
                Storage::disk('public')->delete($profile->profile_image);
            }

            $profileData['profile_image'] = $newImagePath;
        }

        $profile->update([
            'zipcode'  => $addressData['zipcode'],
            'address'  => $addressData['address'],
            'building' => $addressData['building'] ?? '',
            'profile_image' => $profileData['profile_image'] ?? $profile->profile_image,
        ]);

        if (isset($addressData['username'])) {
            $user->update([
                'username' => $addressData['username'],
            ]);
        }

        return redirect()->route('profile.index')->with('success', 'プロフィールが更新されました。');
    }
}
