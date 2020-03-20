<?php

namespace Impactaweb\Crud\Traits;

use Illuminate\Validation\ValidationException;

trait Upload
{

    private $savedFiles = [];

    /**
     * @param array $savedFiles
     */
    public function setSavedFiles(array $savedFiles): void
    {
        $this->savedFiles = $savedFiles;
    }

    /**
     * @return array
     */
    public function getSavedFiles(): array
    {
        return $this->savedFiles;
    }

    /**
     * Upload specific field from request
     * @param string $field
     * @param string $folder
     * @param bool $fullpath
     */
    public function saveFile(string $field, string $folder, bool $fullpath = false)
    {
        $request = request();
        if ($request->hasFile($field)) {
            $file = $request->file($field);
            $path = $file->store($this->pathJoins($this->getPublicFolder(), $folder));
            if (file_exists($path)) {
                throw ValidationException::withMessages(
                    [$fieldName => "Falha no upload do {$file->getClientOriginalName()}{$file->extension()}"]);
            }
            if ($fullpath) {
                $files = $this->getSavedFiles();
                $files[$field] = $path;
                $this->setSavedFiles($files);
                return $path;
            } else {
                $files = $this->getSavedFiles();
                $files[$field] = $file->hashName();
                $this->setSavedFiles($files);
                return $file->hashName();
            }
        }
    }

    /**
     * @return string
     */
    public function getStorageFolder(): string
    {

        return config('form.upload.storage_folder', 'storage');
    }

    /**
     * @return string
     */
    public function getTempFolder(): string
    {
        return config('form.upload.temp_folder', 'tmp');
    }

    /**
     * @return string
     */
    public function getPublicFolder(): string
    {
        return config('form.upload.public_folder', 'app/public');
    }

    private function pathJoins(string $path1, string $path2)
    {
        $path1 = explode('/', $path1);
        $path2 = explode('/', $path2);
        return join('/', array_merge($path1, $path2));
    }


    /**
     * Delete file from path
     * @param string $path Path completo onde se encontra o arquivo
     * @return bool
     */
    public function destroyFile(string $path): bool
    {
        if (file_exists(storage_path($path))) {
            return Storage::move($path, storage_path('trash/' . $path));
        } else {
            return false;
        }
    }

}
