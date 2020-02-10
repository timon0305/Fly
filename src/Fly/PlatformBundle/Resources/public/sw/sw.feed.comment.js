/**
 * Created by swaroge on 04.08.15.
 */
(function ( $ ) {
    var methods = {
        showForm : function(){
            var form = $('<form id="sw-feed-comment"></form>');
            form.append('<textarea id="sw-feed-add-field"></textarea>');
            return form;
        },
        list: function(){
            return '<ul><li>Will be soon...</li></ul>';
        }
    };

    $.fn.swFeedComment = function( options ) {

        // This is the easiest way to have default options.
        var settings = $.extend({}, options );

        this.append(methods.list);
        this.append(methods.showForm);

        console.log('swFeedComment');

        return this;

    };

}( jQuery ));