<?php

namespace Kohaku1907\Laravel2step\Models;

use \Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Kohaku1907\Laravel2step\Models\Concerns\HandlesAuth;


class TwoStepAuth extends Model
{
    use HandlesAuth;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'authenticatable_id',
        'authenticatable_type',
        'channel',
        'code',
        'count',
        'enabled_at',
        'last_verified_at',
        'request_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'enabled_at' => 'datetime',
        'last_verified_at' => 'datetime',
        'request_at' => 'datetime',
        'count' => 'int',
    ];

    /**
     * Get the database connection.
     */
    public function getTableName()
    {
        return config('2step.table_name');
    }

    /**
     * The model that uses 2Step Authentication.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function authenticatable(): MorphTo
    {
        return $this->morphTo('authenticatable');
    }

    /**
     * Returns if the Two-Step Authentication has been enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled_at !== null;
    }

    /**
     * Returns if the Two-Step Authentication is not been enabled.
     *
     * @return bool
     */
    public function isDisabled(): bool
    {
        return ! $this->isEnabled();
    }

    /**
     * Enable Two-Step Authentication.
     * 
     * @param string $channel
     * @return void
     */
    public function enable($channel): void
    {
        $this->channel = $channel;
        $this->enabled_at = now();
        $this->save();
    }

    /**
     * Disable Two-Step Authentication.
     *
     * @return void
     */
    public function disable(): void
    {
        $this->enabled_at = null;
        $this->save();
    }
}