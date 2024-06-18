<?php

use App\Models\User;
use Ibnisnar\Authentication\Features;
use Ibnisnar\Authentication\Http\Livewire\ApiTokenManager;
use Livewire\Livewire;

test('api tokens can be created', function () {
    $this->actingAs($user = User::factory()->create());

    Livewire::test(ApiTokenManager::class)
        ->set(['createApiTokenForm' => [
            'name' => 'Test Token',
            'permissions' => [
                'read',
                'update',
            ],
        ]])
        ->call('createApiToken');

    expect($user->fresh()->tokens)->toHaveCount(1);
    expect($user->fresh()->tokens->first())
        ->name->toEqual('Test Token')
        ->can('read')->toBeTrue()
        ->can('delete')->toBeFalse();
})->skip(function () {
    return ! Features::hasApiFeatures();
}, 'API support is not enabled.');
