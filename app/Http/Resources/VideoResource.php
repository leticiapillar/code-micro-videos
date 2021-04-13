<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
//        return parent::toArray($request);
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'year_lauched' => $this->year_lauched,
            'opened' => $this->opened,
            'rating' => $this->rating,
            'duration' => $this->duration,
            'thumb_file_url'=>$this->getThumbFileUrlAttribute(),
            'banner_file_url'=>$this->getBannerFileUrlAttribute(),
            'trailer_file_url'=>$this->getTrailerFileUrlAttribute(),
            'video_file_url'=>$this->getVideoFileUrlAttribute(),
            'categories' => CategoryResource::collection($this->categories),
            'genres' => GenreResource::collection($this->genres),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            "deleted_at" => $this->updated_at,
        ];
    }
}
