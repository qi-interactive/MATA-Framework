<?php

/**
* @link http://www.matacms.com/
* @copyright Copyright (c) 2015 Qi Interactive Limited
* @license http://www.matacms.com/license/
*/

namespace mata\helpers;

class GoogleAnalyticsHelper {

    /**
    * Works like ::truncate, but it will then get the last
    * occurence of the $character and truncate further.
    *
    * Useful when truncating to a whole word.
    */
    public static function renderTrackingCode($trackingId) {

        if (YII_DEBUG)
            return;

        echo "
        <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', '" . $trackingId . "', 'auto');
        ga('send', 'pageview');

        </script>
        ";

    }
}
