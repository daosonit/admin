<?php

use Illuminate\Support\Debug\Dumper;
use App\Mytour\MytourExtends\MytourModelCollection;
use App\Mytour\MytourExtends\MytourCollection;

if (!function_exists('at_office'))
{
	/**
	 * @return boolean
	 */
	function at_office()
	{
		return collect(Config::get('mytour.office_ip'))->contains(Request::ip());
	}
}

if ( ! function_exists('dnd'))
{
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed
     * @return void
     */
    function dnd()
    {
        $back_track = debug_backtrace();
        $caller     = array_shift($back_track);

        echo "<span style='font-family: Menlo,Monaco,Consolas,monospace; display:block; text-align: center; background: #D6D61F; font-weight: 600; color: #111;'>DUMP IN (" . $caller['file'] . " -- line: <b style='color: red'>" . $caller['line'] . "</b>)</span>";
        array_map(function($x) { (new Dumper)->dump($x); }, func_get_args());
        echo "<span style='font-family: Menlo,Monaco,Consolas,monospace; display:block; text-align: center; background: #D6D61F; font-weight: 600; color: #111;'>END DUMP</span>";

    }
}

if ( ! function_exists('str_int_time'))
{
    /**
     * Dump the passed variables and end the script.
     *
     * @param  $time string
     * @return int
     */
    function str_int_time($time)
    {
        $time = str_replace('/', '-', (string) $time);
        return strtotime($time);
    }
}

if ( ! function_exists('strtime_firstday_month'))
{
    /**
     * Dump the passed variables and end the script.
     *
     * @param  $time int
     * @return int
     */
    function strtime_firstday_month($time)
    {
        return strtotime(date('Ym01', (int) $time));
    }
}


if ( ! function_exists('strtime_lastday_month'))
{
    /**
     * Dump the passed variables and end the script.
     *
     * @param  $time int
     * @return int
     */
    function strtime_lastday_month($time)
    {
        return strtotime(date('Ymt', (int) $time));
    }
}

if ( ! function_exists('strtime_next_month'))
{
    /**
     * Dump the passed variables and end the script.
     *
     * @param  $time int
     * @return int
     */
    function strtime_next_month($time)
    {
        return strtotime("+1 month" , $time);
    }
}

if ( ! function_exists('convert_str_time_range'))
{
    /**
     * Dump the passed variables and end the script.
     *
     * @param  $time string
     * @return array
     */
    function convert_str_time_range($time_range)
    {
        $date_range_checkin = explode('-', $time_range);
        $date_checkin  = "";
        $date_checkout = "";
        if (is_array($date_range_checkin)) {
            $date_checkin  = trim($date_range_checkin[0]);
            $date_checkin  = str_int_time($date_checkin);

            $date_checkout = trim($date_range_checkin[1]);
            $date_checkout = str_int_time($date_checkout);
        }

        $data_return = ['time_start' => $date_checkin, 'time_finish' => $date_checkout];

        return $data_return;
    }
}

if ( ! function_exists('showQueryExecute'))
{
    function showQueryExecute()
    {
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        $query     = '';
        if(!empty($last_query))
        {
            foreach ($last_query['bindings'] as $i => $binding) {
                if ($binding instanceof \DateTime) {
                    $last_query['bindings'][$i] = $binding->format('\'Y-m-d H:i:s\'');
                } else {
                    if (is_string($binding)) {
                        $last_query['bindings'][$i] = "'$binding'";
                    }
                }
            }

            // Insert bindings into query
            $query = str_replace(array('%', '?'), array('%%', '%s'), $last_query['query']);

            $query = vsprintf($query, $last_query['bindings']);
        }

        return $query;
    }
}

if ( ! function_exists('format_currency'))
{
    /**
     * Change data type string-> int.
     *
     * @param  $currency str
     * @return int
     */
    function format_currency($currency)
    {
        $result = str_replace(',','',$currency);

        return $result;
    }
}


if ( ! function_exists('cur_sub_domain'))
{
    /**
     * Change data type string-> int.
     *
     * @param  $currency str
     * @return int
     */
    function cur_sub_domain()
    {
        $hostName = parse_url(Request::url(), PHP_URL_HOST);
        preg_match('/(.*).mytour/', $hostName, $subDomain);
        return isset($subDomain[1]) ? $subDomain[1] : '';
    }
}


if ( ! function_exists('cur_tld'))
{
    /**
     * Change data type string-> int.
     *
     * @param  $currency str
     * @return int
     */
    function cur_tld()
    {
        $hostName = parse_url(Request::url(), PHP_URL_HOST);
        preg_match('/mytour.(.*)/', $hostName, $tld);
        return isset($tld[1]) ? $tld[1] : '';
    }
}

if ( ! function_exists('model_collect'))
{
    /**
     * Tạo một eloquent collection từ 1 mảng các models
     *
     * @param  array  models
     * @return App\Mytour\MytourExtends\MytourModelCollection
     */
    function model_collect(array $models)
    {
        return new MytourModelCollection($models);
    }
}



if ( ! function_exists('mytour_collect'))
{
    /**
     * Tạo một collection từ 1 mảng các items
     *
     * @param  array  items
     * @return App\Mytour\MytourExtends\MytourCollection
     */
    function mytour_collect(array $items)
    {
        return new MytourCollection($items);
    }
}


if ( ! function_exists('is_live_env'))
{
    /**
     * Định rõ nếu môi trường là trên server thật
     *
     * @return boolean
     */
    function is_live_env()
    {
        return in_array(App::environment(), [ENV_LIVE, 'production']);
    }
}

if ( ! function_exists('is_dev_env'))
{
    /**
     * Định rõ nếu môi trường là trên server dev
     *
     * @return boolean
     */
    function is_dev_env()
    {
        return (App::environment() === ENV_DEV);
    }
}


if ( ! function_exists('is_local_env'))
{
    /**
     * Định rõ nếu môi trường là trên server local
     *
     * @return boolean
     */
    function is_local_env()
    {
        return (App::environment() === ENV_LOCAL);
    }
}

if ( ! function_exists('convert_format_price'))
{
    /**
     * Convert giá từ string sang Double
     * 1,000,000 -> 1000000
     *
     * @param  str  items
     * @return App\Mytour\MytourExtends\MytourCollection
     */
    function convert_format_price($price)
    {
        return doubleval(str_replace(",", "", $price));
    }
}

if ( ! function_exists('generatePrice'))
{
    /**
     * Định dạng và làm tròn giá
     * @param int $price : giá truyền vào
     * @param string $round_type : kiểu làm tròn
     * @param string $is_format_number : định dạng tiền tệ (1: có | 0: không)
     * @return int
     */
    function generatePrice($price, $round_type = '', $is_format_number = 1){
        switch ($round_type) {
            case 'down':
                $price = floor($price / 1000) * 1000;
                break;

            case 'up':
                $price = ceil($price / 1000) * 1000;
                break;

            default:
                break;
        }
        if($is_format_number == 1){
            $price = number_format($price, 0, '.', ',');
        }
        return $price;
    }
}

if ( ! function_exists('getcontent'))
{
    function getcontent($url, $post = "", $refer = "", $usecookie = false, $iHot = 0)
    {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);

        if ($post) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        }

        if ($refer) {
            curl_setopt($curl, CURLOPT_REFERER, $refer);
        }

        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/6.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.7) Gecko/20050414 Firefox/1.0.3");
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        //curl_setopt($curl, CURLOPT_TIMEOUT_MS, 5000);

        if ($usecookie) {
            curl_setopt($curl, CURLOPT_COOKIEJAR, $usecookie);
            curl_setopt($curl, CURLOPT_COOKIEFILE, $usecookie);
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);

        $html = curl_exec($curl);
        curl_close($curl);

        return $html;
    }
}

if ( ! function_exists('pushValueToKey')) {
    /**
     * Gán key theo id xuất hiện ở value
     * @param array $arrData     : Mảng cần gán lại
     * @param string $name_value : key của value cần gán sang
     * @return array
     */
    function pushValueToKey($arrData, $name_value)
    {
        $array_return = array();
        if (empty($arrData)) return $array_return;
        
        foreach ($arrData as $key => $value) {
            $array_return[$value[$name_value]] = $value;
        }

        return $array_return;
    }
}

if (!function_exists('url_s3')) {
    /**
     * @return string
     */
    function url_s3()
    {
        return env('S3_DOMAIN');
    }
}


















