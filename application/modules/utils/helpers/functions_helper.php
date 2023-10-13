<?php


//Path without slashe
define("_PATH", substr(FCPATH, -1));
define("REGEX_PATTERN_UNICODE_LOGIN", "~^(?:[\p{L}\p{Mn}\p{Pd}0-9\._]+)+$~u");
define("REGEX_PATTERN_UNICODE_NAME", "~^(?:[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+)+$~u");
define("REGEX_PATTERN_UNICODE_NAME_COMPLET", "~^(?:[\p{L}\p{Mn}\p{Pd}\\x{2019}]+\s[\p{L}\p{Mn}\p{Pd}\\x{2019}]+)+$~u");
define("REGEX_PATTERN_UNICODE_HASHTAG", "~^(?:[\p{L}\p{Mn}\p{Pd}\\x{2019}0-9\.\-_]+)+$~u");
define("_HASHTAG", "/^([\p{L}\p{Mn}\-\_]+)$/u");
define("REGEX_HASHTAG", "/(?<!\S)#([0-9a-zA-Z\-\_]+)/");
//Regex for french
define("REGEX_FR", "ÀÂÆÇÉÈÊËÎÏÔŒÙÛÜŸÿüûùœôïîëêèéçæâà");


class MyFile
{

    static function saveFile($filename, $data)
    {

        if (file_exists($filename))
            @unlink($filename);

        $myfile = fopen($filename, "w") or die("Unable to open file!");
        fwrite($myfile, $data);
        fclose($myfile);

    }

}

class MyCurl
{

    static function run($url, $data = array())
    {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('User-Agent: My-Great-Marketplace-App'));
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING , "gzip");
        curl_setopt($curl, CURLOPT_ENCODING, '');

        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        $result = curl_exec($curl);

        if(!curl_errno($curl)){
            print_r(curl_error($curl));
            return;
        }

        curl_close($curl);

        return $result;
    }

    static function get($url)
    {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('User-Agent: My-Great-Marketplace-App'));
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);


        return curl_exec($curl);
    }


}

function installFolderFound()
{
    if (ENVIRONMENT == "development")
        return FALSE;
    $path = Path::getPath(array("install2", "index.php"));
    if (file_exists($path)) {
        return TRUE;
    }

    return FALSE;
}

function array_key_whitelist(array $input, array $allowedKeys = [])
{
    $return = [];
    foreach ($allowedKeys as $key) {
        if (array_key_exists($key, $input)) {
            $return[$key] = $input[$key];
        } else {
            $return[$key] = "";
        }
    }
    return $return;
}

function getLast12Months()
{

    $months = array();

    for ($i = 0; $i <= 11; $i++) {

        if ($i > 0)
            $start = date("Y-m-1", strtotime("-$i months"));
        else
            $start = date("Y-m-1", time());

        $months[] = $start;
    }

    return $months;
}

if (!function_exists('admin_url')) {

    function admin_url($str = "")
    {

        return site_url(__ADMIN . "/" . $str);
    }

}

if (!function_exists('custom_protocol_url')) {

    function custom_protocol_url($str = "", $protocol = "")
    {
        $link = site_url($str);
        $link = str_replace('www.', '', $link);
        $link = str_replace('http://', $protocol . '://', $link);
        $link = str_replace('https://', $protocol . '://', $link);
        return $link;
    }

}


class Path
{

    public static function getImagesBaseUrl()
    {
        return IMAGES_BASE_URL;
    }


    public static function getPath($t = array())
    {

        if (!empty($t)) {
            $newpath = FCPATH;
            foreach ($t as $v) {
                if (preg_match("#\.#", $v)) {
                    $newpath .= $v;
                    return $newpath;
                } else {

                    $newpath .= $v . _PATH;
                }

            }

            return $newpath;
        }

        return FCPATH;
    }

    public static function addPath($oldpath, $t = array())
    {


        if (!empty($t)) {

            $lastc = substr($oldpath, -1);

            if ($lastc != _PATH) {
                $oldpath .= _PATH;
            }

            $newpath = $oldpath;
            foreach ($t as $v) {

                if (preg_match("#\.#", $v)) {
                    $newpath .= $v;
                    return $newpath;
                } else {

                    $newpath .= $v . _PATH;
                }

            }

            return $newpath;
        }

        return $oldpath;


    }
}

class Checker
{

    public static function load()
    {

        if (defined("HASLINKER")) {

            if (file_exists("config/" . HASLINKER . ".json")) {

                $params = loadData("config/" . HASLINKER . ".json");
                $params = json_decode($params, JSON_OBJECT_AS_ARRAY);

                if (!empty($params)) {
                    foreach ($params as $key => $value) {

                        if (is_array($value)) {
                            if (!defined($key))
                                define($key, json_encode($value, JSON_FORCE_OBJECT));
                        } else {

                            if (!defined($key)) {
                                define($key, $value);
                            }

                        }

                    }
                }
            }

        }

    }


    public static function user_agent_exist($useragent, $platform)
    {

        $platform = strtolower($platform);
        if (preg_match("#" . $platform . "#i", $useragent)) {
            return TRUE;
        }

        return FALSE;
    }

    public static function isValid($headers)
    {


        $CI =& get_instance();
        $CI->load->library('user_agent');


        //check validate
        if (!empty($headers)) {


            if (isset($headers['User-Agent']))
                $user_agent = Security::decrypt($headers['User-Agent']);
            else
                $user_agent = Security::decrypt($CI->input->get_request_header('User-Agent', TRUE));


            if (self::user_agent_exist($user_agent, "ios")) {

                if (isset($headers['Api-key-ios']))
                    $key = Security::decrypt($headers['Api-key-ios']);
                else
                    $key = Security::decrypt($CI->input->get_request_header('Api-key-ios', TRUE));

                if (isset($headers['Api-app-id']))
                    $app_id = Security::decrypt($headers['Api-app-id']);
                else
                    $app_id = Security::decrypt($CI->input->get_request_header('Api-app-id', TRUE));

                if (ConfigManager::defined("API_" . md5($app_id))
                    && ConfigManager::getValue("API_" . md5($app_id)) == $key) {
                    return TRUE;
                }


            } else if (self::user_agent_exist($user_agent, "android")) {


                if (isset($headers['Api-key-android']))
                    $key = Security::decrypt($headers['Api-key-android']);
                else
                    $key = Security::decrypt($CI->input->get_request_header('Api-key-android', TRUE));

                if (isset($headers['Api-app-id']))
                    $app_id = Security::decrypt($headers['Api-app-id']);
                else
                    $app_id = Security::decrypt($CI->input->get_request_header('Api-app-id', TRUE));


                if (ConfigManager::defined("API_" . md5($app_id))
                    && ConfigManager::getValue("API_" . md5($app_id)) == $key) {
                    return TRUE;
                }

            }
            //check if is a device or dev mode
        }


        return FALSE;
    }

}


//All mathods for text 
class Text
{

    public static function strToNumber($value)
    {
        $value = $value + 0;
        return $value;
    }

    public static function outputList($data = array())
    {

        foreach ($data as $key => $value) {
            foreach ($value as $k => $v) {
                if (is_string($v)) {
                    $data[$key][$k] = Text::output($v);
                }
            }
        }

        return $data;
    }


    public static function groupTranslate($data = array())
    {

        foreach ($data as $key => $value) {
            foreach ($value as $k => $v) {
                if (is_string($v) and !is_numeric($v)) {
                    $data[$key][$k] = Translate::sprint($v);
                }
            }
        }

        return $data;
    }

    public static function tokenIsValid($str = "")
    {

        if (preg_match("#[a-zA-Z0-9]+#", $str))
            return TRUE;

        return FALSE;
    }


    public static function compareToStrings($str1 = "", $str2 = "")
    {

        if ($str1 == $str2) {
            return TRUE;
        }

        return FALSE;
    }


    public static function isRealDate($str = "")
    {

        if (Text::validateDate($str, "Y-m-d"))
            return TRUE;

        return FALSE;
    }

    public static function hashIsValid($str = "")
    {
        if (preg_match("#^([a-z0-9]+)$#i", $str)) {
            return TRUE;
        }

        return FALSE;
    }

    public static function convert_hashtag($string = "")
    {
        $text = preg_replace(REGEX_HASHTAG, '<a class="post-hashtag"  href="' . site_url("hashtag") . '/$1"><b>#$1</b></a>', $string);
        return $text;
    }

    //
    public static function parse_to_html_link($string)
    {
        if ($string != "") {

            $unwanted_array = array('Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
                'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U',
                'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
                'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
                'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y');
            $str = strtr($string, $unwanted_array);

            return trim(preg_replace('<\W+>', "-", $str), "_") . ".html";
        } else
            return "";
    }

    public static function parse_to_clean_text($string)
    {
        if ($string != "") {

            $unwanted_array = array('Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
                'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U',
                'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
                'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
                'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y');
            $str = strtr($string, $unwanted_array);

            return trim(preg_replace('<\W+>', "-", $str), "_") . "";
        } else
            return "";
    }

    public static function checkSenderIdValidate($str = "")
    {

        if (preg_match("#([a-zA-Z0-9]+\@[0-9]+)#", $str)) {
            return TRUE;
        }

        return FALSE;
    }

    public static function checkEmailFields($str = "")
    {

        if (filter_var($str, FILTER_VALIDATE_EMAIL)) {
            return TRUE;
        }

        return FALSE;
    }

    public static function checkPhoneFields($str = "")
    {

        if (preg_match("#^[0-9 \-_.\(\)\+]+$#i", $str)) {
            return TRUE;
        }

        return FALSE;
    }

    public static function checkNameFields($str = "")
    {


        if (Text::detectIsArabic($str) == FALSE) {
            if (preg_match(REGEX_PATTERN_UNICODE_NAME, $str) and strlen($str) > 1) {
                return TRUE;
            }
        }


        return FALSE;
    }

    public static function checkNameCompleteFields($str = "")
    {


        if (Text::detectIsArabic($str) == FALSE) {
            if (preg_match(REGEX_PATTERN_UNICODE_NAME_COMPLET, $str) and strlen($str) > 3) {
                return TRUE;
            }
        }


        return FALSE;
    }

    public static function checkUsernameValidate($str = "")
    {

        if ($str == null)
            $str = "";

        if (Text::detectIsArabic($str))
            return FALSE;

        if (preg_match("#^([0-9]+)$#", $str)) {
            return FALSE;
        }

        if (strlen($str) <= 3) {
            return FALSE;
        }

        if (!preg_match(REGEX_PATTERN_UNICODE_LOGIN, $str)) {
           return FALSE;
        }


        return TRUE;
    }

    public static function detectIsArabic($str = "")
    {
        if (is_arabic($str)) {
            return TRUE;
        } else {

        }

        return FALSE;
    }


    public static function _print($str = "")
    {
        return nl2br(trim(htmlspecialchars($str)));
    }

    public static function detectArabic($str = "")
    {
        if (is_arabic($str)) {
            return ' dir="rtl" ';
        }
        return "";
    }

    public static function displayHashTag($str = "")
    {

        return Text::convert_hashtag($str);
    }

    public static function textToHtmlUrl($str = "")
    {

        if ($str != "") {
            return $str . ".htm";
        }

        return $str;
    }


    public static function textParser($data = array(), $file = '')
    {


        $url = base_url("views/mailing/templates/" . $file . ".html");
        $content = "";
        try {


            $content = url_get_content($url);
            if (!empty($data) and $content != '') {

                foreach ($data as $key => $value) {


                    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $content = preg_replace("#\{" . $key . "\}#", "<a href='$value'>" . $value . "</a>", $content);
                    } else {
                        $content = preg_replace("#\{" . $key . "\}#", $value, $content);
                    }
                }
            }

        } catch (Exception $ex) {

        }
        return $content;
    }

    public static function textParserHTML($data = array(), $html = '')
    {


        $content = "";
        try {
            $content = $html;
            if (!empty($data) and $content != '') {

                foreach ($data as $key => $value) {


                    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $content = preg_replace("#\{" . $key . "\}#", "<a href='$value'>" . $value . "</a>", $content);
                    } else {
                        $content = preg_replace("#\{" . $key . "\}#", $value, $content);
                    }
                }
            }

        } catch (Exception $ex) {

        }
        return $content;
    }

    public static function input($str = "", $acceptHTML = FALSE)
    {

        if ($acceptHTML == TRUE)
            $str = trim(htmlentities($str, ENT_QUOTES, ENCODING));
        else
            $str = trim(htmlentities(strip_tags($str), ENT_QUOTES, ENCODING));

        return $str;
    }

    public static function inputText($str = "")
    {
        //remove links
        $str = preg_replace('/<a href=\"(.*?)\">(.*?)<\/a>/', "\\2", $str);
        $str = trim(htmlentities($str, ENT_QUOTES, ENCODING));
        return $str;

    }


    public static function inputWithoutStripTags($str = "")
    {

        $str = strip_tags($str, " <a> <b> <big> <br>  <em> <font> <h1> <h2> <h3> <h4> <h5> <h6> <i> <p> <small> <strike> <strong> <sub> <sup> <u>");
        $str = trim(htmlentities(($str), ENT_QUOTES, ENCODING));
        return $str;
    }

    public static function output($str = "")
    {

        $str = ($str == NULL) ? "" : html_entity_decode($str, ENT_QUOTES, ENCODING);
        $string = preg_replace("/<div\s(.+?)>(.+?)<\/div>/is", "<p>$2</p>", $str);
        $string = preg_replace("/<div>(.+?)<\/div>/is", "<p>$1</p>", $string);

        return $string;
    }

    public static function echo_output($str = "")
    {
        $str = ($str == NULL) ? "" : html_entity_decode($str, ENT_QUOTES, ENCODING);
        $str = htmlspecialchars($str);
        return $str;
    }


    public static function setToArray($str = "", $attrs = array())
    {

        $finaldate = array();

        if ($str != "") {

            $data = explode(" ", $str);


            $i = 0;
            foreach ($data as $value) {

                foreach ($attrs as $value2) {

                    if (trim($value) != "") {
                        $finaldate[$i][trim($value2)] = trim($value);
                    }
                }

                $i++;
            }
        }


        return $finaldate;
    }


    public static function preparedLikeStatement($data = array())
    {

        $str = "";
        $strfinal = "";

        if (!empty($data)) {

            foreach ($data as $value) {

                $i = 0;
                $str = "";
                foreach ($value as $key => $value2) {

                    if ($str != "")
                        $str = $str . " OR ";

                    $str = $str . "   $key like '%" . Text::input($value2) . "%' ";

                }

                if ($strfinal != "")
                    $strfinal = $strfinal . " AND ";

                $strfinal = " " . $strfinal . " (" . $str . ")";
            }

        }


        return $strfinal;
    }


    public static function encrypt($str = "")
    {
        return $str;
    }


    public static function validateDate($str = "")
    {

        try {
            date('Y-m-d', strtotime($str));
            return TRUE;
        } catch (Exception $e) {

        }
        return FALSE;

    }


    public static function calcul2dates($date, $current)
    {
        $start = strtotime($date);
        $end = strtotime($current);
        $days_between = ceil(abs($end - $start) / 86400);
        return $days_between;
    }


    public static function substrwords($text, $maxchar, $end = '...')
    {
        if (strlen($text) > $maxchar || $text == '') {
            $words = preg_split('/\s/', $text);
            $output = '';
            $i = 0;
            while (1) {
                $length = strlen($output) + strlen($words[$i]);
                if ($length > $maxchar) {
                    break;
                } else {
                    $output .= " " . $words[$i];
                    ++$i;
                }
            }
            $output .= $end;
        } else {
            $output = $text;
        }
        return $output;
    }


    public static function removeSymbols($str)
    {


        $replace = array(
            'ъ' => '-', 'Ь' => '-', 'Ъ' => '-', 'ь' => '-',
            'Ă' => 'A', 'Ą' => 'A', 'À' => 'A', 'Ã' => 'A', 'Á' => 'A', 'Æ' => 'A', 'Â' => 'A', 'Å' => 'A', 'Ä' => 'Ae',
            'Þ' => 'B',
            'Ć' => 'C', 'ץ' => 'C', 'Ç' => 'C',
            'È' => 'E', 'Ę' => 'E', 'É' => 'E', 'Ë' => 'E', 'Ê' => 'E',
            'Ğ' => 'G',
            'İ' => 'I', 'Ï' => 'I', 'Î' => 'I', 'Í' => 'I', 'Ì' => 'I',
            'Ł' => 'L',
            'Ñ' => 'N', 'Ń' => 'N',
            'Ø' => 'O', 'Ó' => 'O', 'Ò' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'Oe',
            'Ş' => 'S', 'Ś' => 'S', 'Ș' => 'S', 'Š' => 'S',
            'Ț' => 'T',
            'Ù' => 'U', 'Û' => 'U', 'Ú' => 'U', 'Ü' => 'Ue',
            'Ý' => 'Y',
            'Ź' => 'Z', 'Ž' => 'Z', 'Ż' => 'Z',
            'â' => 'a', 'ǎ' => 'a', 'ą' => 'a', 'á' => 'a', 'ă' => 'a', 'ã' => 'a', 'Ǎ' => 'a', 'а' => 'a', 'А' => 'a', 'å' => 'a', 'à' => 'a', 'א' => 'a', 'Ǻ' => 'a', 'Ā' => 'a', 'ǻ' => 'a', 'ā' => 'a', 'ä' => 'ae', 'æ' => 'ae', 'Ǽ' => 'ae', 'ǽ' => 'ae',
            'б' => 'b', 'ב' => 'b', 'Б' => 'b', 'þ' => 'b',
            'ĉ' => 'c', 'Ĉ' => 'c', 'Ċ' => 'c', 'ć' => 'c', 'ç' => 'c', 'ц' => 'c', 'צ' => 'c', 'ċ' => 'c', 'Ц' => 'c', 'Č' => 'c', 'č' => 'c', 'Ч' => 'ch', 'ч' => 'ch',
            'ד' => 'd', 'ď' => 'd', 'Đ' => 'd', 'Ď' => 'd', 'đ' => 'd', 'д' => 'd', 'Д' => 'D', 'ð' => 'd',
            'є' => 'e', 'ע' => 'e', 'е' => 'e', 'Е' => 'e', 'Ə' => 'e', 'ę' => 'e', 'ĕ' => 'e', 'ē' => 'e', 'Ē' => 'e', 'Ė' => 'e', 'ė' => 'e', 'ě' => 'e', 'Ě' => 'e', 'Є' => 'e', 'Ĕ' => 'e', 'ê' => 'e', 'ə' => 'e', 'è' => 'e', 'ë' => 'e', 'é' => 'e',
            'ф' => 'f', 'ƒ' => 'f', 'Ф' => 'f',
            'ġ' => 'g', 'Ģ' => 'g', 'Ġ' => 'g', 'Ĝ' => 'g', 'Г' => 'g', 'г' => 'g', 'ĝ' => 'g', 'ğ' => 'g', 'ג' => 'g', 'Ґ' => 'g', 'ґ' => 'g', 'ģ' => 'g',
            'ח' => 'h', 'ħ' => 'h', 'Х' => 'h', 'Ħ' => 'h', 'Ĥ' => 'h', 'ĥ' => 'h', 'х' => 'h', 'ה' => 'h',
            'î' => 'i', 'ï' => 'i', 'í' => 'i', 'ì' => 'i', 'į' => 'i', 'ĭ' => 'i', 'ı' => 'i', 'Ĭ' => 'i', 'И' => 'i', 'ĩ' => 'i', 'ǐ' => 'i', 'Ĩ' => 'i', 'Ǐ' => 'i', 'и' => 'i', 'Į' => 'i', 'י' => 'i', 'Ї' => 'i', 'Ī' => 'i', 'І' => 'i', 'ї' => 'i', 'і' => 'i', 'ī' => 'i', 'ĳ' => 'ij', 'Ĳ' => 'ij',
            'й' => 'j', 'Й' => 'j', 'Ĵ' => 'j', 'ĵ' => 'j', 'я' => 'ja', 'Я' => 'ja', 'Э' => 'je', 'э' => 'je', 'ё' => 'jo', 'Ё' => 'jo', 'ю' => 'ju', 'Ю' => 'ju',
            'ĸ' => 'k', 'כ' => 'k', 'Ķ' => 'k', 'К' => 'k', 'к' => 'k', 'ķ' => 'k', 'ך' => 'k',
            'Ŀ' => 'l', 'ŀ' => 'l', 'Л' => 'l', 'ł' => 'l', 'ļ' => 'l', 'ĺ' => 'l', 'Ĺ' => 'l', 'Ļ' => 'l', 'л' => 'l', 'Ľ' => 'l', 'ľ' => 'l', 'ל' => 'l',
            'מ' => 'm', 'М' => 'm', 'ם' => 'm', 'м' => 'm',
            'ñ' => 'n', 'н' => 'n', 'Ņ' => 'n', 'ן' => 'n', 'ŋ' => 'n', 'נ' => 'n', 'Н' => 'n', 'ń' => 'n', 'Ŋ' => 'n', 'ņ' => 'n', 'ŉ' => 'n', 'Ň' => 'n', 'ň' => 'n',
            'о' => 'o', 'О' => 'o', 'ő' => 'o', 'õ' => 'o', 'ô' => 'o', 'Ő' => 'o', 'ŏ' => 'o', 'Ŏ' => 'o', 'Ō' => 'o', 'ō' => 'o', 'ø' => 'o', 'ǿ' => 'o', 'ǒ' => 'o', 'ò' => 'o', 'Ǿ' => 'o', 'Ǒ' => 'o', 'ơ' => 'o', 'ó' => 'o', 'Ơ' => 'o', 'œ' => 'oe', 'Œ' => 'oe', 'ö' => 'oe',
            'פ' => 'p', 'ף' => 'p', 'п' => 'p', 'П' => 'p',
            'ק' => 'q',
            'ŕ' => 'r', 'ř' => 'r', 'Ř' => 'r', 'ŗ' => 'r', 'Ŗ' => 'r', 'ר' => 'r', 'Ŕ' => 'r', 'Р' => 'r', 'р' => 'r',
            'ș' => 's', 'с' => 's', 'Ŝ' => 's', 'š' => 's', 'ś' => 's', 'ס' => 's', 'ş' => 's', 'С' => 's', 'ŝ' => 's', 'Щ' => 'sch', 'щ' => 'sch', 'ш' => 'sh', 'Ш' => 'sh', 'ß' => 'ss',
            'т' => 't', 'ט' => 't', 'ŧ' => 't', 'ת' => 't', 'ť' => 't', 'ţ' => 't', 'Ţ' => 't', 'Т' => 't', 'ț' => 't', 'Ŧ' => 't', 'Ť' => 't', '™' => 'tm',
            'ū' => 'u', 'у' => 'u', 'Ũ' => 'u', 'ũ' => 'u', 'Ư' => 'u', 'ư' => 'u', 'Ū' => 'u', 'Ǔ' => 'u', 'ų' => 'u', 'Ų' => 'u', 'ŭ' => 'u', 'Ŭ' => 'u', 'Ů' => 'u', 'ů' => 'u', 'ű' => 'u', 'Ű' => 'u', 'Ǖ' => 'u', 'ǔ' => 'u', 'Ǜ' => 'u', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'У' => 'u', 'ǚ' => 'u', 'ǜ' => 'u', 'Ǚ' => 'u', 'Ǘ' => 'u', 'ǖ' => 'u', 'ǘ' => 'u', 'ü' => 'ue',
            'в' => 'v', 'ו' => 'v', 'В' => 'v',
            'ש' => 'w', 'ŵ' => 'w', 'Ŵ' => 'w',
            'ы' => 'y', 'ŷ' => 'y', 'ý' => 'y', 'ÿ' => 'y', 'Ÿ' => 'y', 'Ŷ' => 'y',
            'Ы' => 'y', 'ž' => 'z', 'З' => 'z', 'з' => 'z', 'ź' => 'z', 'ז' => 'z', 'ż' => 'z', 'ſ' => 'z', 'Ж' => 'zh', 'ж' => 'zh'
        );

        $str = Text::output($str);
        $str = strtr($str, $replace);
        $str = iconv("UTF-8", "ASCII//TRANSLIT", $str); // "%#dsdeaa.,d s#$4.sedf;21df"

        return $str;
    }



}


class Json
{


    public static function isJson($string)
    {
        $string = json_decode($string, JSON_OBJECT_AS_ARRAY);
        return (json_last_error() == JSON_ERROR_NONE);
    }


    public static function encode($str = "")
    {
        return json_encode($str, JSON_FORCE_OBJECT);
    }


    public static function decode($str = "")
    {
        return json_decode($str, JSON_FORCE_OBJECT);
    }

    public static function convertToJson($data = array(), $tag = "default", $crypt = FALSE, $args = array())
    {

        $data = Json::prepareDataForJson($data, $crypt);
        $args = Json::prepareDataForJson($args, $crypt);


        return Json::encode(array("success" => 1, $tag => $data, "args" => $args));
    }

    public static function prepareDataForJson($data = array(), $crypt = FALSE)
    {

        $newdata = array();

        if (is_array($data) and !empty($data)) {

            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $key2 => $value2) {
                        if ($crypt == FALSE) {
                            $newdata[$key][$key2] = Json::outputForJson($value2);
                        } else {
                            $newdata[$key][$key2] = Json::outputForJson(Security::encrypt($value2));
                        }
                    }
                } else {
                    if ($crypt == FALSE) {
                        $newdata[$key] = Json::outputForJson($value);
                    } else {
                        $newdata[$key] = Json::outputForJson(Security::encrypt($value));
                    }
                }
            }

        }

        return $newdata;
    }

    public static function setHeaderToJson()
    {

    }

    public static function outputForJson($str = "")
    {

        return $str;
    }

}


class Pagination
{


    public $current_page;
    public $per_page;
    public $count;
    public $first_nbr;
    public $nbrpages;
    public $nextpage;


    public function __construct()
    {

    }


    function getNbrpages()
    {
        return $this->nbrpages;
    }

    function setNbrpages($nbrpages)
    {
        $this->nbrpages = $nbrpages;
    }


    function getCurrent_page()
    {
        return $this->current_page;
    }

    function getPer_page()
    {
        return $this->per_page;
    }

    function getCount()
    {
        return $this->count;
    }

    function setCurrent_page($current_page)
    {

        if ($current_page <= 0) {
            $current_page = 1;
        }

        $this->current_page = $current_page;
    }

    function setPer_page($per_page)
    {
        $this->per_page = $per_page;
    }

    function setCount($count)
    {
        $this->count = $count;
    }


    function getFirst_nbr()
    {
        return $this->first_nbr;
    }

    function setFirst_nbr($first_nbr)
    {
        $this->first_nbr = $first_nbr;
    }


    public function calcul()
    {


        //Nous allons maintenant compter le nombre de pages.
        if ($this->count == 0) {
            $this->nbrpages = 1;
        } else {
            if ($this->per_page > 0) {
                $this->nbrpages = ceil($this->count / $this->per_page);
            } else {
                $this->nbrpages = 1;
            }

        }


        if ($this->nbrpages == 0) {
            $this->nbrpages = 1;
        }


        if (isset($this->current_page)) // Si la variable $_GET['page'] existe...
        {
            $this->current_page = intval($this->current_page);


            if ($this->current_page > $this->nbrpages) // Si la valeur de $pageActuelle (le numéro de la page) est plus grande que $nombreDePages...
            {
                $this->current_page = $this->nbrpages;

            }
        } else // Sinon
        {
            $this->current_page = 1; // La page actuelle est la n°1
        }


        $this->first_nbr = ($this->current_page - 1) * $this->per_page;
        if ($this->first_nbr < 0) {
            $this->first_nbr = 0;
        }


        $this->nextpage = $this->nbrpages - $this->current_page;

        if ($this->nbrpages > $this->current_page) {
            $this->nextpage = $this->current_page + 1;
        } else if ($this->nbrpages == $this->current_page) {
            $this->nextpage = -1;
        }

    }


    public function links($data = array(), $page = '', $class = "pagination", $prevClass = "", $nextClass = "", $activeClass = "active")
    {


        if ($page == '') {
            $url = Pagination::createUrl($data);
        } else {


            $url = Pagination::createUrl($data, $page);

        }


        if ($this->nbrpages == 1) {
            return;
        }


        $html = "";

        $html .= '<ul class="' . $class . '">';


        if (($this->current_page <= $this->nbrpages and $this->current_page > 1)) {


            if (class_exists("Translate")) {
                if (empty($data)) {
                    $html .= '<li style="width: auto;" class="' . $prevClass . ' paginate_button "><a style="width: auto;    padding-left: 10px;
    padding-right: 10px;" data="' . ($this->current_page - 1) . '" href="' . $url . 'page=' . ($this->current_page - 1) . '">' . Translate::sprint("Prev") . '</a></li>';
                } else {
                    $html .= '<li style="width: auto;" class="' . $nextClass . ' paginate_button "><a style="width: auto;    padding-left: 10px;
    padding-right: 10px;" data="' . ($this->current_page - 1) . '" href="' . $url . '&page=' . ($this->current_page - 1) . '">' . Translate::sprint("Prev") . '</a></li>';
                }
            } else {
                if (empty($data)) {
                    $html .= '<li  class="' . $prevClass . ' paginate_button "><a data="' . ($this->current_page - 1) . '" href="' . $url . 'page=' . ($this->current_page - 1) . '"><<</a></li>';
                } else {
                    $html .= '<li  class="' . $nextClass . ' paginate_button "><a data="' . ($this->current_page - 1) . '" href="' . $url . '&page=' . ($this->current_page - 1) . '"><<</a></li>';
                }
            }


        }


        $html .= '<li class="inactive" style="width: auto;    padding-left: 15px;
    padding-right: 15px;">' . $this->current_page . "&nbsp;-&nbsp;" . $this->nbrpages . '</li>';


        if ($this->current_page < $this->nbrpages) {

            if (class_exists("Translate")) {
                if (empty($data)) {
                    $html .= '<li style="width: auto;" class="' . $nextClass . '"><a style="width: auto;    padding-left: 10px;
    padding-right: 10px;" data="' . ($this->current_page + 1) . '" href="' . $url . 'page=' . ($this->current_page + 1) . '#">' . Translate::sprint("Next") . '</a></li>';
                } else {
                    $html .= '<li style="width: auto;"  class="' . $nextClass . '"><a style="width: auto;    padding-left: 10px;
    padding-right: 10px;"  data="' . ($this->current_page + 1) . '" href="' . $url . '&page=' . ($this->current_page + 1) . '#">' . Translate::sprint("Next") . '</a></li>';
                }
            } else {
                if (empty($data)) {
                    $html .= '<li class="' . $nextClass . '"><a data="' . ($this->current_page + 1) . '" href="' . $url . 'page=' . ($this->current_page + 1) . '#">>></a></li>';
                } else {
                    $html .= '<li class="' . $nextClass . '"><a  data="' . ($this->current_page + 1) . '" href="' . $url . '&page=' . ($this->current_page + 1) . '#">>></a></li>';
                }
            }


        }

        $html .= '</ul>';

        return $html;

    }


    private function _pages($pages, $current_page)
    {


        if ($current_page <= 0) {
            $current_page = 1;
        }


        if ($pages > 0) {


            if (($current_page % 10) == 1) {
                $r = $current_page;
            } else {
                for ($i = $current_page; $i >= 1; $i--) {
                    if (($i % 10) == 1 and $pages >= $i) {


                        $r = $i;
                        break;
                    }
                }
            }


            if ($pages > $r) {
                $t = $r;
                $ini = $r;
            } else {
                $t = $r;
                $ini = $r;
            }
            return @$t;

        }

        return 1;
    }


    public static function createUrl($data = array(), $page = '')
    {


        if (!defined("PAGE")) {
            define("PAGE", $page);
        }

        $url = "";
        if (!empty($data)) {

            foreach ($data as $key => $v) {

                if ($v != "" or $v > 0) {

                    if ($url == "") {
                        if ($page == "") {
                            $url .= PAGE . "?";

                        } else {


                            $url .= $page . "?";
                        }
                    } else {
                        $url .= "&";
                    }

                    $url .= $key . "=" . $v;
                }
            }
        }

        if ($url == "") {
            if ($page == "")
                $url .= PAGE . "?";
            else {
                $url .= $page . "?";
            }
        }
        return $url;
    }


}

function uniord($u)
{
    // i just copied this function fron the php.net comments, but it should work fine!
    $k = mb_convert_encoding($u, 'UCS-2LE', 'UTF-8');
    $k1 = ord(substr($k, 0, 1));
    $k2 = ord(substr($k, 1, 1));
    return $k2 * 256 + $k1;
}

function is_arabic($str)
{

    if ($str == null)
        $str = "";

    if (mb_detect_encoding($str) !== 'UTF-8') {
        $str = mb_convert_encoding($str, mb_detect_encoding($str), 'UTF-8');
    }

    /*
    $str = str_split($str); <- this function is not mb safe, it splits by bytes, not characters. we cannot use it
    $str = preg_split('//u',$str); <- this function woulrd probably work fine but there was a bug reported in some php version so it pslits by bytes and not chars as well
    */
    preg_match_all('/.|\n/u', $str, $matches);
    $chars = $matches[0];
    $arabic_count = 0;
    $latin_count = 0;
    $total_count = 0;
    foreach ($chars as $char) {
        //$pos = ord($char); we cant use that, its not binary safe
        $pos = uniord($char);
        if ($pos >= 1536 && $pos <= 1791) {
            $arabic_count++;
        } else if ($pos > 123 && $pos < 123) {
            $latin_count++;
        }
        $total_count++;
    }

    if ($total_count > 0)
        if (($arabic_count / $total_count) > 0.6) {
            // 60% arabic chars, its probably arabic
            return true;
        }
    return false;
}


function kdefined($key)
{

    if (defined("HASLINKER")) {
        Checker::load();
        if (defined($key)) {
            return TRUE;
        }
    }

    return FALSE;
}


if (!function_exists('url_get_content')) {

    function url_get_content($url)
    {

        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );

        return file_get_contents($url, false, stream_context_create($arrContextOptions));
    }
}

if (!function_exists('isMobile')) {
    function isMobile()
    {
        $ctx = &get_instance();
        $ctx->load->library('user_agent');
        return $ctx->agent->is_mobile();
    }
}


if (!function_exists('textClear')) {
     function textClear($text=""){
        return trim( ($text!=null)?$text:"" );
    }
}

if (!function_exists('numVal')) {
    function numVal($val=0){
        return ( ($val==null)?0:$val );
    }
}

if (!function_exists('textVerify')) {
    function textVerify($text=""){
        return $text!=null?$text:"";
    }
}

if (!function_exists('jsonDecode')) {
    function jsonDecode($text,$associative=JSON_FORCE_OBJECT){

        if($text == null)
            return array();

        if(Json::isJson($text)){
            return json_decode($text,$associative);
        }

        return array();
    }
}


class RequestInput
{


    public static function get($key="")
    {
        $ctx = &get_instance();

        if($key != "")
            $val = $ctx->input->get($key);
        else
            $val = $ctx->input->get();

        return $val != null ? $val : ($key!=""?"":array());
    }

    public static function post($key="")
    {
        $ctx = &get_instance();

        if($key!="")
            $val = $ctx->input->post($key);
        else
            $val = $ctx->input->post();

        return $val != null ? $val :  ($key!=""?"":array());
    }
}