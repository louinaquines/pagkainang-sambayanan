<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CharityController;
use App\Http\Controllers\CharityRequestController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleAuthController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', function () {
    $emergencyMode = DB::table('settings')
        ->where('key', 'emergency_mode')
        ->value('value');
    
    // Fetch weather for user's location (if charity)
    $weather = [];
    $user = auth()->user();
    if ($user && $user->role === 'charity' && !empty($user->address)) {
        try {
            $weather = Cache::remember('weather_user_' . $user->id, 1800, function () use ($user) {
                $apiKey = env('OPENWEATHER_API_KEY');
                if (empty($apiKey)) return [];
                $response = Http::get('https://api.openweathermap.org/data/2.5/weather', [
                    'q' => $user->address . ', Philippines',
                    'appid' => $apiKey,
                    'units' => 'metric'
                ]);
                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'temperature' => $data['main']['temp'] ?? null,
                        'condition' => $data['weather'][0]['main'] ?? null,
                        'icon' => $data['weather'][0]['icon'] ?? null,
                        'city' => $data['name'] ?? $user->address,
                        'humidity' => $data['main']['humidity'] ?? null,
                    ];
                }
                return [];
            });
        } catch (\Exception $e) {
            $weather = [];
        }
    }
    
    // Fetch ReliefWeb disasters (cached for 30 minutes)
    $disasters = [];
    try {
        $disasters = Cache::remember('reliefweb_disasters', 1800, function () {
            $response = Http::get('https://api.reliefweb.int/v1/disasters', [
                'filter[field][country]' => 'Philippines',
                'limit' => 3
            ]);
            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? [];
            }
            return [];
        });
    } catch (\Exception $e) {
        $disasters = [];
    }
    
    return view('dashboard', compact('emergencyMode', 'weather', 'disasters'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('role:donor')->group(function () {
        Route::get('/donate', [DonationController::class, 'create'])->name('donations.create');
        Route::post('/donate', [DonationController::class, 'store'])->name('donations.store');
        Route::post('/charity-requests/{id}/fulfill', [CharityRequestController::class, 'fulfill'])->name('charity.requests.fulfill');
        Route::post('/donations/claims/{claimId}/accept', [DonationController::class, 'acceptClaim'])->name('donations.claims.accept');
    });

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');

    Route::middleware('role:charity')->group(function () {
        Route::get('/charity/request/create', [CharityRequestController::class, 'create'])->name('charity.request.create');
        Route::post('/charity/request', [CharityRequestController::class, 'store'])->name('charity.request.store');
        Route::get('/feedback/{donationId}/create', [FeedbackController::class, 'create'])->name('feedback.create');
        Route::post('/feedback/{donationId}', [FeedbackController::class, 'store'])->name('feedback.store');
        Route::post('/feedback/comment/{feedbackId}', [FeedbackController::class, 'comment'])->name('feedback.comment');
        Route::post('/donations/{id}/claim', [DonationController::class, 'claim'])->name('donations.claim');
        Route::patch('/profile/emergency-status', [ProfileController::class, 'updateEmergencyStatus'])->name('profile.emergency-status');
    });

    Route::get('/charity-requests', [CharityRequestController::class, 'index'])->name('charity.requests.index');
    Route::get('/available-donations', [DonationController::class, 'available'])->name('donations.available');
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
    Route::get('/donations', [DonationController::class, 'index'])->name('donations.index');
    Route::delete('/donations/bulk-delete', [DonationController::class, 'destroyBulk'])->name('donations.bulk.delete');
    Route::get('/charity/register', [CharityController::class, 'showRegistrationForm'])->name('charity.register');
    Route::post('/charity/register', [CharityController::class, 'submitRegistration'])->name('charity.submit');

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::post('/admin/toggle-emergency', [AdminController::class, 'toggleEmergencyMode'])->name('admin.toggleEmergency');
        Route::post('/admin/confirm-allocation', [AdminController::class, 'confirmAllocation'])->name('admin.confirmAllocation');
        Route::get('/admin/charities', [AdminController::class, 'charities'])->name('admin.charities');
        Route::post('/admin/charities/{id}/approve', [AdminController::class, 'approveCharity'])->name('admin.charities.approve');
        Route::post('/admin/charities/{id}/reject', [AdminController::class, 'rejectCharity'])->name('admin.charities.reject');
    });
});
    Route::get('auth/google', [GoogleAuthController::class, 'redirectToGoogle']);
    Route::get('auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

require __DIR__ . '/auth.php';

// GET logout route (fallback to prevent 419 page expired errors when session expires)
Route::get('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout.get');

Route::get('/clear-site', function() {
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    return "Site cache cleared! Go check the login page.";
});