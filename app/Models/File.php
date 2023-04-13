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
        'md5'
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

    public function getPathAttribute(): string
    {
        return sprintf('%d/%s', $this->app_id, $this->name);
    }
}
