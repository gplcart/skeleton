/* global window, GplCart, jQuery */
(function (window, GplCart, $) {

    "use strict";

    /**
     * This function will be called once DOM is ready
     * GplCart.onload is a full equivalent of $(document).ready(function(){});
     * @returns {undefined}
     */
    GplCart.onload.callImmediately = function () {
        // alert('Called callImmediately() function');
    };

    /**
     * Custom "private" function, that is accessible only inside this module
     * @returns {undefined}
     */
    var myCustomFunction = function () {
        alert('Called myCustomFunction() function');
    };

})(window, GplCart, jQuery);