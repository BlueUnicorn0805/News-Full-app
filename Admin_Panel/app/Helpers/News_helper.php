<?php

//news status : 1-Active // 0-Deactive
if (!function_exists('is_news_status')) {
    function is_news_status($status)
    {
        $value = '';
        if ($status == '1') {
            $value = '<div class="badge badge-success">Active</div>';
        } elseif ($status == '0') {
            $value = '<div class="badge badge-primary">Deactive</div>';
        }
        return $value;
    }
}

//App style : style_1, style_2,style_3, style_4, style_5
if (!function_exists('style_app')) {
    function style_app($style_app)
    {
        $image = '';
        if ($style_app == 'style_1') {
            $image = '<a href="' . APP_URL . 'public/images/app_style/App_Style_1.png"  data-toggle="lightbox" data-title="Image"><img src="' . APP_URL . 'public/images/app_style/App_Style_1.png" alt="style_1" class="" height=60, width=60></a>';
        } elseif ($style_app == 'style_2') {
            $image = '<a href="' . APP_URL . 'public/images/app_style/App_Style_2.png"  data-toggle="lightbox" data-title="Image"><img src="' . APP_URL . 'public/images/app_style/App_Style_2.png" alt="style_2" class="" height=50, width=50></a>';
        } elseif ($style_app == 'style_3') {
            $image = '<a href="' . APP_URL . 'public/images/app_style/App_Style_3.png"  data-toggle="lightbox" data-title="Image"><img src="' . APP_URL . '/public/images/app_style/App_Style_3.png" alt="style_3" class="" height=50, width=50></a>';
        } elseif ($style_app == 'style_4') {
            $image = '<a href="' . APP_URL . 'public/images/app_style/App_Style_4.png"  data-toggle="lightbox" data-title="Image"><img src="' . APP_URL . 'public/images/app_style/App_Style_4.png" alt="style_4" class="" height=50, width=50></a>';
        } elseif ($style_app == 'style_5') {
            $image = '<a href="' . APP_URL . 'public/images/app_style/App_Style_5.png"  data-toggle="lightbox" data-title="Image"><img src="' . APP_URL . 'public/images/app_style/App_Style_5.png" alt="style_5" class="" height=50, width=50></a>';
        }
        return $image;
    }
}

//language status : 1-Active // 0-Deactive
if (!function_exists('is_language_status')) {
    function is_language_status($status)
    {
        $value = '';
        if ($status == '1') {
            $value = '<div class="badge badge-success">Active</div>';
        } elseif ($status == '0') {
            $value = '<div class="badge badge-primary">Deactive</div>';
        }
        return $value;
    }
}

//Featured Section status : 1-Active // 0-Deactive
if (!function_exists('is_featured_section_status')) {
    function is_featured_section_status($status)
    {
        $value = '';
        if ($status == '1') {
            $value = '<div class="badge badge-success">Active</div>';
        } elseif ($status == '0') {
            $value = '<div class="badge badge-primary">Deactive</div>';
        }
        return $value;
    }
}

// pages table list - Is page policy page or not
if (!function_exists('is_policy_page')) {
    function is_policy_page($policy)
    {
        $value = '';
        if ($policy == 'terms-policy') {
            $value = '<div class="badge badge-primary">Terms Policy</div>';
        } elseif ($policy == 'privacy-policy') {
            $value = '<div class="badge badge-info">Privacy Policy</div>';
        }
        else{
            $value = '-';
        }
        return $value;
    }
}

// news table list - Is news pexpire or not
if (!function_exists('is_expire')) {
    function is_expire($show_till)
    {
        $today = date('Y-m-d');
        $value = '';
        if ($show_till == '0000-00-00') {
            $value = '-'; 
        } elseif ($today > $show_till) {
            $value = '<div class="badge badge-danger">Expired</div>'; 
        } else {
            $value = '-'; 
        }
        return $value;
    }
}

//Web style : style_1, style_2,style_3, style_4, style_5
if (!function_exists('style_web')) {
    function style_web($style_web)
    {
        $image = '';
        if ($style_web == 'style_1') {
            $image = '<a href="' . APP_URL . 'public/images/app_style/Web_Style_1.png"  data-toggle="lightbox" data-title="Image"><img src="' . APP_URL . 'public/images/app_style/Web_Style_1.png" alt="style_1" class="" height=40, width=100></a>';
        } elseif ($style_web == 'style_2') {
            $image = '<a href="' . APP_URL . 'public/images/app_style/Web_Style_2.png"  data-toggle="lightbox" data-title="Image"><img src="' . APP_URL . 'public/images/app_style/Web_Style_2.png" alt="style_2" class="" height=40, width=100></a>';
        } elseif ($style_web == 'style_3') {
            $image = '<a href="' . APP_URL . 'public/images/app_style/Web_Style_3.png"  data-toggle="lightbox" data-title="Image"><img src="' . APP_URL . '/public/images/app_style/Web_Style_3.png" alt="style_3" class="" height=40, width=100></a>';
        } elseif ($style_web == 'style_4') {
            $image = '<a href="' . APP_URL . 'public/images/app_style/Web_Style_4.png"  data-toggle="lightbox" data-title="Image"><img src="' . APP_URL . 'public/images/app_style/Web_Style_4.png" alt="style_4" class="" height=40, width=100></a>';
        } elseif ($style_web == 'style_5') {
            $image = '<a href="' . APP_URL . 'public/images/app_style/Web_Style_5.png"  data-toggle="lightbox" data-title="Image"><img src="' . APP_URL . 'public/images/app_style/Web_Style_5.png" alt="style_5" class="" height=40, width=100></a>';
        }
        return $image;
    }
}
?>