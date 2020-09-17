(function($) {

    $('.date-enrty1').datetextentry({
    	field_order : 'MDY',
    	separator   : '/',
		errorbox_x    : -100,
		errorbox_y    : 30
    });

     $('.date-enrty2').datetextentry({
    	field_order : 'MDY',
		separator   : '/',
		errorbox_x    : -100,
		errorbox_y    : 30
    });
    $('.date-enrty3').datetextentry({
    	field_order : 'MDY',
		separator   : '/',
		errorbox_x    : -100,
		errorbox_y    : 30
    });
    $('.date-enrty4').datetextentry({
        format_date : function (date) {
            function str_right(str, n) { return str.substr(str.length - n); }
            function pad2(n) { return str_right('00'   + (n || 0), 2); }
            function pad4(n) { return str_right('0000' + (n || 0), 4); }

            console.log(date); return [ pad2(date.month), pad2(date.day), pad4(date.year) ].join('/');},
            field_order : 'MDY',
            separator   : '/',
            errorbox_x    : -100,
            errorbox_y    : 30
        });
    $('form').submit(function(e) {
        e.preventDefault();
    });

})(jQuery);
