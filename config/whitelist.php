<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Allowed IP Cache TTL
    |--------------------------------------------------------------------------
    |
    | How long (in seconds) the resolved list of allowed IPs / CIDR ranges /
    | hostname lookups is cached. The cache is also flushed immediately
    | whenever an entry is created, updated, or deleted, so this TTL only
    | bounds how long DNS-only changes for hostname entries stay stale.
    |
    */

    'cache_ttl' => (int) env('WHITELIST_CACHE_TTL', 300),

];
