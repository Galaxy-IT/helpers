<?php


use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Request;

function prd($elem = '')
{
    pr($elem);

    die;
}

/**
 * print_r variable
 *
 * @param string $elem
 */
function pr($elem = '')
{
    echo '<hr><pre>';

    print_r($elem);

    echo '</pre><hr>';
}

function vdd($elem = '')
{
    vd($elem);

    die;
}

/**
 * print_r variable
 *
 * @param string $elem
 */
function vd($elem = '')
{
    echo '<hr><pre>';

    var_dump($elem);

    echo '</pre><hr>';
}

/**
 * Show last query to database
 */
if (!function_exists('get_last_query')) {
    /**
     * @return mixed
     */
    function get_last_query()
    {
        $queries = DB::getQueryLog();

        $sql = end($queries);

        if (!empty($sql['bindings'])) {
            $pdo = DB::getPdo();

            foreach ($sql['bindings'] as $binding) {
                $sql['query'] =
                    preg_replace(
                        '/\?/',
                        $pdo->quote($binding),
                        $sql['query'],
                        1
                    );
            }
        }

        return $sql['query'];
    }
}


if (!function_exists('add_get_parameters')) {
    /**
     * @param array $parameters
     * @param null  $url
     *
     * @return string
     */
    function add_get_parameters($parameters, $url = null)
    {
        $newParametersArray = [];
        $parameters = array_merge($_GET, $parameters);

        foreach ($parameters as $name => $parameter) {
            $newParametersArray[] = "$name=$parameter";
        }

        sort($newParametersArray);

        $url = $url ?: Request::url();

        return $url . '?' . implode('&', $newParametersArray);
    }
}

if (!function_exists('update_get_parameters')) {
    /**
     * @param array $parameters
     * @param null  $url
     *
     * @return string
     */
    function update_get_parameters($parameters, $url = null)
    {
        $newParametersArray = [];
        $_keys = [];
        $_parameters = $_GET;

        foreach ($_parameters as $_parameter => $_value) {
            if (isset($parameters[$_parameter])) {
                $_value = explode(',', $_value);
                $_value = array_merge($_value, (array) $parameters[$_parameter]);
                $_value = implode(',', $_value);
            }

            $newParametersArray[] = $_parameter . '=' . $_value;
            $_keys = $_parameter;
        }

        $parameters = array_except($parameters, $_keys);
        foreach ($parameters as $parameter => $value) {
            $newParametersArray[] = $parameter . '=' . implode(',', (array) $value);
        }

        sort($newParametersArray);

        $url = $url ?: Request::url();

        return $url . '?' . implode('&', $newParametersArray);
    }
}

if (!function_exists('remove_get_parameters')) {
    /**
     * @param array $parameters
     * @param null  $url
     *
     * @return string
     */
    function remove_get_parameters($parameters, $url = null)
    {
        $newParametersArray = [];
        $_parameters = $_GET;

        foreach ($_parameters as $_parameter => $_value) {
            $_value = explode(',', $_value);

            if (isset($parameters[$_parameter])) {
                $_value = array_filter(
                    $_value,
                    function ($v) use ($parameters, $_parameter) {
                        return $v != $parameters[$_parameter];
                    }
                );
            }

            if (!empty($_value)) {
                $newParametersArray[] = $_parameter . '=' . implode(',', $_value);
            }
        }

        sort($newParametersArray);

        $url = $url ?: Request::url();

        return count($newParametersArray) ? $url . '?' . implode('&', $newParametersArray) : $url;
    }
}

if (!function_exists('remove_get_parameter')) {
    /**
     * @param string $parameter
     * @param null   $url
     *
     * @return string
     */
    function remove_get_parameter($parameter, $url = null)
    {
        $newParametersArray = [];
        $_parameters = $_GET;

        foreach ($_parameters as $_parameter => $_value) {
            if ($_parameter != $parameter) {
                $newParametersArray[] = $_parameter . '=' . $_value;
            }
        }

        sort($newParametersArray);

        $url = $url ?: Request::url();

        return count($newParametersArray) ? $url . '?' . implode('&', $newParametersArray) : $url;
    }
}


if (!function_exists('is_front')) {
    /**
     * @return bool
     */
    function is_front()
    {
        if (php_sapi_name() == 'cli') {
            return false;
        }

        return !is_admin_panel();
    }
}

if (!function_exists('is_admin_panel')) {
    /**
     * @return bool
     */
    function is_admin_panel()
    {
        if (php_sapi_name() == 'cli') {
            return false;
        }

        return request()->segment(1) == 'admin';
    }
}

if (!function_exists('random_string')) {
    /**
     * Create a Random String
     *
     * Useful for generating passwords or hashes.
     *
     * @access    public
     *
     * @param    string  $type // of random string.  basic, alpha, all, numeric, no_zero, unique, md5, encrypt and sha1
     * @param    integer $length number of characters
     *
     * @return    string
     */
    function random_string($type = 'all', $length = 8)
    {
        switch ($type) {
            case 'basic':
                return mt_rand();
                break;

            case 'all':
            case 'numeric':
            case 'no_zero':
            case 'alpha':
            case 'alpha_num':
            case 'lover_alpha_num':
                switch ($type) {
                    case 'lover_alpha_num':
                        $pool = '0123456789abcdefghijklmnopqrstuvwxyz';
                        break;
                    case 'alpha':
                        $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;
                    case 'alpha_num':
                        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;
                    case 'numeric':
                        $pool = '0123456789';
                        break;
                    case 'no_zero':
                        $pool = '123456789';
                        break;
                    default:
                        $pool = '!@#$%^&*()_+/|\?.,><~`=-0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                }

                $str = '';
                for ($i = 0; $i < $length; $i++) {
                    $str .= substr($pool, mt_rand(0, strlen($pool) - 1), 1);
                }

                return $str;

                break;

            case 'unique':
            case 'md5':
                return md5(uniqid(mt_rand()));

                break;

            case 'encrypt':
            case 'sha1':
                return hash('sha1', uniqid(mt_rand(), true));

                break;
        }
    }
}


if (!function_exists('get_raw_sql')) {
    /**
     * @param Illuminate\Database\Query\Builder $query
     *
     * @return string
     */
    function get_raw_sql(\Illuminate\Database\Query\Builder $query)
    {
        $bindings = $query->getBindings();

        return preg_replace_callback('/\?/', function ($match) use (&$bindings, $query) {
            return $query->getConnection()->getPdo()->quote(array_shift($bindings));
        }, $query->toSql());
    }
}

function query_results_to_array($collection)
{
    return collect($collection)->map(function ($item) {
        return (array) $item;
    })->toArray();
}

function create_dir_if_not_exists(string $path)
{
    if (!file_exists($path))
        mkdir($path, 0777, true);
}

function swap(&$var1, &$var2)
{
    $tmp = $var1;
    $var1 = $var2;
    $var2 = $tmp;
}

function get_path_without_locale(string $path = null)
{
    $locales = config('translatable.locales');
    $path = $path ?? Request::path();
    foreach ($locales as $locale) {
        if (strpos($path, $locale) === 0 || strpos($path, '/' . $locale) === 0) {
            $count = 1;
            $path = str_replace($locale, '', $path, $count);
            break;
        }
    }
    return $path;
}

function get_locales()
{
    return array_keys(config('laravellocalization.supportedLocales'));
}

function get_locales_config()
{
    return config('laravellocalization.supportedLocales');
}

function validate_base64($base64data, array $allowedMime = ['png', 'jpg', 'jpeg', 'gif'])
{
    // strip out data uri scheme information (see RFC 2397)
    if (strpos($base64data, ';base64') !== false) {
        [, $base64data] = explode(';', $base64data);
        [, $base64data] = explode(',', $base64data);
    }

    // strict mode filters for non-base64 alphabet characters
    if (base64_decode($base64data, true) === false) {
        return false;
    }

    // decoding and then reeconding should not change the data
    if (base64_encode(base64_decode($base64data)) !== $base64data) {
        return false;
    }

    $binaryData = base64_decode($base64data);

    // temporarily store the decoded data on the filesystem to be able to pass it to the fileAdder
    $tmpFile = tempnam(sys_get_temp_dir(), 'medialibrary');
    file_put_contents($tmpFile, $binaryData);

    // guard Against Invalid MimeType
    $allowedMime = array_flatten($allowedMime);

    // no allowedMimeTypes, then any type would be ok
    if (empty($allowedMime)) {
        return true;
    }

    // Check the MimeTypes
    $validation = Illuminate\Support\Facades\Validator::make(
        ['file' => new Illuminate\Http\File($tmpFile)],
        ['file' => 'mimes:' . implode(',', $allowedMime)]
    );

    return !$validation->fails();
}


function is_url($url)
{
    if (!is_string($url) || empty($url)) {
        return false;
    }

    $validation = Illuminate\Support\Facades\Validator::make(
        ['url' => $url],
        ['url' => 'url']
    );

    return !$validation->fails();
}

function is_json($string)
{
    if (!is_string($string)) {
        return false;
    }

    json_decode($string);

    return (json_last_error() == JSON_ERROR_NONE);
}


function get_ai(string $table)
{
    $statement = DB::select("SHOW TABLE STATUS LIKE '$table'");

    return $statement[0]->Auto_increment;
}

function str_lreplace(string $search, string $replace, string $subject)
{
    $pos = strrpos($subject, $search);

    if ($pos !== false) {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }

    return $subject;
}

function trim_phone(string $phone)
{
    return preg_replace("/[^0-9]/", "", $phone);
}

function is_pjax()
{
    return (bool)request()->header('X-PJAX', false);
}

function is_joined(string $table, Builder $query)
{
    return is_array($query->joins) && count(array_filter($query->joins, function ($join) use ($table) {
        return $join->table == $table;
    })) > 0;
}

function is_selected($column, Builder $query)
{
    return in_array($column, $query->columns);
}

function pg_q(string $query, array $params = [])
{
    $conn = pg_connect('host=localhost port=5432 dbname=de_lis user=root password=root');
    $res = pg_query_params($conn, $query, $params);

    return pg_fetch_array($res);
}