<?php

return [
    App\Providers\AppServiceProvider::class,
    config('horizon.enable') ?? App\Providers\HorizonServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
];
