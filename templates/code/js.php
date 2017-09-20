/* global window, Gplcart, jQuery */
(function (window, Gplcart, $) {

    "use strict";

    /**
     * This function will be called once DOM is ready
     * Gplcart.onload is a full equivalent of $(document).ready(function(){});
     * @returns {undefined}
     */
    Gplcart.onload.callImmediately = function () {
        // alert('Called callImmediately() function');
    };

    /**
     * Custom "private" function, that is accessible only inside this module
     * @returns {undefined}
     */
    var myCustomFunction = function () {
        alert('Called myCustomFunction() function');
    };

})(window, Gplcart, jQuery);