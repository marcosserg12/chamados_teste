<?php
/**
 * Created by PhpStorm.
 * User: felipyamorim
 * Date: 07/08/19
 * Time: 15:38
 */

if (!function_exists('dd')) {
    function dd($var)
    {
        http_response_code(500);
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
        die;
    }
}

if (!function_exists('json_response')) {
    function json_response(array $data, $code = 200)
    {
        header('Content-Type: application/json');
        http_response_code($code);
        return json_encode($data);
    }
}

if ( ! function_exists('redirect')) {
    function redirect($path)
    {
        header("Location: " . $path);
        http_response_code(302);
        die;
    }
}

if (!function_exists('only_numbers')) {
    function only_numbers($string, $allowNull = false) {

        $stringReplaced = preg_replace(
            '/\D/', '', $string
        );

        if($allowNull){
            return $stringReplaced ?: null;
        }

        return $stringReplaced;
    }
}

if (!function_exists('only_text_and_numbers')) {
    function only_text_and_numbers($string, $allowNull = false) {

        $stringReplaced = preg_replace(
            '/[\W_]/', '', $string
        );

        if($allowNull){
            return $stringReplaced ?: null;
        }

        return $stringReplaced;
    }
}

if (!function_exists('mask')) {
    function mask($value, $mask)
    {
        if(empty($value)){
            return false;
        }

        $k = 0;
        $maskared = '';

        for ($i = 0; $i < strlen($mask); $i++) {
            if (substr($mask, $i, 1) == '#') {
                $maskared .= substr($value, $k++, 1);
            } else {
                $maskared .= substr($mask, $i, 1);
            }
        }
        return $maskared;
    }
}

if (!function_exists('ucwords_br')) {
    /**
     * Uppercase the first character of each word in a string respecting the brazilian rules
     *
     * @param $value String
     * @return null|string
     */
    function ucwords_br($value)
    {
        if (empty($value))
            return null;

        $no_capitalize = array('e', 'das', 'dos', 'da', 'de', 'do');
        $capitalized = array();
        $romanRegex = "/^(m{0,4})(cm|cd|d?c{0,3})(xc|xl|l?x{0,3})(ix|iv|v?i{0,3})$/";

        foreach (explode(' ', mb_strtolower(trim($value))) as $value) {
            $capitalized[] =
                (!in_array($value, $no_capitalize))
                    ? (preg_match($romanRegex, $value) == true)
                    ? mb_strtoupper($value)
                    : mb_ucfirst($value)
                    : $value;
        }

        return join($capitalized, null);
    }
}

if (!function_exists('mb_ucfirst')) {
    /**
     * Uppercase the first character of string
     *
     * @param $string String
     * @return string
     */
    function mb_ucfirst($string)
    {
        $first = mb_strtoupper(mb_substr($string, 0, 1));
        return $first . mb_substr($string, 1);
    }
}

if (!function_exists('convert_date')) {
    function convert_date($value, $format = 'd/m/Y')
    {
        if(empty($value)){
            return null;
        }

        $date = new DateTime($value);
        return $date->format($format);
    }
}

if (!function_exists('convert_date_from_format')) {
    function convert_date_from_format($value, $fromFormat = 'd/m/Y', $toFormat = 'Y-m-d')
    {
        if(empty($value)){
            return;
        }

        $date = DateTime::createFromFormat($fromFormat, $value);
        return $date->format($toFormat);
    }
}

if (!function_exists('date_age')) {
    function date_age($dateOfBirth, $dateToDiff = null)
    {   $dateOfBirth = new DateTime($dateOfBirth);

        if( ! $dateToDiff){
            $dateToDiff = new DateTime();
        }

        $diff = $dateOfBirth->diff($dateToDiff);

        return $diff->y;
    }
}


if (!function_exists('convert_decimal_to_database_format')) {
    function convert_decimal_to_database_format($value, $locale = 'pt_BR'){
        if(empty($value)){
            return null;
        }

        $numberFormatter = new NumberFormatter( $locale, NumberFormatter::DECIMAL );
        return $numberFormatter->parse($value);
    }
}

if (!function_exists('convert_decimal_to_locale_format')) {
    function convert_decimal_to_locale_format(float $value, $groupingSeparator = false, $locale = 'pt_BR')
    {
        $numberFormatter = new NumberFormatter($locale, NumberFormatter::DECIMAL);
        $numberFormatter->setSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL, $groupingSeparator ? '.' : null);

        return $numberFormatter->format($value);
    }
}

if (!function_exists('split_date_range')) {
    function split_date_range($range){
        list($start, $end) = explode(' - ', $range);

        $start = DateTime::createFromFormat('d/m/Y H:i:s', $start . '00:00:00');
        $end = DateTime::createFromFormat('d/m/Y H:i:s', $end . '00:00:00');

        return [
            'start' => $start,
            'end' => $end
        ];
    }
}

if (!function_exists('join_date_range_string')) {
    function join_date_range_string(array $range, $format = 'd/m/Y'){
        return $range['start']->format($format) . ' - ' . $range['end']->format($format);
    }
}

if (!function_exists('password_generate')) {
    function password_generate($length = 8)
    {
        $data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($data), 0, $length);
    }
}

if (!function_exists('generate_token')) {
    function generate_token()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}

if (!function_exists('convert_diff_to_time')) {
    function convert_diff_to_time(\DateInterval $diff)
    {

        $diffTime = array();

        if ($diff->y > 0) {
            $diffTime[] = ($diff->y > 1) ? $diff->format('%y anos') : $diff->format('%y ano');
        }

        if ($diff->m > 0) {
            $diffTime[] = ($diff->m > 1) ? $diff->format('%m meses') : $diff->format('%m mês');
        }

        if ($diff->d > 0) {
            $diffTime[] = ($diff->d > 1) ? $diff->format('%a dias') : $diff->format('%a dia');
        }

        if ($diff->h > 0) {
            $diffTime[] = ($diff->h > 1) ? $diff->format('%h horas') : $diff->format('%h hora');
        }

        if ($diff->i > 0) {
            $diffTime[] = ($diff->i > 1) ? $diff->format('%i minutos') : $diff->format('%i minuto');
        }

        if ($diff->s > 0) {
            $diffTime[] = ($diff->s > 1) ? $diff->format('%s segundos') : $diff->format('%s segundo');
        }

        if(count($diffTime) === 1){
            return $diffTime[0];
        }

        $last = array_pop($diffTime);

        return join(", ", $diffTime) . ' e ' . $last;
    }
}

if (!function_exists('absolute_url')) {
    function absolute_url()
    {
        return sprintf('%s://%s', $_SERVER['REQUEST_SCHEME'], $_SERVER['HTTP_HOST']);
    }
}
