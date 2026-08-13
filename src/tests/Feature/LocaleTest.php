<?php

namespace Tests\Feature;

use Tests\TestCase;

class LocaleTest extends TestCase
{
    public function test_user_can_switch_locale_to_spanish(): void
    {
        $response = $this->get(route('locale.switch', 'es'));

        $response->assertRedirect();
        $response->assertSessionHas('locale', 'es');
        $response->assertCookie('velntra_locale', 'es');
    }

    public function test_user_can_switch_locale_to_english(): void
    {
        $response = $this->get(route('locale.switch', 'en'));

        $response->assertRedirect();
        $response->assertSessionHas('locale', 'en');
        $response->assertCookie('velntra_locale', 'en');
    }

    public function test_invalid_locale_is_ignored(): void
    {
        $response = $this->get(route('locale.switch', 'invalid_locale'));

        $response->assertRedirect();
        $this->assertNotEquals('invalid_locale', session('locale'));
    }

    public function test_middleware_sets_app_locale_from_session(): void
    {
        $this->withSession(['locale' => 'es'])
            ->get(route('dashboard'));

        $this->assertEquals('es', app()->getLocale());
    }
}
