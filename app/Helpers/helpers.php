<?php

/**
 * @return array|mixed
 */
function translations($json)
{
    if (! file_exists($json)) {
        return [];
    }

    return json_decode(file_get_contents($json), true);
}

/**
 * Map function for vuetify selects
 *
 * @return array
 */
function mapForSelect($values, $sameValue = false, $translate = true)
{
    if ($values instanceof \Illuminate\Support\Collection) {
        $values = $values->toArray();
    }

    return collect($values)->map(function ($value, $key) use (&$sameValue, &$translate) {
        return [
            'value' => $sameValue ? $value : $key,          // v-select value
            'text' => $translate ? __($value) : $value,    // v-select text
        ];
    })->values()->toArray();
}

/**
 * Inverse of mapForSelect function
 *
 * @return mixed[]
 */
function mapFromSelect(array $values, string $key = 'text')
{
    return collect($values)->map(function ($value) use ($key) {
        if (is_null($value)) {
            return null;
        }

        if (is_scalar($value)) {
            return $value;
        }

        return $value[$key];
    })->filter()->values()->toArray();
}

/**
 * @return string
 */
function createUrl($baseUrl, array $params)
{
    $queryString = http_build_query($params);

    if (strpos($baseUrl, '?') === false) {
        return $baseUrl.'?'.$queryString;
    } else {
        return $baseUrl.'&'.$queryString;
    }
}

/**
 * Recursive multidimensional array key search
 */
function findKey($keySearch, $array): bool
{
    // check if it's even an array
    if (! is_array($array)) {
        return false;
    }

    // key exists
    if (array_key_exists($keySearch, $array)) {
        return true;
    }

    // key isn't in this array, go deeper
    foreach ($array as $key => $val) {
        // return true if it's found
        if (findKey($keySearch, $val)) {
            return true;
        }
    }

    return false;
}

function isImage(string $extension): bool
{
    return in_array($extension, config('filesystems.image_extensions'));
}

function getCurrencyFormat(float $amount, string $currency = 'HUF'): string
{
    return rtrim(rtrim(number_format($amount, 2, ',', '.'), '0'), ',').' Ft';
}
