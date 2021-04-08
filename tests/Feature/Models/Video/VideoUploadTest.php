<?php


namespace Tests\Feature\Models\Video;


use App\Models\Video;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Http\UploadedFile;
use Tests\Exceptions\TestException;

class VideoUploadTest extends BaseVideoTestCase
{

    public function testCreateWithFiles()
    {
        \Storage::fake();
        $video = Video::create(
            $this->sendData + [
                'thumb_file' => UploadedFile::fake()->create('thumb.jpg'),
                'banner_file' => UploadedFile::fake()->create('banner.jpg'),
                'trailer_file' => UploadedFile::fake()->create('trailer.mp4'),
                'video_file' => UploadedFile::fake()->image('video.mp4')
            ]
        );
        //dump($video->toArray());
        \Storage::assertExists("{$video->id}/{$video->video_file}");
        \Storage::assertExists("{$video->id}/{$video->thumb_file}");
        \Storage::assertExists("{$video->id}/{$video->banner_file}");
        \Storage::assertExists("{$video->id}/{$video->trailer_file}");
    }

    public function testCreateIfRoolbackFiles()
    {
        \Storage::fake();
        \Event::listen(TransactionCommitted::class, function () {
            throw new TestException();
        });
        $hasError = false;

        try {
            Video::create(
                $this->sendData + [
                    'video_file' => UploadedFile::fake()->create('video.jpg'),
                    'thumb_file' => UploadedFile::fake()->image('thumb.jpg'),
                    'banner_file' => UploadedFile::fake()->image('banner.jpg'),
                    'trailer_file' => UploadedFile::fake()->image('trailer.jpg')
                ]
            );
        } catch (TestException $exception) {
            $this->assertCount(0, \Storage::allFiles());
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }

    public function testUpdateWithFiles()
    {
        \Storage::fake();
        $video = factory(Video::class)->create();
        $thumbFile = UploadedFile::fake()->image("thumb.jpg");
        $bannerFile = UploadedFile::fake()->image("banner.jpg");
        $trailerFile = UploadedFile::fake()->image("trailer.mp4");
        $videoFile = UploadedFile::fake()->create("video.mp4");
        $video->update($this->sendData + [
                'thumb_file' => $thumbFile,
                'banner_file' => $bannerFile,
                'trailer_file' => $trailerFile,
                'video_file' => $videoFile,
            ]);
        \Storage::assertExists("{$video->id}/{$video->thumb_file}");
        \Storage::assertExists("{$video->id}/{$video->banner_file}");
        \Storage::assertExists("{$video->id}/{$video->trailer_file}");
        \Storage::assertExists("{$video->id}/{$video->video_file}");

        $newVideoFile = UploadedFile::fake()->image("video.mp4");
        $video->update($this->sendData + [
                'video_file' => $newVideoFile
            ]);
        \Storage::assertExists("{$video->id}/{$newVideoFile->hashName()}");
        \Storage::assertExists("{$video->id}/{$thumbFile->hashName()}");
        \Storage::assertExists("{$video->id}/{$bannerFile->hashName()}");
        \Storage::assertExists("{$video->id}/{$trailerFile->hashName()}");
        \Storage::assertMissing("{$video->id}/{$videoFile->hashName()}");
    }

    public function testUpdateIfRoolbackFiles()
    {
        \Storage::fake();
        $video = factory(Video::class)->create();
        \Event::listen(TransactionCommitted::class, function () {
            throw new TestException();
        });
        $hasError = false;

        try {
            $video->update(
                $this->sendData + [
                    'thumb_file' => UploadedFile::fake()->image('thumb.jpg'),
                    'banner_file' => UploadedFile::fake()->image('banner.jpg'),
                    'trailer_file' => UploadedFile::fake()->image('trailer.jpg'),
                    'video_file' => UploadedFile::fake()->create('video.mp4'),
                ]
            );
        } catch (TestException $exception) {
            $this->assertCount(0, \Storage::allFiles());
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }

}
