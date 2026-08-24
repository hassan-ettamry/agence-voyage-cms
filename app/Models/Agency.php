<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Agency extends Model
{
    public const ONBOARDING_PENDING = 'pending';
    public const ONBOARDING_IN_PROGRESS = 'in_progress';
    public const ONBOARDING_COMPLETED = 'completed';

    public const ONBOARDING_STEP_PROFILE = 'profile';
    public const ONBOARDING_STEP_TEMPLATE = 'template';
    public const ONBOARDING_STEP_THEME = 'theme';
    public const ONBOARDING_STEP_REVIEW = 'review';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'logo',
        'settings',
        'plan',
        'status',
        'theme_id',
        'theme_overrides',
        'active_site_template_id',
        'template_applied_at',
        'onboarding_status',
        'onboarding_step',
        'onboarding_data',
        'onboarding_auto_start',
        'onboarding_completed_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'theme_overrides' => 'array',
        'template_applied_at' => 'datetime',
        'onboarding_data' => 'array',
        'onboarding_auto_start' => 'boolean',
        'onboarding_completed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) Str::uuid();

            if (! $model->slug) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function destinations()
    {
        return $this->hasMany(Destination::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function mediaAssets()
    {
        return $this->hasMany(MediaAsset::class);
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }

    public function activeSiteTemplate()
    {
        return $this->belongsTo(SiteTemplate::class, 'active_site_template_id');
    }

    public function siteArchives()
    {
        return $this->hasMany(SiteArchive::class);
    }

    public function onboardingIsComplete(): bool
    {
        return $this->onboarding_status === self::ONBOARDING_COMPLETED;
    }

    public function shouldAutoStartOnboarding(): bool
    {
        return $this->onboarding_auto_start && ! $this->onboardingIsComplete();
    }

    public function catalogCurrency(): string
    {
        $currency = strtoupper((string) data_get($this->settings, 'catalog.currency', 'MAD'));

        return preg_match('/^[A-Z]{3}$/', $currency) === 1 ? $currency : 'MAD';
    }
}
