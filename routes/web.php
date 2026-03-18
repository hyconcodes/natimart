<?php

use App\Models\Shop;
use Illuminate\Support\Facades\Route;

$domain = env('APP_DOMAIN', 'natimart.test');

// Vendor Storefront Routes (Subdomains)
Route::domain('{shop_slug}.'.$domain)->group(function () {
    Route::get('/', function ($shop_slug) {
        $shop = Shop::where('slug', $shop_slug)->firstOrFail();

        return view('vendor-store', compact('shop'));
    })->name('vendor.store');

    Route::middleware(['auth', 'verified', 'role:vendor'])->group(function () {
        Route::get('/dashboard', function ($shop_slug) {
            $user = Auth::user();
            if ($user->shop && $user->shop->slug !== $shop_slug) {
                // Enforce that vendors can only access THEIR subdomain
                $port = request()->getPort();
                $portSuffix = in_array($port, [80, 443]) ? '' : ':'.$port;
                $url = request()->getScheme().'://'.$user->shop->slug.'.'.env('APP_DOMAIN', 'localhost').$portSuffix.'/dashboard';

                return redirect()->to($url);
            }

            return view('pages.vendor.dashboard');
        })->name('vendor.dashboard');

        Route::get('/verification', function ($shop_slug) {
            return view('pages.vendor.verification');
        })->name('vendor.verification');
    });
});

// Main Site Routes
Route::view('/', 'welcome')->name('home');
Route::view('/about', 'about')->name('about');
Route::get('/states/{state}', function ($state) {
    return view('state-products', compact('state'));
})->name('state.products');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        $user = Auth::user();
        if ($user->hasRole('masteradmin')) {
            return view('pages.admin.dashboard');
        } elseif ($user->hasRole('state_coordinator')) {
            return view('pages.coordinator.dashboard');
        } elseif ($user->hasRole('vendor')) {
            if ($user->shop) {
                $port = request()->getPort();
                $portSuffix = in_array($port, [80, 443]) ? '' : ':'.$port;
                $url = request()->getScheme().'://'.$user->shop->slug.'.'.env('APP_DOMAIN', 'localhost').$portSuffix.'/dashboard';

                return redirect()->to($url);
            }

            return view('pages.vendor.dashboard'); // Fallback if no shop
        }

        return view('dashboard');
    })->name('dashboard');

    // Admin Routes
    Route::middleware(['role:masteradmin'])->group(function () {
        Route::view('admin/users', 'pages.admin.users')->name('admin.users');
        Route::view('admin/roles', 'pages.admin.roles')->name('admin.roles');
    });

    Route::middleware(['role:masteradmin|state_coordinator'])->group(function () {
        Route::view('admin/vendors', 'pages.admin.vendors')->name('admin.vendors');
    });
});

require __DIR__.'/settings.php';
