<?php


namespace App\Models\Traits;


use Illuminate\Http\UploadedFile;

trait UploadFiles
{
    protected abstract function uploadDir();

    /**
     * @param UploadedFile $files
     */
    public function uploadFiles(array $files)
    {
        foreach ($files as $file) {
            $this->uploadFile($file);
        }
    }

    public function uploadFile(UploadedFile $file)
    {
        $file->store($this->uploadDir());
    }

    public function deleteFiles(array $files)
    {
        foreach ($files as $file) {
            $this->deleteFile($file);
        }
    }

    /**
     * @param string/UploadFile $file
     */
    public function deleteFile($file)
    {
        $filename = $file instanceof UploadedFile ? $file->hashname() : $file;
        \Storage::delete("{$this->uploadDir()}/{$filename}");
    }

}
