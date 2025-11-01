<?php

namespace App\Http\Middleware;

use App\Models\Cart;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                // 1. Check if the user is logged in
                'user' => $request->user() ? [
                    // 2. If yes, share only these specific fields
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'role' => $request->user()->role, // <-- Here is your role
                ] : null, // 3. If no (a guest), share null
            ],
            'carts' => function () {
                if (!Auth::check()) {
                    return [];
                }

                return Cart::where('user_id', Auth::id())->with(['product', 'product.images'])->get();
            },
            'cart_count' => function () {
                if (!Auth::check()){
                    return 0;
                }

                return Cart::where('user_id', Auth::id())->sum('quantity');
            },
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
