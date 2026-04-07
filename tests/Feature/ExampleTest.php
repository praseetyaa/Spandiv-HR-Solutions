<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test that the home page redirects to login (guests are redirected).
     */
    public function test_the_application_redirects_guests_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    /**
     * Test that the login page loads successfully.
     */
    public function test_login_page_loads_successfully(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }
}
