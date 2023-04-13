<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class File
 * @package App\Models
 * @author  Dmytro Feshchenko <dimafe2000@gmail.com>
 * @property string $path
 * @property string $uuid
 * @property int $app_id
 * @property string $name
 * @property string $updated_at
 * @property string $created_at
 * @property string $md5
 * @property int $size
 * @property string $human_size
 */
class File extends Model
{
    use HasFactory;

    public $fillable = [
        'uuid',
        'app_id',
        'name',
        'updated_at',
        'created_at',
        'md5',
        'size'
    ];

    protected static function booted(): void
    {
        static::creating(function (File $file) {
            $file->uuid = Str::uuid()->toString();
        });

        static::deleting(function (File $file) {
            Storage::delete($file->path);
        });
    }

    /**
     * @return BelongsTo
     */
    public function app()
    {
        return $this->belongsTo(App::class);
    }

    /**
     * @return string
     */
    public function getPathAttribute(): string
    {
        return sprintf('%d/%s', $this->app_id, $this->name);
    }

    /**
     * @return string
     */
    function getHumanSizeAttribute(): string
    {
        $i = floor(log($this->size, 1024));

        return round($this->size / pow(1024, $i), [0, 0, 2, 2, 3][$i]) . ['B', 'KB', 'MB', 'GB', 'TB'][$i];
    }
}
