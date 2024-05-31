<?php

//get_system_timezone
if (!function_exists('get_system_timezone')) {

    function get_system_timezone()
    {
        $CI = \Config\Database::connect();
        $builder = $CI->table('tbl_settings');
        $res = $builder->where('type', 'system_timezone')->get()->getResult();
        if (!empty($res)) {
            return $res[0]->message;
        } else {
            return 'Asia/Kolkata';
        }
    }

}

//is_category_enabled
if (!function_exists('is_category_enabled')) {

    function is_category_enabled()
    {
        $CI = \Config\Database::connect();
        $builder = $CI->table('tbl_settings');
        $res = $builder->where('type', 'category_mode')->get()->getResult();
        return ($res) ? $res[0]->message : 0;
    }

}

//is_subcategory_enabled
if (!function_exists('is_subcategory_enabled')) {

    function is_subcategory_enabled()
    {
        $CI = \Config\Database::connect();
        $builder = $CI->table('tbl_settings');
        $res = $builder->where('type', 'subcategory_mode')->get()->getResult();
        return ($res) ? $res[0]->message : 0;
    }

}

//is_breaking_news_enabled
if (!function_exists('is_breaking_news_enabled')) {

    function is_breaking_news_enabled()
    {
        $CI = \Config\Database::connect();
        $builder = $CI->table('tbl_settings');
        $res = $builder->where('type', 'breaking_news_mode')->get()->getResult();
        return ($res) ? $res[0]->message : 0;
    }

}

//is_comments_enabled
if (!function_exists('is_comments_enabled')) {

    function is_comments_enabled()
    {
        $CI = \Config\Database::connect();
        $builder = $CI->table('tbl_settings');
        $res = $builder->where('type', 'comments_mode')->get()->getResult();
        return ($res) ? $res[0]->message : 0;
    }

}

//is_live_streaming_enabled
if (!function_exists('is_live_streaming_enabled')) {

    function is_live_streaming_enabled()
    {
        $CI = \Config\Database::connect();
        $builder = $CI->table('tbl_settings');
        $res = $builder->where('type', 'live_streaming_mode')->get()->getResult();
        return ($res) ? $res[0]->message : 0;
    }

}

//is_auto_news_expire_news_enabled
if (!function_exists('is_auto_news_expire_news_enabled')) {

    function is_auto_news_expire_news_enabled()
    {
        $CI = \Config\Database::connect();
        $builder = $CI->table('tbl_settings');
        $res = $builder->where('type', 'auto_delete_expire_news_mode')->get()->getResult();
        return ($res) ? $res[0]->message : 0;
    }

}

//slug generate
function slug($string, $spaceRepl = "-")
{
    $string = str_replace("&", "and", $string);
    $string = preg_replace("/[^a-zA-Z0-9 _-]/", "", $string);
    $string = strtolower($string);
    $string = preg_replace("/[ ]+/", " ", $string);
    $string = str_replace(" ", $spaceRepl, $string);

    return $string;
}

//ALLOW_MODIFICATION

if (!function_exists('is_modification_allowed')) {

    function is_modification_allowed() {
       
        $session = session();
    
        $allow_modification = ALLOW_MODIFICATION;
        
        $allow_modification = ($allow_modification == 0) ? 0 : 1;
        if (isset($allow_modification) && $allow_modification == 0) {
            return false;
        }
        return true;
    }

}

//hideEmailAddress

if (!function_exists('hideEmailAddress')) {
    function hideEmailAddress($email) {
        if(is_modification_allowed()) {
            if(filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                list($first, $last) = explode('@', $email);
                $first = str_replace(substr($first, '3'), str_repeat('*', strlen($first)-3), $first);
                $last = explode('.', $last);
                $last_domain = $last['0'];
                $hideEmailAddress = $first.'@'.$last_domain.'.'.$last['1'];
                return $hideEmailAddress;
            }  
        } else {
            return $email;
        }
    }
}

//hideMobileNumber

if (!function_exists('hideMobileNumber')) {
    function hideMobileNumber($mobile) {
        if(is_modification_allowed()){
            $first='-';
            if($mobile){
                $first = str_replace(substr($mobile, '3'), str_repeat('*', strlen($mobile)-3), $mobile);
            }
        return $first;
        }else {
            return $mobile;
        }
    }
}

//email set
if (!function_exists('is_email_setting')) {

    function is_email_setting()
    {
        $CI = \Config\Database::connect();
        $builder = $CI->table('tbl_settings');
        $email_setting = \Config\Services::email();
        $email_setting->SMTPHost = $builder->where('type', 'smtp_host')->get()->getRow()->message;
        $email_setting->SMTPUser = $builder->where('type', 'smtp_user')->get()->getRow()->message;
        $email_setting->SMTPPass = $builder->where('type', 'smtp_password')->get()->getRow()->message;
        $email_setting->SMTPPort = $builder->where('type', 'smtp_port')->get()->getRow()->message;
        $email_setting->SMTPCrypto = $builder->where('type', 'smtp_crypto')->get()->getRow()->message;
        $email_setting->fromName = $builder->where('type', 'from_name')->get()->getRow()->message;
        $email_setting->mailType = 'html';
        return $email_setting;
    }

}
?>