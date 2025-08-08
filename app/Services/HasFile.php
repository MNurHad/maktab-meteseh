<?php

namespace App\Services;

use App\Models\File;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HasFile
{
    /**]
     * @param $file
     * @param string $directory
     * @return mixed
     */
    protected function uploadToDisk($file, $directory)
    {
        return Storage::putFile($directory, $file);
    }

    /**'
     * @param $model
     * @return void
     */
    protected function deleteFromDisk($model): void
    {
        Storage::disk($model->disk)
            ->delete($model->path);
    }

    /**
     * @return string
     */
    protected function getDisk(): string
    {
        return config('filesystems.default');
    }

    /**
     * @return MorphOne
     */
    public function file(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }

    /**
     * @return MorphMany
     */
    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }

    /**
     * @return mixed
     */
    public function getFileUrlAttribute()
    {
        return optional($this->file)->url;
    }

    /**
     * @return mixed
     */
    public function getFileTypeAttribute()
    {
        return optional($this->file)->type;
    }

    /**
     * @return array
     */
    public function getFileUrlsAttribute(): array
    {
        return $this->files->map(function ($file) {
            return $file->url;
        })->toArray();
    }

    /**
     * @return array
     */
    public function getFileResourcesAttribute(): array
    {
        return $this->files
            ->map(function ($image) {
                $size = 0;
                if (Storage::disk($image->disk)->exists($image->path)) {
                    $size = Storage::disk($image->disk)->size($image->path);
                }
                return [
                    'url' => $image->url,
                    'file' => [
                        'name' => $image->name,
                        'size' => $size,
                        'id' => $image->id
                    ],
                ];
            })
            ->toArray();
    }

    /**
     * @return null
     */
    public function getThumbnailUrlAttribute()
    {
        if (!empty($this->thumbnail)) {
            foreach (File::DISK_STORAGE as $disk) {
                if (Storage::disk($disk)->exists($this->thumbnail)) {
                    return Storage::disk($disk)->url($this->thumbnail);
                }
            }
        }

        return null;
    }

    /**
     * @return void
     */
    public function deleteThumbnail(): void
    {
        if (!empty($this->thumbnail)) {
            foreach (File::DISK_STORAGE as $disk) {
                if (Storage::disk($disk)->exists($this->thumbnail)) {
                    Storage::disk($disk)->delete($this->thumbnail);
                    break;
                }
            }
        }
    }
    /**
     * @param $file
     * @param string $directory
     * @param array $params
     * @return mixed
     */
    public function saveFileBaru($file, $directory, $params = [], $disk = null)
    {
        $name = $file->getClientOriginalName();
        $name = pathinfo($name, PATHINFO_FILENAME);

        if (!$disk) {
            $disk = $this->getDisk();
        }
        $path =   Storage::disk($disk)->put($directory, $file);


        $data = compact('path', 'disk', 'name') + $params;

        if ($model = $this->file) {
            $this->deleteFromDisk($model);

            $this->file()
                ->update($data);
            $model = $model->refresh();
        } else {
            $model = $this->file()
                ->create($data);
        }

        return $model;
    }
    /**
     * @param $file
     * @param string $directory
     * @param array $params
     * @return mixed
     */
    public function saveFile($file, $directory, $params = [])
    {
        $name = $file->getClientOriginalName();
        $name = pathinfo($name, PATHINFO_FILENAME);
        $path = $this->uploadToDisk($file, $directory);
        $disk = $this->getDisk();
        $data = compact('path', 'disk', 'name') + $params;

        if ($model = $this->file) {
            $this->deleteFromDisk($model);

            $this->file()
                ->update($data);
            $model = $model->refresh();
        } else {
            $model = $this->file()
                ->create($data);
        }

        return $model;
    }

    /**
     * @param $files
     * @param string $directory
     * @param array $params
     * @return void
     */
    public function saveFiles($files, $directory, $params = []): void
    {
        $disk = $this->getDisk();
        $files = collect($files)
            ->map(function ($file, $key) use ($directory, $disk, $params) {
                $isMulti = is_array($params[0] ?? null);
                $filename = $file->getClientOriginalName();
                $filename = pathinfo($filename, PATHINFO_FILENAME);
                $data = [
                    'name' => $filename,
                    'disk' => $disk,
                    'path' => $this->uploadToDisk($file, $directory),
                ];

                if ($isMulti) {
                    return $data + $params[$key];
                }

                return $data + $params;
            })
            ->toArray();

        $this->files()
            ->createMany($files);
    }

    /**
     * @param bool $force
     * @return void
     */
    public function deleteFile($force = true): void
    {
        if ($model = $this->file) {
            if ($force) {
                $this->deleteFromDisk($model);
            }

            $this->file()
                ->delete();
        }
    }

    /**
     * @param array $ids
     * @return void
     */
    public function deleteFiles(array $ids = []): void
    {
        $files = empty($ids)
            ? $this->files
            : $this->files->whereIn('id', $ids);

        if ($files->isEmpty()) {
            return;
        }

        $files->each(function ($file) {
            $this->deleteFromDisk($file);
        });

        if (empty($ids)) {
            $this->files()->delete();
        } else {
            $this->files()->whereIn('id', $ids)
                ->delete();
        }
    }
    public function uploadBase64($base64, $directory, $params = [])
    {

        $image_64 = $base64; //your base64 encoded data

        $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf

        $replace = substr($image_64, 0, strpos($image_64, ',') + 1);

        // find substring fro replace here eg: data:image/png;base64,

        $image = str_replace($replace, '', $image_64);

        $image = str_replace(' ', '+', $image);
        // $directory = 'public/' . $directory;
        $imageName = Str::random(10) . '.' . $extension;
        $name = $imageName;

        $path = $directory . "/" . $imageName;
        $disk = $this->getDisk();
        $data = compact('path', 'disk', 'name') + $params;
        Storage::disk($disk)->put($directory . "/" . $imageName, base64_decode($image));
        if ($model = $this->file) {
            $this->deleteFromDisk($model);

            $this->file()
                ->update($data);
            $model = $model->refresh();
        } else {
            $model = $this->file()
                ->create($data);
        }

        return $model;
        //
    }

    public function uploads($filesUpload, $path)
    {

        $disk = $this->getDisk();


        $filename = $filesUpload->getClientOriginalName();
        $filename = pathinfo($filename, PATHINFO_FILENAME);
        $data = [
            'name_img' => $filename,
            'disk' => $disk,
            'path' => $this->uploadToDisk($filesUpload, $path),
        ];



        return $data;
    }

    public function deleteSpeakerAttach()
    {

        $this->files()->where('type', 'attachment')
            ->delete();
    }
    public function deleteSpeakerImage()
    {
        $this->files()->where('type', 'image')
            ->delete();
    }
}
