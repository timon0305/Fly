/**
 * Created by swaroge on 19.07.15.
 */
$( document ).ajaxStart(function(e) {
    //overlayPage('yes');
    //console.log(e);
});
$( document ).ajaxComplete(function(data,statusText, xhr) {
    //console.log('comlete ajax');
    //overlayPage('no');
});
$( document ).ajaxError(function(data,statusText, xhr) {
    checkAjxLogging(statusText.status);
    //overlayPage('no');
});
function checkAjxLogging(code){
    if(code == 401){
        //overlayPage('yes');
        showAjxActionAlerts(401);
        setTimeout(function(){
            window.location.href= Routing.generate('fos_user_security_login');
            return false;
        },1500);

    }
}

function showAjxActionAlerts(status,message){
    var msg = message ? message : 'Server Error';
    switch(status){
        case 401 : msg='Session expired. Redirect to login'; break;
        case 500 : msg='Server Error'; break;
    }

    $.growl(msg, {
//                ele: 'body', // which element to append to
        type: 'danger', // (null, 'info', 'danger', 'success')
        offset: 70, // 'top', or 'bottom'
        align: 'center', // ('left', 'right', or 'center')
        width: '700', // (integer, or 'auto')
        delay: '10000' , // Time while the message will be displayed. It's not equivalent to the *demo* timeOut!
//                allow_dismiss: true, // If true then will display a cross to close the popup.
//                stackup_spacing: 10 // spacing between consecutively stacked growls.);
        animate: {
            enter: 'animated fadeInRight'
            //exit: 'animated hide'
        }
    });
}

function showAjxActionMessage(type,message){


    $.growl(message, {
//                ele: 'body', // which element to append to
        type: type, // (null, 'info', 'danger', 'success')
        offset: 70, // 'top', or 'bottom'
        align: 'center', // ('left', 'right', or 'center')
        width: '700', // (integer, or 'auto')
        delay: '10000' , // Time while the message will be displayed. It's not equivalent to the *demo* timeOut!
//                allow_dismiss: true, // If true then will display a cross to close the popup.
//                stackup_spacing: 10 // spacing between consecutively stacked growls.);
        animate: {
            enter: 'animated fadeInRight'
            //exit: 'animated hide'
        }
    });
}

function overlayPage(status){
    if(status == 'yes'){
        $('.overlay').css('height','100%').show();
    }else{
        $('.overlay').hide();
    }

}