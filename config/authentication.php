<?php

use Ibnisnar\Authentication\Features;

return [
    'stack' => 'livewire',
    'middleware' => ['web'],
    'features' => [Features::accountDeletion()],
    'profile_photo_disk' => 'public',
];
