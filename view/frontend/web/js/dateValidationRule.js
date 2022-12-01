define([
    'jquery',
    'jquery/ui',
    'jquery/validate',
    'mage/translate'
    ], function($){
        'use strict';
        return function() {
            $.validator.addMethod(
                "validate-date-not-less-than-today",
                function(value, element) {
                    console.log('this is custom rule');
                    return true;
                },
                $.mage.__("Make sure the To Date is later than or the same as the From Date.")
            );
    }
});
