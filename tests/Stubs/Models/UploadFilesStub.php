<?php

namespace Tests\Stubs\Models;

use App\Models\Traits\UploadFiles;
use Illuminate\Database\Eloquent\Model;

class UploadFilesStub extends Model
{
    use UploadFiles;

//    protected $table = 'categories_stubs';
//    protected $fillable = ['name', 'description'];
//
//    public static function createTable()
//    {
//        \Schema::create('categories_stubs', function (Blueprint $table) {
//            $table->bigIncrements('id');
//            $table->string("name");
//            $table->string("description")->nullable();
//            $table->timestamps();
//        });
//    }
//    public static function dropTable()
//    {
//        \Schema::dropIfExists('categories_stubs');
//    }

    protected function uploadDir()
    {
        return "1";
    }
}
