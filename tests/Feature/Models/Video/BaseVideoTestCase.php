<?php

namespace Tests\Feature\Models\Video;

use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

abstract class BaseVideoTestCase extends TestCase
{
    use DatabaseMigrations;

    protected $sendData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sendData = [
            'title' => 'title test',
            'description' => 'description test',
            'year_lauched' => 2010,
            'rating' => Video::RATING_LIST[0],
            'duration' => 90
        ];
    }
}
