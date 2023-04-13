<?php

namespace App\Models;

use App\Observers\AppObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;

class App extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public $fillable = [
        'name',
        'token_lifetime',
        'deleted_at',
        'slack_notification_webhook_url',
        'slack_notification_channel',
        'updated_at',
        'created_at',
        'webhook_url'
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::observe(AppObserver::class);
    }

    /**
     * @return bool
     */
    public function notificationsEnabled()
    {
        return ($this->slack_notification_channel && $this->slack_notification_webhook_url) || $this->webhook_url;
    }

    /**
     * @param Notification $notification
     * @return string
     */
    public function routeNotificationForSlack(Notification $notification)
    {
        return $this->slack_notification_webhook_url;
    }

    /**
     * @param Notification $notification
     * @return string
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function routeNotificationForWebhook(Notification $notification)
    {
        return $this->webhook_url;
    }

    /**
     * @return BelongsToMany
     */
    public function collaborators()
    {
        return $this->belongsToMany(User::class, 'app_collaborators')
            ->withPivot([
                'role',
            ])
            ->withTimestamps();
    }

    /**
     * @return MorphMany
     */
    public function log()
    {
        return $this->morphMany(LogEntry::class, 'loggable');
    }

    /**
     * @return HasMany
     */
    public function setup_tokens()
    {
        return $this->hasMany(AppSetupToken::class);
    }

    /**
     * @return AppSetupToken|null
     */
    public function existsToken(): ?AppSetupToken
    {
        $tokenLifetime = $this->token_lifetime;

        /** @var AppSetupToken $token */
        $token = $this->setup_tokens()->whereRaw(
            "DATE_ADD(created_at,INTERVAL $tokenLifetime MINUTE) > created_at"
        )->first();

        return $token;
    }

    /**
     * @return HasMany
     */
    public function variables()
    {
        return $this->hasMany(Variable::class);
    }

    /**
     * @return HasMany
     */
    public function files()
    {
        return $this->hasMany(File::class);
    }
}
