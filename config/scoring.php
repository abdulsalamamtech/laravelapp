<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Scoring mode
    |--------------------------------------------------------------------------
    |
    | 'legacy'  -> Reproduces the exact formula + field set of
    |              App\Models\BusinessMetric::calculate*Score() so the swap
    |              is seamless (identical scores, except the filing typo fix).
    |
    | 'config'  -> Definition-driven mode. Reads active BusinessMetricInput
    |              rows (types/status/priority/point) to decide which fields
    |              feed each score. Uses priority weights, a neutral baseline
    |              for missing booleans instead of 0, no "+5 fudge" in the
    |              denominator, and caps the LLM stage blend at 15%.
    |
    */
    'mode' => env('SCORING_MODE', 'legacy'),

    /*
    |--------------------------------------------------------------------------
    | Apply the filing typo fix
    |--------------------------------------------------------------------------
    |
    | has_filing_history / has_filling_history were the same fact counted twice
    | (only the DB column has_filling_history exists). The old TRS formulas added
    | a dead 0 field (denominator +1, total +0), dragging every TRS score down.
    | Kept on by default even in legacy mode; disable to reproduce the old buggy
    | numbers byte-for-byte.
    |
    */
    'apply_filing_typo_fix' => env('SCORING_APPLY_FILING_TYPO_FIX', true),

    /*
    |--------------------------------------------------------------------------
    | Read-path freshness
    |--------------------------------------------------------------------------
    |
    | How old a synced score may be before the read path recomputes it IN MEMORY
    | (pure, read-only - never writes to the DB) instead of returning the stored
    | value. Keeps keyMetrics()/getBhs()/... from recomputing + rewriting on every
    | page load while still reflecting fixes within this window.
    |
    */
    'freshness_minutes' => env('SCORING_FRESHNESS_MINUTES', 60 * 24),

    /*
    |--------------------------------------------------------------------------
    | Read-path cache TTL (seconds)
    |--------------------------------------------------------------------------
    */
    'cache_ttl' => env('SCORING_CACHE_TTL', 600),

    /*
    |--------------------------------------------------------------------------
    | Priority weights used in 'config' mode
    |--------------------------------------------------------------------------
    */
    'priority_weights' => [
        'critical' => 1.0,
        'important' => 0.75,
        'supporting' => 0.5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Neutral baseline for missing booleans in 'config' mode
    |--------------------------------------------------------------------------
    | 0.25 = a false/absent boolean keeps 25% of its possible points instead of 0.
    */
    'baseline_ratio' => env('SCORING_BASELINE_RATIO', 0.25),

    /*
    |--------------------------------------------------------------------------
    | Maximum influence of the LLM stage array in 'config' mode (0-1)
    |--------------------------------------------------------------------------
    */
    'stage_cap' => env('SCORING_STAGE_CAP', 0.15),

    /*
    |--------------------------------------------------------------------------
    | Composite health score display scale
    |--------------------------------------------------------------------------
    |
    | The composite health score (0-100 average of BHS/CRS/IRS/TRS) is rescaled
    | by this multiplier AT THE API OUTPUT boundary only. The underlying compute,
    | stored scores and the 0-100 threshold logic (getStatusBadge / getRiskBand /
    | progressType / confidentLevelBadge) are left untouched so labels stay
    | correct. Set to 1 to revert to the default 0-100 display format.
    | set 1 : 100%, 10 : 1000  and 100 : 10000
    |
    */
    'health_scale' => (float) env('SCORING_HEALTH_SCALE', 10),
];
