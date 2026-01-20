<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Company Type Options
    |--------------------------------------------------------------------------
    |
    | You can provide a mapping of company `type_code` => label here. By
    | default the array below is used. To override from your `.env` file set
    | `COMPANY_TYPE_OPTIONS` to a JSON object, e.g.: 
    |
    | COMPANY_TYPE_OPTIONS='{"242":"UAB","111":"Ne pelno įstaigos, organizacijos"}'
    |
    | For simple overrides you may also use a comma-separated list of
    | key:value pairs: `242:UAB,111:Ne pelno ...`
    |
    */

    'types' => (function () {
        $env = env('COMPANY_TYPE_OPTIONS', null);

        if ($env) {
            $decoded = json_decode($env, true);
            if (is_array($decoded)) {
                return $decoded;
            }

            $pairs = array_filter(array_map('trim', explode(',', $env)));
            $out = [];

            foreach ($pairs as $p) {
                $parts = array_map('trim', explode(':', $p, 2));
                if (!empty($parts[0])) {
                    $out[$parts[0]] = $parts[1] ?? $parts[0];
                }
            }

            if (!empty($out)) {
                return $out;
            }
        }

        return [
        ];
    })(),
];
