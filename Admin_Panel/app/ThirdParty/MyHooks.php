<?php

class MyHooks {

    public static function my_hook() {
        if (file_exists('install/index.php')) {
            header("location:install/");
            die();
        }
    }

}
?>