<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiSmokeTest extends TestCase
{
    public function test_feed_is_available(): void
    {
        $this->getJson('/api/v1/feed')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'live_matches',
                    'today_matches',
                    'breaking',
                    'top_news',
                    'competitions',
                ],
            ]);
    }
}
