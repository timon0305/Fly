/**
 * Created by swaroge on 03.08.15.
 */
(function ( $ ) {

    var methods = {
        swModalTemplate : function(options) {
            var modalFade = $('<div class="modal fade in" data-modal-color="bluegray" id="modalSwLightbox" data-backdrop="static" data-keyboard="true" tabindex="-1" role="dialog" aria-hidden="false" ></div>');
            var modalDialogWrap = $('<div class="modal-dialog modal-lg"></div>');
            var modalContent = $('<div class="modal-content"></div>');
            var modalHeader = $('<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            var modalBody = $('<div class="modal-body"></div>');
            var modalFooter = $('<div class="modal-footer"></div>');
            var modalFooterBtnSave = $('<button type="button" class="btn btn-link waves-effect">Save changes</button>');
            var modalFooterBtnClose = $('<button type="button" class="btn btn-link waves-effect" data-dismiss="modal">Close</button>');

            if(options.type == 'image'){
                modalBody.append('<img src="'+options.url+'" class="img-responsive" />');
            }
            if(options.type == 'youtube'){
                modalBody.append('<iframe src="'+options.embed+'" frameborder="0" allowfullscreen width="100%" height = "500px"></iframe>')
            }

            var modalSw = modalFade.append(modalDialogWrap.append(modalContent.append(modalHeader).append(modalBody).append(modalFooter.append(modalFooterBtnClose))))

            return modalSw;

            //modalSw.modal('show');

        },
        swShowModal : function( ) {

        },
        swHideModal : function( ) {

        },
        getVideoHeight: function(){

        }
    };


    $.fn.swlightbox = function( options ) {

        // This is the easiest way to have default options.
        var settings = $.extend({}, options );


        var tpl = methods.swModalTemplate(options);
        $('body').append(tpl);
        tpl.modal('show');

        console.log(this.attr('href'));

        return this;



    };

}( jQuery ));