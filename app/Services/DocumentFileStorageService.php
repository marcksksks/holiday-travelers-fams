<?php

namespace App\Services;

use Closure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class DocumentFileStorageService
{
    public function create(
        UploadedFile $file,
        string $directory,
        Closure $persist
    ): mixed {
        $newPath = $this->storeOrFail(
            $file,
            $directory
        );

        try {
            return DB::transaction(
                fn () => $persist($newPath)
            );
        } catch (Throwable $exception) {
            Storage::disk('documents')
                ->delete($newPath);

            throw $exception;
        }
    }

    public function replace(
        UploadedFile $file,
        string $directory,
        ?string $oldPath,
        Closure $persist
    ): mixed {
        $newPath = $this->storeOrFail(
            $file,
            $directory
        );

        try {
            $result = DB::transaction(
                fn () => $persist($newPath)
            );
        } catch (Throwable $exception) {
            Storage::disk('documents')
                ->delete($newPath);

            throw $exception;
        }

        if (
            filled($oldPath) &&
            $oldPath !== $newPath
        ) {
            Storage::disk('documents')
                ->delete($oldPath);
        }

        return $result;
    }

    private function storeOrFail(
        UploadedFile $file,
        string $directory
    ): string {
        $path = $file->store(
            $directory,
            'documents'
        );

        if (
            ! is_string($path) ||
            trim($path) === ''
        ) {
            throw new RuntimeException(
                'Unable to store the document file.'
            );
        }

        return $path;
    }
}