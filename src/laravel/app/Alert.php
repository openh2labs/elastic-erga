<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Alert
 * @package App
 *
 * @property int $id
 * @property string $description
 * @property string $criteria
 * @property string $criteria_total
 * @property string $es_host
 * @property string $es_index
 * @property string $es_type
 * @property string $es_datetime_field
 * @property int $minutes_back
 * @property float $pct_of_total_threshold
 * @property int $pct_alert_state
 * @property string $number_of_hits
 * @property int $number_hit_alert_state
 * @property int $zero_hit_alert_state
 * @property string $alert_email_sender
 * @property string $alert_email_recipient
 * @property string $alert_type
 * @property string $created_at
 * @property string $updated_at
 * @property int $es_config_error_state
 * @property int $librato_id
 * @property string $criteria_temp
 * @property string $criteria_total_temp
 * @property int $consecutive_failures
 * @property int $consecutive_failures_count
 * @property int $consecutive_failures_pct
 * @property int $consecutive_failures_count_pct
 * @property int $consecutive_failures_e0
 * @property int $consecutive_failures_count_e0
 * @property int $alert_enabled_gt0
 * @property int $alert_enabled_gt0_pct
 * @property int $alert_enabled_e0
 */
class Alert extends Model
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
     *s
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
     * scope query to only return config errors
     *
     * @param $query
     * @return mixed
     */
    public function scopeAllESErrors($query){
        return $query->where('es_config_error_state', '=', true);
    }
}
