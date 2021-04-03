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
                'video_file' => UploadedFile::fake()->image('video.jpg'),
                'thumb_file' => UploadedFile::fake()->create('thumb.jpg')
            ]
        );
        //dump($video->toArray());
        \Storage::assertExists("{$video->id}/{$video->video_file}");
        \Storage::assertExists("{$video->id}/{$video->thumb_file}");
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
                    'thumb_file' => UploadedFile::fake()->image('thumb.jpg')
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
        $videoFile = UploadedFile::fake()->create("video.mp4");
        $thumbFile = UploadedFile::fake()->image("thumb.jpg");
        $video->update($this->sendData + [
                'video_file' => $videoFile,
                'thumb_file' => $thumbFile
            ]);
        \Storage::assertExists("{$video->id}/{$video->video_file}");
        \Storage::assertExists("{$video->id}/{$video->thumb_file}");

        $newVideoFile = UploadedFile::fake()->image("video.mp4");
        $video->update($this->sendData + [
                'video_file' => $newVideoFile
            ]);
        \Storage::assertExists("{$video->id}/{$newVideoFile->hashName()}");
        \Storage::assertExists("{$video->id}/{$thumbFile->hashName()}");
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
                    'video_file' => UploadedFile::fake()->create('video.mp4'),
                    'thumb_file' => UploadedFile::fake()->image('thumb.jpg')
                ]
            );
        } catch (TestException $exception) {
            $this->assertCount(0, \Storage::allFiles());
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }

}
