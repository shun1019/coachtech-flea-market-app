<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    /**
     * 認証が必要な処理を制限する
     *
     * - 商品の出品ページ (`create`) や登録処理 (`store`) はログイン必須
     */
    public function __construct()
    {
        $this->middleware('auth')->only(['create', 'store']);
    }

    /**
     * 商品一覧ページを表示
     *
     * - おすすめ、マイリスト、購入済み、出品済みの商品を表示
     * - 検索機能にも対応
     */
    public function index()
    {
        $tab = request()->query('tab', 'recommended');
        $search = request()->query('search', '');
        $user = Auth::user();

        if (!$user) {
            if ($tab === 'mylist') {
                return view('index', ['items' => collect(), 'tab' => $tab, 'search' => $search]);
            }
            $query = Item::with('categories');
        } else {
            if ($tab === 'recommended') {
                $query = Item::with('categories')->where('user_id', '!=', $user->id);
            } elseif ($tab === 'mylist') {
                $likedItemIds = $user->likes()->pluck('item_id');
                $query = Item::with('categories')->where('user_id', '!=', $user->id)
                    ->whereIn('id', $likedItemIds);
            } elseif ($tab === 'purchased') {
                $purchasedItemIds = $user->purchases()->pluck('item_id');
                $query = Item::with('categories')->whereIn('id', $purchasedItemIds);
            } elseif ($tab === 'exhibited') {
                $query = Item::with('categories')->where('user_id', $user->id);
            } else {
                $query = Item::with('categories');
            }
        }

        if ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%');
        }

        $items = $query->select('items.*')->distinct()->paginate(8)->appends([
            'tab' => $tab,
            'search' => $search
        ]);

        return view('index', compact('items', 'tab', 'search'));
    }

    /**
     * 商品の出品ページを表示
     */
    public function create()
    {
        $categories = Category::all();
        return view('sell', compact('categories'));
    }

    /**
     * 商品をデータベースに登録
     */
    public function store(ExhibitionRequest $request)
    {
        $path = $request->file('image')->store('items', 'public');

        $item = Item::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'condition' => $request->condition,
            'image' => $path,
            'status' => 'available',
            'like_count' => 0,
            'comments_count' => 0,
        ]);

        if ($request->categories) {
            $item->categories()->attach($request->categories);
        }

        return redirect()->route('profile.index');
    }

    /**
     * 商品詳細ページを表示
     */
    public function show($item_id)
    {
        $item = Item::with('categories')->findOrFail($item_id);
        $comments = $item->comments()->with('user')->get();
        $user = Auth::user();
        $userLiked = $user && $user->likes()->where('item_id', $item_id)->exists();

        return view('detail', compact('item', 'comments', 'user', 'userLiked'));
    }

    /**
     * いいね（Like）のON/OFFを切り替える
     */
    public function toggleLike($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        if (!$user) {
            return redirect()->back();
        }

        $liked = $user->likes()->toggle($item_id);
        $item->increment('like_count', count($liked['attached']));
        $item->decrement('like_count', count($liked['detached']));

        return redirect()->back();
    }
}
