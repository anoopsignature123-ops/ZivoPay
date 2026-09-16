<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test root route loads the website landing page successfully.
     */
    public function test_root_route_loads_website_landing_page(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
