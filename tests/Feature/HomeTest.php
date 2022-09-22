<?php

use Tests\TestCase;

class HomeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testProfilesPictures()
    {
        $response = $this->get(route('home.profile-pictures'));
        dd($response);
        $response->assertStatus(200);
    }
}
