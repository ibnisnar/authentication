<?php

use App\Models\User;
use Illuminate\Support\Str;
use Ibnisnar\Authentication\Features;
use Ibnisnar\Authentication\Http\Livewire\ApiTokenManager;
use Livewire\Livewire;

test('api token permissions can be updated', function () {
    $this->actingAs($user = User::factory()->create());

    $token = $user->tokens()->create([
        'name' => 'Test Token',
        'token' => Str::random(40),
        'abilities' => ['create', 'read'],
    ]);

    Livewire::test(ApiTokenManager::class)
        ->set(['managingPermissionsFor' => $token])
        ->set(['updateApiTokenForm' => [
            'permissions' => [
                'delete',
                'missing-permission',
            ],
        ]])
        ->call('updateApiToken');

    expect($user->fresh()->tokens->first())
        ->can('delete')->toBeTrue()
        ->can('read')->toBeFalse()
        ->can('missing-permission')->toBeFalse();
})->skip(function () {
    return ! Features::hasApiFeatures();
}, 'API support is not enabled.');
