<?php

namespace App\Models;

use App\Observers\VariableObserver;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Variable
 * @package App\Models
 * @author  Dmytro Feshchenko <dimafe2000@gmail.com>
 * @property Collection $versions
 */
class Variable extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['latest_version'];

    protected $with = ['versions', 'app'];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::observe(VariableObserver::class);
    }

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['latest_version'];

    /**
     * @return VariableVersion|null
     */
    public function getLatestVersionAttribute()
    {
        return $this->versions->last();
    }

    /**
     * @return HasMany
     */
    public function versions()
    {
        return $this->hasMany(VariableVersion::class);
    }

    /**
     * @return BelongsTo
     */
    public function app()
    {
        return $this->belongsTo(App::class);
    }
}
