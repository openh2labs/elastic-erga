<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class alerts extends Model
{
    /**
     * Scope a query to only include all alerts in alert state.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAllState($query)
    {
        return $query->orWhere('pct_alert_state', '=', true)->orWhere('number_hit_alert_state', '=', true)->orWhere('zero_hit_alert_state', '=', true);
    }

    /**
     * Scope a query to only include all alerts in pct alert state.
     *s.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAllPct($query)
    {
        return $query->where('pct_alert_state', '=', true);
    }

    /**
     * Scope a query to only include all alerts in hits alert state.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAllHit($query)
    {
        return $query->where('number_hit_alert_state', '=', true);
    }

    /**
     * Scope a query to only include all alerts in zero hits alert state.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAllZeroHit($query)
    {
        return $query->where('zero_hit_alert_state', '=', true);
    }

    /**
     * scope query to only return config errors.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeAllESErrors($query)
    {
        return $query->where('es_config_error_state', '=', true);
    }
}
