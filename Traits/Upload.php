<?php

namespace Impactaweb\Crud\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

trait Upload
{

    private $tempFiles = [];
    private $publicFolder = '';
    private $storageFolder = '';
    private $tempFolder = '';

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        # Get folders from config
        $this->setTempFolder(config('form.upload.temp_folder', 'tmp'));
        $this->setPublicFolder(config('form.upload.public_folder', 'app/public'));
        $this->setStorageFolder(config('form.upload.storage_folder', 'storage'));
    }

    /**
     * Upload specific field from request
     * @param string $field
     * @param string $folder
     * @return string
     * @throws ValidationException
     */
    public function saveFileFromRequest(string $field, string $folder, bool $fullpath = false): string
    {
        $request = request();
        $file = $request->file($field);
        $path = $file->store($this->pathJoins($this->getPublicFolder(), $folder));
        if (file_exists($path)) {
            throw ValidationException::withMessages(
                [$fieldName => "Falha no upload do {$file->getClientOriginalName()}{$file->extension()}"]);
        }
        if ($fullpath) {
            return $path;
        } else {
            return $file->hashName();
        }

    }

    /**
     * @return string
     */
    public function getStorageFolder(): string
    {

        return $this->storageFolder;
    }

    /**
     * @param string $storageFolder
     */
    public function setStorageFolder(string $storageFolder): void
    {
        $this->storageFolder = $storageFolder;
    }

    /**
     * @return string
     */
    public function getTempFolder(): string
    {
        return $this->tempFolder;
    }

    /**
     * @param string $tempFolder
     */
    public function setTempFolder(string $tempFolder): void
    {
        $this->tempFolder = $tempFolder;
    }

    /**
     * Delete file from path
     * @param string $path
     * @param string $clientFolder
     * @return bool
     */
    public function destroyFile(string $path, string $clientFolder): bool
    {
        if (file_exists(storage_path($clientFolder . '/' . $path))) {
            return Storage::delete($path);
        } else if (file_exists(storage_path($clientFolder . $path))) {
            return Storage::delete($clientFolder . $path);
        } else {
            return false;
        }
    }

    /**
     * @return string
     */
    public function getPublicFolder(): string
    {
        return $this->publicFolder;
    }

    /**
     * @param string $publicFolder
     */
    public function setPublicFolder(string $publicFolder): void
    {
        $this->publicFolder = $publicFolder;
    }

    private function pathJoins(string $path1, string $path2)
    {
        $path1 = explode('/', $path1);
        $path2 = explode('/', $path2);
        return join('/', array_merge($path1, $path2));
    }
}
