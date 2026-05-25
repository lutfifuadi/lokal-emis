<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $admin = User::where('email', 'admin@emis.local')->first();
        
        $response = $this->actingAs($admin)->get('/');

        $response->assertRedirect(route('admin.dashboard'));
    }
}
