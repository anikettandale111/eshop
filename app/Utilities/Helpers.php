<?php

use App\Models\Category;
use App\Models\Currency;

class Helper
{

    public static function defaultLogo()
    {
        return asset('frontend/assets/images/logo.svg');
    }

    public static function userProfile()
    {
        return asset('backend/assets/images/avatar/img-1.jpg');
    }

    public static function defaultFavicon()
    {
        return asset('frontend/images/favicon.jpg');
    }

    public static function loader()
    {
        return asset('backend/assets/images/loader.gif');
    }

    public static function DefaultImage()
    {
        return asset('backend/assets/images/default-img.jpg');
    }

    public static function minPrice()
    {
        return floor(\App\Models\Product::min('purchase_price'));
    }

    public static function maxPrice()
    {
        return floor(\App\Models\Product::max('purchase_price'));
    }
    public static function units()
    {
        $x = ['kg', 'pc', 'gms', 'ltrs'];
        return $x;
    }


    // monthly income
    public static function getMonthlySum()
    {
        $year = \Carbon\Carbon::now()->year;
        $month = \Carbon\Carbon::now()->month;
        if ($month < 10) {
            $month = '0' . $month;
        }

        $search = $year . '-' . $month;
        $revenues = \App\Models\Order::where('created_at', 'like', $search . '%')->where('order_status', 'delivered')->get();

        $sum = 0;
        foreach ($revenues as $revenue) {
            $sum += $revenue->total_amount;
        }


        return $sum;
    }

    public static function isDeviceMobile(){
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
            return true;
        }
        return false;
    }
    public static function check_permission($mod_name)
    {

        if ((auth('admin')->user()->id === 1) || in_array($mod_name, json_decode(auth('admin')->user()->staff->role->permissions)) == true) {
            return true;
        }
        return false;
    }



    // get sku combination
    public static function combinations($arrays)
    {
        $result = [[]];
        foreach ($arrays as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property => $property_value]);
                }
            }
            $result = $tmp;
        }
        return $result;
    }

    // Inch to cm
    public static function size_converter($inch)
    {
        $cm = floatval($inch * 2.54);
        return $cm;
    }

    // currency load
    public static function currency_load()
    {
        if (session()->has('system_default_currency_info') == false) {
            session()->put('system_default_currency_info', Currency::find(1));
        }
    }

    public static function currency_converter($amount)
    {
        return format_price(convert_price($amount));
    }

    // price range
    public static function get_price_range($product, $formatted = true)
    {
        $lowest_price = $product->purchase_price;
        $highest_price = $product->purchase_price;
        if (count($product->stocks) > 0) {
            foreach ($product->stocks as $key => $stock) {
                if ($lowest_price > $stock->price) {
                    $lowest_price = $stock->price;
                }
                if ($highest_price < $stock->price) {
                    $highest_price = $stock->price;
                }
            }
        }

        if ($formatted) {
            if ($lowest_price == $highest_price) {
                return format_price(convert_price($lowest_price));
            } else {
                return format_price(convert_price($lowest_price)) . ' - ' . format_price(convert_price($highest_price));
            }
        } else {
            return $lowest_price . ' - ' . $highest_price;
        }
        //
        //        foreach(json_decode($product->variation) as $key=>$variation){
        //            if($lowest_price > $variation->price){
        //                $lowest_price=$variation->price;
        //            }
        //            if($highest_price < $variation->price){
        //                $highest_price=$variation->price;
        //            }
        //        }

        $lowest_price = convert_price($lowest_price - Helper::get_product_discount($product, $lowest_price));
        $highest_price = convert_price($highest_price - Helper::get_product_discount($product, $highest_price));

        if ($lowest_price == $highest_price) {
            return self::currency_converter($lowest_price);
        }
        return self::currency_converter($lowest_price) . '-' . self::currency_converter($highest_price);
    }

    public static function get_product_discount($product, $price)
    {
        $discount = 0;
        if ($product->discount_type == 'percent') {
            $discount = ($price * $product->discount) / 100;
        } elseif ($product->discount_type == 'amount') {
            $discount = $product->discount;
        }
        return floatval($discount);
    }
}

if (!function_exists('appMode')) {
    function appMode()
    {
        return \Illuminate\Support\Facades\Config::get('app.app_demo');
    }
}

if (!function_exists('demoCheck')) {
    function demoCheck()
    {
        if (appMode()) {
            \Brian2694\Toastr\Facades\Toastr::warning('For the demo version, you cannot change this', 'Failed');
            return true;
        } else {
            return false;
        }
    }
}


//currency symbol
if (!function_exists('currency_symbol')) {
    function currency_symbol()
    {
        Helper::currency_load();
        if (session()->has('currency_symbol')) {
            $symbol = session('currency_symbol');
        } else {
            $system_default_currency_info = session('system_default_currency_info');
            $symbol = $system_default_currency_info->symbol;
        }

        return $symbol;
    }
}

//Shows Price on page based on low to high with discount
if (!function_exists('home_discounted_price')) {
    function home_discounted_price($product, $formatted = true)
    {
        $price = $product->unit_price;
        $highest_price = $product->purchase_price;

        if (count($product->stocks) > 0) {
            foreach ($product->stocks as $key => $stock) {
                if ($highest_price < $stock->price) {
                    $highest_price = $stock->price;
                }
            }
        }

        if ($formatted) {
            if ($price == $highest_price) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }
}

//formats currency
if (!function_exists('format_price')) {
    function format_price($price)
    {
        return currency_symbol() . number_format($price, 2);
    }
}
//converts currency to home default currency
if (!function_exists('convert_price')) {
    function convert_price($price)
    {
        Helper::currency_load();
        $system_default_currency_info = session('system_default_currency_info');

        $price = floatval($price) / floatval($system_default_currency_info->exchange_rate);

        if (\Illuminate\Support\Facades\Session::has('currency_exchange_rate')) {
            $exchange = session('currency_exchange_rate');
        } else {
            $exchange = $system_default_currency_info->exchange_rate;
        }

        $price = floatval($price) * floatval($exchange);

        return $price;
    }
}

if (!function_exists('get_settings')) {
    function get_settings($key)
    {
        return \App\Models\Setting::value($key);
    }
}

class PaginationLinks
{

    /* Returns a set of pagination links. The parameters are:
     *
     * $page          - the current page number
     * $numberOfPages - the total number of pages
     * $context       - the amount of context to show around page links - this
     *                  optional parameter defauls to 1
     * $linkFormat    - the format to be used for links to other pages - this
     *                  parameter is passed to sprintf, with the page number as a
     *                  second and third parameter. This optional parameter
     *                  defaults to creating an HTML link with the page number as
     *                  a GET parameter.
     * $pageFormat    - the format to be used for the current page - this
     *                  parameter is passed to sprintf, with the page number as a
     *                  second and third parameter. This optional parameter
     *                  defaults to creating an HTML span containing the page
     *                  number.
     * $ellipsis      - the text to be used where pages are omitted - this
     *                  optional parameter defaults to an ellipsis ('...')
     */
    public static function create(
        $page,
        $numberOfPages,
        $context    = 1,
        $linkFormat = '<a href="javascript:void(0)" data-page="%d" >%d</a>',
        $pageFormat = '<a href="javascript:void(0)" class="active-page">%d</a>',
        $ellipsis   = '&hellip;'
    ) {

        // create the list of ranges
        $ranges = array(array(1, 1 + $context));
        self::mergeRanges($ranges, $page   - $context, $page + $context);
        self::mergeRanges($ranges, $numberOfPages - $context, $numberOfPages);

        // initialise the list of links
        $links = array();

        // loop over the ranges
        foreach ($ranges as $range) {

            // if there are preceeding links, append the ellipsis
            if (count($links) > 0) $links[] = $ellipsis;

            // merge in the new links
            $links =
                array_merge(
                    $links,
                    self::createLinks($range, $page, $linkFormat, $pageFormat)
                );
        }

        // return the links
        return implode(' ', $links);
    }

    /* Merges a new range into a list of ranges, combining neighbouring ranges.
     * The parameters are:
     *
     * $ranges - the list of ranges
     * $start  - the start of the new range
     * $end    - the end of the new range
     */
    private static function mergeRanges(&$ranges, $start, $end)
    {

        // determine the end of the previous range
        $endOfPreviousRange = &$ranges[count($ranges) - 1][1];

        // extend the previous range or add a new range as necessary
        if ($start <= $endOfPreviousRange + 1) {
            $endOfPreviousRange = $end;
        } else {
            $ranges[] = array($start, $end);
        }
    }

    /* Create the links for a range. The parameters are:
     *
     * $range      - the range
     * $page       - the current page
     * $linkFormat - the format for links
     * $pageFormat - the format for the current page
     */
    private static function createLinks($range, $page, $linkFormat, $pageFormat)
    {

        // initialise the list of links
        $links = array();

        // loop over the pages, adding their links to the list of links
        for ($index = $range[0]; $index <= $range[1]; $index++) {
            $links[] =
                sprintf(
                    ($index == $page ? $pageFormat : $linkFormat),
                    $index,
                    $index
                );
        }

        // return the array of links
        return $links;
    }
}
