/*!
 * @package    yii2-krajee-base
 * @subpackage yii2-widget-activeform
 * @author     Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright  Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2016
 * @version    1.8.5
 *
 * Common client validation file for all Krajee widgets.
 *
 * For more JQuery/Bootstrap plugins and demos visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
var kvListenEvent;
(function ($) {
    "use strict";

    kvListenEvent = function (event, selector, callback) {
        var $body = $(document.body);
        if ($body.length) {
            $body.on(event, selector, callback);
        } else {
            $(selector).on(event, callback);
        }
    };

})(window.jQuery);
