<?php

namespace Impactaweb\Crud\Traits;

use Illuminate\Support\Facades\Storage;
use stdClass;

trait Upload
{
    /**
     * Upload specific field from request
     * @param string $field
     * @param object $options
     * @param $request
     * @return object
     */
    public function saveFile(string $field, object $options, $request): object
    {
        $success = new stdClass();
        $err = new stdClass();

        if ($request->hasFile($field)) {
            $file = $request->file($field);

            if (in_array($request->$field->extension(), $options->extensions)) {
                $path = $file->store('tmp');

                if (!$path) {
                    $err->$field = "Falha no upload do {$file->getClientOriginalName()}{$file->extension()}";
                }

                $success->$field = (object)[
                    'url' => url('storage/' . $path),
                    'path' => $path,
                    'hashName' => strpos($path, '/') ? str_replace('/', '', mb_strrchr($path, '/')) : $path
                ];
            } else {
                $err->$field = "O arquivo {$file->getClientOriginalName()}{$file->extension()} não possui uma extensão válida";
            }

        }

        return (object)[
            'error' => count((array)$err) === 0 ? false : $err,
            'success' => $success
        ];
    }

    /**
     * Upload all files from request
     * @param object $options
     * @param $request
     * @return array
     */
    public function saveFiles(object $options, $request): array
    {
        $err = [];
        $success = [];

        foreach ($options->fields as $field) {
            $file = null;

            if ($request->hasFile($field) && $request->$field->isValid()) {
                $file = $request->file($field);

                if (in_array($request->$field->extension(), $options->extensions)) {
                    $path = $file->store('tmp');
                    if (!$path) {
                        $err[$field] = "Falha no upload do {$file->getClientOriginalName()}{$file->extension()}";
                    } else {
                        $success[$field] = (object)[
                            'url' => url('storage/' . $path),
                            'path' => $path,
                            'hashName' => strpos($path, '/') ? str_replace('/', '', mb_strrchr($path, '/')) : $path
                        ];
                    }

                } else {
                    $err[$field] = "O arquivo {$file->getClientOriginalName()}{$file->extension()} não possui uma extensão valida";
                }

            }
        }

        return [
            'error' => count((array)$err) === 0 ? false : $err,
            'success' => $success
        ];
    }


    /**
     * Delete file from path
     * @param string $path
     * @param string $clientFolder
     * @return bool
     */
    public function destroyFile(string $path, string $clientFolder = 'tmp'): bool
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
     * Move file from tmp folder to specific folder
     * @param string $fileName File hash name
     * @param string $folderDestiny
     * @return bool
     */

    public function move(string $fileName, string $folderDestiny): bool
    {
        if (file_exists(storage_path('tmp/' . $fileName))) {
            return Storage::move('tmp/' . $fileName, 'app/public/' . $folderDestiny .'/'. $fileName);
        }
        return false;
    }
}
