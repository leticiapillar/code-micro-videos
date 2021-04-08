<?php

namespace Tests\Unit\Models;

use App\Models\Traits\UploadFiles;
use App\Models\Video;
use App\Models\Traits\Uuid;
use Tests\TestCase;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoUnitTest extends TestCase
{
    private $video;

    protected function setUp(): void
    {
        parent::setUp();
        $this->video = new Video();
    }

    public function testIfUseTraits()
    {
        $traits = [
            SoftDeletes::class, Uuid::class, UploadFiles::class
        ];
        $videoTraits = array_keys(class_uses(Video::class));
        $this->assertEquals($traits, $videoTraits);
    }

    public function testFillableAttributes()
    {
        $fillable = ['title', 'description', 'year_lauched', 'opened', 'rating', 'duration',
            'video_file', 'thumb_file', 'banner_file', 'trailer_file'];
        $this->assertEquals($fillable, $this->video->getFillable());
    }

    public function testCastsAttributes()
    {
        $casts = [
            'opened' => 'boolean',
            'year_lauched' => 'integer',
            'duration' => 'integer'
        ];
        $this->assertEquals($casts, $this->video->getCasts());
    }

    public function testKeyTypeAttribule()
    {
        $this->assertEquals('string', $this->video->getKeyType());
    }

    public function testIncrementingAttribute()
    {
        $this->assertFalse($this->video->incrementing);
    }

    public function testDateAttributes()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        $this->assertEqualsCanonicalizing($dates, $this->video->getDates());
        $this->assertCount(count($dates), $this->video->getDates());
    }
}
