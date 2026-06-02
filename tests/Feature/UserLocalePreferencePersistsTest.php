<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Modules\Core\Http\Middleware\SetUserLocale;
use Modules\Core\Models\User;
use Tests\TestCase;

class UserLocalePreferencePersistsTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function runMiddleware(Request $request): void
    {
        $middleware = new SetUserLocale;
        $middleware->handle($request, fn () => new Response);
    }

    public function test_middleware_sets_locale_to_id_for_user_with_id_preference(): void
    {
        $user = User::factory()->create(['preferred_locale' => 'id']);
        $this->actingAs($user);

        $this->runMiddleware(Request::create('/admin'));

        $this->assertSame('id', App::getLocale());
    }

    public function test_middleware_sets_locale_to_en_for_user_with_en_preference(): void
    {
        $user = User::factory()->create(['preferred_locale' => 'en']);
        $this->actingAs($user);

        $this->runMiddleware(Request::create('/admin'));

        $this->assertSame('en', App::getLocale());
    }

    public function test_preferred_locale_is_fillable_and_persists(): void
    {
        $user = User::factory()->create(['preferred_locale' => 'en']);

        $this->assertSame('en', $user->fresh()->preferred_locale);

        $user->update(['preferred_locale' => 'id']);

        $this->assertSame('id', $user->fresh()->preferred_locale);
    }

    public function test_user_without_preferred_locale_falls_back_to_config(): void
    {
        $user = User::factory()->create(['preferred_locale' => null]);
        $this->actingAs($user);

        $this->runMiddleware(Request::create('/admin'));

        $this->assertSame(config('app.locale', 'id'), App::getLocale());
    }
}
