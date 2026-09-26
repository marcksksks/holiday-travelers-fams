<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Visitor Traffic Forecast
    |--------------------------------------------------------------------------
    |
    | This feature runs locally and does not require an external AI API.
    | Machine-learning forecasting is enabled only after enough historical
    | check-in data exists.
    |
    */

    /*
     * Academic demonstration mode.
     *
     * Synthetic historical observations are created in memory only.
     * No synthetic operational rows are inserted into PostgreSQL.
     */
    'demo_mode' => filter_var(
        env(
            'VISITOR_FORECAST_DEMO_MODE',
            false
        ),
        FILTER_VALIDATE_BOOL
    ),

    'history_days' => 90,

    'minimum_history_visits' => 100,

    'minimum_history_days' => 21,

    'operating_start_hour' => 8,

    'operating_end_hour' => 17,

    'neighbors' => 12,

    /*
     * These staffing thresholds are business rules.
     * They are not part of the machine-learning prediction.
     */
    'staffing' => [
        'normal_max' => 5,
        'moderate_max' => 10,
        'high_max' => 15,
    ],

];
