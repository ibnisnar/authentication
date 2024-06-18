<?php

use Illuminate\Support\Facades\Route;
use Ibnisnar\Authentication\Http\Controllers\Livewire\ApiTokenController;
use Ibnisnar\Authentication\Http\Controllers\Livewire\PrivacyPolicyController;
use Ibnisnar\Authentication\Http\Controllers\Livewire\TeamController;
use Ibnisnar\Authentication\Http\Controllers\Livewire\TermsOfServiceController;
use Ibnisnar\Authentication\Http\Controllers\Livewire\UserProfileController;
use Ibnisnar\Authentication\Authentication;

Route::group(['middleware' => config('authentication.middleware', ['web'])], function () {
    if (Authentication:hasTermsAndPrivacyPolicyFeature() {
        Route::get('/terms-of-service', [TermsOfServiceController::class, 'show'])->name('terms.show');
        Route::get('/privacy-policy', [PrivacyPolicyController::class, 'show'])->name('policy.show');
    }

    $authMiddleware = config('authentication.guard')
        ? 'auth:'.config('authentication.guard')
        : 'auth';

    $authSessionMiddleware = config('authentication.auth_session', false)
        ? config('authentication.auth_session')
        : null;

    Route::group(['middleware' => array_values(array_filter([$authMiddleware, $authSessionMiddleware]))], function () {
        // User & Profile...
        Route::get('/user/profile', [UserProfileController::class, 'show'])->name('profile.show');

        Route::group(['middleware' => 'verified'], function () {
            // API...
            if (Authentication:hasApiFeatures() {
                Route::get('/user/api-tokens', [ApiTokenController::class, 'index'])->name('api-tokens.index');
            }
        });
    });
});
