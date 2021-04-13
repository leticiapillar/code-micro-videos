<?php

namespace App\Models;

use App\Models\Traits\UploadFiles;
use App\Models\Traits\Uuid;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Models\Video
 *
 * @property string $id
 * @property string $title
 * @property string $description
 * @property int $year_lauched
 * @property bool $opened
 * @property string $rating
 * @property int $duration
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Video newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Video newQuery()
 * @method static Builder|Video onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Video query()
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereOpened($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereYearLauched($value)
 * @method static Builder|Video withTrashed()
 * @method static Builder|Video withoutTrashed()
 * @mixin Eloquent
 * @property-read Collection|Category[] $categories
 * @property-read int|null $categories_count
 * @property-read Collection|Video[] $videos
 * @property-read int|null $videos_count
 * @property-read Collection|Genre[] $genres
 * @property-read int|null $genres_count
 * @property string|null $video_file
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereVideoFile($value)
 * @property string|null $thumb_file
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereThumbFile($value)
 * @property string|null $banner_file
 * @property string|null $trailer_file
 * @property-read mixed $banner_file_url
 * @property-read mixed $thumb_file_url
 * @property-read mixed $trailer_file_url
 * @property-read mixed $video_file_url
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereBannerFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereTrailerFile($value)
 */
class Video extends Model
{
    use SoftDeletes, Uuid, UploadFiles;

    const RATING_LIST = ['L', '10', '12', '14', '16', '18'];

    const THUMB_FILE_MAX_SIZE = 1024 * 5; //5MB
    const BANNER_FILE_MAX_SIZE = 1024 * 10; //10MB
    const TRAILER_FILE_MAX_SIZE = 1024 * 1024 * 1; //1GB
    const VIDEO_FILE_MAX_SIZE = 1024 * 1024 * 50; //50GB

    protected $fillable = ['title', 'description', 'year_lauched', 'opened', 'rating', 'duration',
        'video_file', 'thumb_file', 'banner_file', 'trailer_file'];
    protected $dates = ['deleted_at'];
    protected $casts = [
        'opened' => 'boolean',
        'year_lauched' => 'integer',
        'duration' => 'integer'
    ];

    public $incrementing = false;
    protected $keyType = 'string';
    public static $fileFields = ['video_file', 'thumb_file', 'banner_file', 'trailer_file'];

    public static function create(array $attributes = [])
    {
        $files = self::extractFiles($attributes);
        try {
            \DB::beginTransaction();
            /** @var Video $obj */
            $obj = static::query()->create($attributes);
            static::handleRelations($obj, $attributes);
            $obj->uploadFiles($files);
            \DB::commit();
            return $obj;
        } catch (\Exception $exception) {
            if (isset($obj)) {
                $obj->deleteFiles($files);
            }
            \DB::rollBack();
            throw $exception;
        }
    }

    public function update(array $attributes = [], array $options = [])
    {
        $files = self::extractFiles($attributes);
        try {
            \DB::beginTransaction();
            $saved = parent::update($attributes, $options);
            static::handleRelations($this, $attributes);
            if ($saved) {
                $this->uploadFiles($files);
            }
            \DB::commit();
            if ($saved && count($files)) {
                $this->deleteOldFiles();
            }
            return $saved;
        } catch (\Exception $exception) {
            $this->deleteFiles($files);
            \DB::rollBack();
            throw $exception;
        }
        return parent::update($attributes, $options);
    }

    public static function handleRelations(Video $video, array $attributes)
    {
        if (isset($attributes['categories_id'])) {
            $video->categories()->sync($attributes['categories_id']);
        }
        if (isset($attributes['genres_id'])) {
            $video->genres()->sync($attributes['genres_id']);
        }
    }

    public function categories()
    {
        // Se uma categoria for excluida logicamente, ela ainda aparece na lista de categirias do Video
        return $this->belongsToMany(Category::class)->withTrashed();
    }

    public function genres()
    {
        // Se um gênero for excluida logicamente, ela ainda aparece na lista de generos do Video
        return $this->belongsToMany(Genre::class)->withTrashed();
    }

    protected function uploadDir()
    {
        return $this->id;
    }

    public function getThumbFileUrlAttribute()
    {
        return $this->thumb_file ? $this->getFileUrl($this->thumb_file) : null;
    }

    public function getBannerFileUrlAttribute()
    {
        return $this->banner_file ? $this->getFileUrl($this->banner_file) : null;
    }

    public function getTrailerFileUrlAttribute()
    {
        return $this->trailer_file ? $this->getFileUrl($this->trailer_file) : null;
    }

    public function getVideoFileUrlAttribute()
    {
        return $this->video_file ? $this->getFileUrl($this->video_file) : null;
    }
}
