<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Stringable;

class Avatar implements Stringable
{
    private const DISK = 'public';
    private const DIRECTORY = 'avatars';
    private const FALLBACK = '/images/default-avatar.svg';

    public function __construct(
        private ?string $path = null,
    ) {}

    public function __toString(): string
    {
        return $this->url();
    }

    public function url(): string
    {
        if ($this->path) {
            return Storage::disk(self::DISK)->url($this->path);
        }

        return self::FALLBACK;
    }

    public function exists(): bool
    {
        return $this->path !== null;
    }

    public function store(UploadedFile $file): string
    {
        $this->delete();

        $this->path = $file->store(self::DIRECTORY, self::DISK);

        return $this->path;
    }

    public function delete(): void
    {
        if ($this->path) {
            Storage::disk(self::DISK)->delete($this->path);
            $this->path = null;
        }
    }

    public function path(): ?string
    {
        return $this->path;
    }
}
