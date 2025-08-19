<?php

use Laravel\Dusk\Browser;

test('basic example', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->waitForText('Let\'s get started', 10) // Increase timeout to 10 seconds
            ->assertSee('Let\'s get started');
    });
});
