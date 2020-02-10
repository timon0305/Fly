/**
 * Created by Swaroge on 06.09.2016.
 */


$(function(){


    /*Image upload actions*/

    $("body").on("change",".imgUploadStorage",function(){
        imageFileStorage($(this).attr('id'),$(this).attr('data-image-area'));
    });

    /* Modal actions */
    $(document).delegate('.btn-action-modal', 'click', function(){
        var modalId = $(this).attr('data-modal-id');
        var modalAction = $(this).attr('data-modal-action');
        // var mapInit = null;
        showHideModal(modalId,modalAction);

        if(modalId == '#newAccModal'){
            setTimeout(function(){
                gmapInit('accnew','fly_platformbundle_acc_address','fly_platformbundle_acc_lat','fly_platformbundle_acc_lng');
            },500);

                $('#accImage').attr('src','/no-image.png');

         }

//         console.log('ok1111');



    });


//////// Forms

    /* new acc save */
    $('#btn-acc-save').click(function(){
        if($( "#form-new-acc" ).valid()){
            $('#form-new-acc').submit();
        }
        return;
    });


    /* new act save */
    // $('#btn-act-save').click(function(){
    //     if(!$( "#form-new-act" ).valid()){
    //         return false;
    //         //
    //     }
    //     $('#form-new-act').submit();
    //     return false;
    // });
// Accomodation forms events

    // New Accomodation
    $('#form-new-acc').submit(function() {
        var options = {
            //target:        '#output2',   // target element(s) to be updated with server response
            // beforeSubmit:  showRequest,  // pre-submit callback
            success: function (responseText, statusText, xhr, $form) {
//                console.log(responseText, statusText, xhr, $form);
                $('#newAccModal').modal('hide');
                var data = responseText.data;
                var html = Accommodation.getItemTpl(data);
                $('#acc-list-block').prepend(html);
                $('#fly_platformbundle_accitem_acc').append('<option value="'+data.id+'">'+data.name+'</option>');

            },  // post-submit callback
            error: function(data,statusText, xhr){
//                console.log(data,statusText, xhr);
            },

            dataType:  'json',        // 'xml', 'script', or 'json' (expected server response type)
            clearForm: true,       // clear all form fields after successful submit
            resetForm: true,        // reset the form after successful submit

        };
        $(this).ajaxSubmit(options);

        return false;
    });

    // New Accomodation Item
    $('#acc-item-form').submit(function() {
        var options = {
            //target:        '#output2',   // target element(s) to be updated with server response
            // beforeSubmit:  showRequest,  // pre-submit callback
            success: function (response, statusText, xhr, $form) {
                // console.log(response, statusText, xhr, $form);
                if(response.asc == 'success'){
                    // addItemToCalendar(response.data,'accItem');
                    showHideModal('#newPkgModal','hide');
                    $('#calendar').fullCalendar( 'refetchEvents' );
                }else{
                    alert(response.msg);
                }


            },  // post-submit callback
            error: function(data,statusText, xhr){
//                console.log(data,statusText, xhr);
            },

            dataType:  'json',        // 'xml', 'script', or 'json' (expected server response type)
            clearForm: true,       // clear all form fields after successful submit
            resetForm: true,        // reset the form after successful submit

        };
        $(this).ajaxSubmit(options);

        return false;
    });

    // Edit
    $(document).delegate('#acc-item-edit-form','submit',function(){

        var options = {
            beforeSubmit:  function(){
//                alert('beforeSubmit');
                if(!$( "#acc-item-edit-form" ).valid()){
                    alert('Invalid Form');
                    return false;
                    //
                }
            },  // pre-submit callback
            success: function (response, statusText, xhr, $form) {
                // console.log(responseText, statusText, xhr, $form);
                if(response.asc == 'success'){
                    $('#calendar').fullCalendar( 'refetchEvents' );
                    showHideModal('#newPkgModal','hide');
                }else{
                    alert(response.msg);
                }

            },  // post-submit callback
            error: function(data,statusText, xhr){
//                console.log(data,statusText, xhr);
            },

            dataType:  'json',        // 'xml', 'script', or 'json' (expected server response type)
            clearForm: true,       // clear all form fields after successful submit
            resetForm: true,        // reset the form after successful submit

        };
        $(this).ajaxSubmit(options);
        return false;
    });

//////////// Action form events

    //New form
    $('#form-new-act').submit(function() {
        var options = {
            //target:        '#output2',   // target element(s) to be updated with server response
             beforeSubmit:  function(){
//                 alert('beforeSubmit');
                     if(!$( "#form-new-act" ).valid()){
                         alert('Invalid Form');
                         return false;
                         //
                     }
             },  // pre-submit callback
            success: function (response, statusText, xhr, $form) {
                // console.log(responseText, statusText, xhr, $form);
                if(response.asc == 'success'){
                    // addItemToCalendar(response.data,'actItem');
                    showHideModal('#newPkgModal','hide');
                    $('#calendar').fullCalendar( 'refetchEvents' );
                }else{
                    alert(response.msg);
                }

            },  // post-submit callback
            error: function(data,statusText, xhr){
//                console.log(data,statusText, xhr);
            },

            dataType:  'json',        // 'xml', 'script', or 'json' (expected server response type)
            clearForm: true,       // clear all form fields after successful submit
            resetForm: true,        // reset the form after successful submit

        };
        $(this).ajaxSubmit(options);

        return false;
    });

    // Edit form
    $(document).delegate('#form-edit-act','submit',function(){

        var options = {
            beforeSubmit:  function(){
//                alert('beforeSubmit');
                if(!$( "#form-edit-act" ).valid()){
                    alert('Invalid Form');
                    return false;
                    //
                }
            },  // pre-submit callback
            success: function (response, statusText, xhr, $form) {
                // console.log(responseText, statusText, xhr, $form);
                if(response.asc == 'success'){
                    $('#calendar').fullCalendar( 'refetchEvents' );
                    showHideModal('#newPkgModal','hide');
                }else{
                    alert(response.msg);
                }

            },  // post-submit callback
            error: function(data,statusText, xhr){
//                console.log(data,statusText, xhr);
            },

            dataType:  'json',        // 'xml', 'script', or 'json' (expected server response type)
            clearForm: true,       // clear all form fields after successful submit
            resetForm: true,        // reset the form after successful submit

        };
        $(this).ajaxSubmit(options);

        return false;
    });


/////////// Remova Event from Claendar Btn
    $(document).delegate('.remove-event','click',function(){
        var confirmRes = confirm('Are you really whant to delete this Item?');
        if(confirmRes){
            var eventId = $(this).attr('data-event-id');
            var type = $(this).attr('data-event-type');
            removeItem(eventId,type);
        }
    });



/**
 * Accomodation price calculate
 */
    $(document).delegate('.acc-item-block','click',function(){
        Accommodation.selectItem($(this).attr('id'));
    });

    $(document).delegate('#fly_platformbundle_accitem_duration','keyup mouseup',function(){
        if($(this).val()){
            Accommodation.claculadeDurationEvent();
        }
        return false;
    });

    $(document).delegate('#fly_platformbundle_accitem_checkin','change',function(){

            Accommodation.claculadeDurationEvent();

        return false;
    });

    $('#acc-pkg-cancel').click(function(){

       resetForm($(this).attr('data-form-id'));

    });




    /*Package Save*/
    $('.btn-pkg-save').click(function(){
        if($( "#form-package" ).valid()){
            addPackage($(this).attr("data-pkg-type"));
        }
        return;
    });


    /*Calendar actions*/
    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        defaultView: 'month',
        editable: true,
        eventRender: function(event, element, view) {
            // console.log(element, view);

        },
        eventAfterRender: function( event, element, view ) {
            // console.log(event, element, view, $(element[0]).closest('.fc-event-container'));
            var html = "<div class='tooltiptext'>";
            html += "<div class='tooltiptext-address'><i class='fa fa-map-marker'></i> "+event.address+"</div>";
            if(event.img) {
                html += "<div class='tooltiptext-img'><img src='/uploads/" + event.img + "' ></div>";
            }
            if(event.description){
                html += "<div class='tooltiptext-descr'>"+event.description.substring(0,160)+"</div>";
            }
            html += "</div>";
            $(element[0]).closest('.fc-event-container').append(html);

            $(element[0]).qtip({
                content: {
                    // title: event.title,
                    text: $(element[0]).next('.tooltiptext'),

                },
                position: {
                    at: 'bottom center'
                },
                hide: {
                    fixed: true,
                    delay: 300
                }
            });
        },
        dayClick: function(date, jsEvent, view) {
            showHideModal('#newPkgModal','show');
            $('#newPkgModalLabel').html('<i class="fa fa-cube"></i> Create New Package');
            $(".pkg-tab li").removeClass('active');
            $(".pkg-tab-content .tab-pane").removeClass('active');
            $('#pkgName').val('');

            $('#packageTabs a[href="#activites"]').parent().show();
            $('#packageTabs a[href="#accommodation"]').parent().show();


            $('#acc-item-edit-form').remove();
            $('#acc-item-form').removeClass('hidden');
            //
            $('#form-edit-act').remove();
            $('#form-new-act').removeClass('hidden');




            //Accomodation datepicker
            setDatePicker("fly_platformbundle_accitem[checkin]",'DD/MM/YYYY',true,false,date.format('DD/MM/YYYY'),false);
            // setDatePicker("pkg-hotel-checkout",'YYYY-MM-DD',true,false,false,false);

//                    Activities Datepicker pkg-act-start, pkg-act-end
            setDatePicker("fly_platformbundle_act[actItem][0][checkin]",'DD/MM/YYYY',true,true,date.format('DD/MM/YYYY'),false);
            setDatePicker("fly_platformbundle_act[actItem][0][checkout]",'DD/MM/YYYY',true,true,false,false);

//                    Other Datepicker pkg-other-start, pkg-other-end
//             setDatePicker("pkg-other-start",'DD/MM/YYYY',true,true,date.format('DD/MM/YYYY'),false);
//             setDatePicker("pkg-other-end",'DD/MM/YYYY',true,true,false,false);
        },
        eventClick: function(calEvent, jsEvent, view) {

           getItemEditForm(calEvent.entId, calEvent.type);
            // console.log(calEvent,jsEvent,view, $(this));
            // alert('Event: ' + calEvent.title);
            // alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
            // alert('View: ' + view.name);

            // change the border color just for fun
            // $(this).css('border', '1px solid red');

        },
        events: function(start, end, timezone, callback) {
            $.ajax({
                url: Routing.generate('fly_platform_package_calendar_events'),
                type: 'POST',
                format: 'json',
                async: false,
                data: {},
                success: function (res, textStatus, xhr) {
                    // console.log(res.data);
                    var eventsItems = [];
                    if(res.asc == 'success'){

                        for(x in res.data){
                            if(res.data[x].type == 'accItem'){
                                eventsItems.push({
                                    entId: res.data[x].id,
                                    type: res.data[x].type,
                                    title: res.data[x].acc.name,
                                    start: res.data[x].checkin.date,
                                    end: res.data[x].checkout.date,
                                    address: res.data[x].acc.address,
                                    img: res.data[x].acc.picture,
                                    description: res.data[x].acc.description,
                                    price: res.data[x].acc.price,
                                    currency: Accommodation.getCurrencySign(res.data[x].acc.currency),
                                    category: (res.data[x].acc.category)?res.data[x].acc.category.title:null,
                                    duration: res.data[x].duration,
                                    backgroundColor: '#204d74'

                                })
                            }

                            if(res.data[x].type == 'actItem'){
                                eventsItems.push({
                                    entId: res.data[x].id,
                                    type: res.data[x].type,
                                    title: res.data[x].act.name,
                                    start: res.data[x].checkin.date,
                                    end: res.data[x].checkout.date,
                                    address: res.data[x].act.address,
                                    // img: res.data[x].acc.picture,
                                    // description: res.data[x].acc.description,
                                    price: res.data[x].act.price,
                                    currency: Accommodation.getCurrencySign(res.data[x].act.currency),
                                    // category: res.data[x].act.category.title,
                                    // duration: res.data[x].duration
                                    backgroundColor: '#ff9800'

                                })
                            }

                        }

                        // console.log(eventsItems);
                        callback(eventsItems);
                    }

                },
                error: function(){
                    alert('errors');
                }
            });
        }



    })


    /*Tabs actions*/
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        e.target // newly activated tab
//        console.log($(e.target).attr('href'));
        if($(e.target).attr('aria-controls') == 'accommodation'){
            // init map new acc form
            getAccomodationList(1);
            $('#acc-item-edit-form').remove();
            $('#pkgType').val('accommodation');
            $('#acc-price-amount').html('--');
        }
        if($(e.target).attr('aria-controls') == 'activites'){
            $('#form-edit-act').remove();
                gmapInit('actnew','fly_platformbundle_act_address','fly_platformbundle_act_lat','fly_platformbundle_act_lng');
            $('#pkgType').val('activites');
        }
        if($(e.target).attr('aria-controls') == 'other'){
            gmapInit('other');
            $('#pkgType').val('other','other_geoAddress','other_geoLat','other_geoLng');
        }
        e.relatedTarget // previous active tab
    })


});

//////////////////// Get items and put its on Calendar ///////////////////
function getItems(){

    $.ajax({
        url: Routing.generate('accitems_json_calendar'),
        type: 'POST',
        format: 'json',
        async: false,
        data: {},
        success: function (res, textStatus, xhr) {
            // console.log(res.data);
            var eventsItems = [];
            if(res.asc == 'success'){

                for(x in res.data){
                    eventsItems.push({
                        title: res.data[x].acc.name,
                        start: res.data[x].checkin.date,
                        end: res.data[x].checkout.date,
                        address: res.data[x].acc.address,
                        img: res.data[x].acc.picture,
                        description: res.data[x].acc.description,
                        price: res.data[x].acc.price,
                        currency: Accommodation.getCurrencySign(res.data[x].acc.currency),
                        category: res.data[x].acc.category.title,
                        duration: res.data[x].duration

                    });

                }

//                console.log(eventsItems);
                callback(eventsItems);
            }

        },
        error: function(){
            alert('errors');
        }
    });


}

//////////////////// Remove Item from  Calendar ///////////////////
function removeItem(id,type){
    $.ajax({
        url: Routing.generate('fly_platform_package_calendar_events_remove',{id: id, type: type}),
        type: 'POST',
        format: 'json',
        async: false,
        data: {},
        success: function (res, textStatus, xhr) {
            // console.log(res.data);
            if(res.asc == 'success'){
                $('#calendar').fullCalendar( 'refetchEvents' );
                showHideModal('#newPkgModal','hide');
            }else{
                alert(res.msg);
            }

        },
        error: function(){
            alert('errors');
        }
    });
}

//////////////////// Get form for edit Item  ///////////////////
function getItemEditForm(id,type){

    $.ajax({
        url: Routing.generate('fly_platform_package_calendar_events_edit_form',{id:id, type:type}),
        type: 'POST',
        format: 'json',
        async: true,
        data: {},
        success: function (res, textStatus, xhr) {
            if(res.asc == 'success'){
                showHideModal('#newPkgModal','show');
                $('#newPkgModalLabel').html('<i class="fa fa-edit"></i> Edit Package Item');
                // alert(res.type);
                if(res.type == 'accItem'){
                    $('#packageTabs a[href="#activites"]').parent().hide();
                    $('#packageTabs a[href="#accommodation"]').parent().show();
                    $('#acc-item-form').addClass('hidden');
                    $('#packageTabs a[href="#accommodation"]').tab('show') // Select tab by name

                    $('#acc-item-edit-form').remove();
                    $('.pkg-acc-form-block').after(res.forms.accItemFormView);

                    $(".acc-item-block").removeClass('active');
                    $('#acc-item-'+res.accId).addClass("active");
                    Accommodation.claculadeDurationEvent();
                    setDatePicker("fly_platformbundle_accitem[checkin]",'DD/MM/YYYY',true,false,false,false);
                }

                if(res.type == 'actItem'){
                    $('#packageTabs a[href="#accommodation"]').parent().hide();
                    $('#packageTabs a[href="#activites"]').parent().show();
                    $('#form-new-act').addClass('hidden');
                    $('#packageTabs a[href="#activites"]').tab('show') // Select tab by name
                    // $('#pkgType').val('activites');

                    $('#form-edit-act').remove();
                    $('#activites').append(res.forms.actFormView);
                    setDatePicker("fly_platformbundle_act[actItem][0][checkin]",'DD/MM/YYYY',true,true,false,false);
                    setDatePicker("fly_platformbundle_act[actItem][0][checkout]",'DD/MM/YYYY',true,true,false,false);
                    setTimeout(function(){
                        gmapInit('actedit','fly_platformbundle_act_address','fly_platformbundle_act_lat','fly_platformbundle_act_lng');
                    }, 500);

                }

            }else{
                alert(res.msg);
            }
        },
        error: function(){
            alert('Cant Edit Event');
        }
    });


}
////////////////////// End ///////////////////////////////////////////////


var Package = {
    modalId: null,
    calendar: null,
    calendarConfig: {
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        defaultView: 'month',
        editable: true
    },
    params: {
        id: null,
        name: null,
        type: null,
        start: null,
        end: null,
        accomodation: {},
        description: null,
        lat: null,
        lng: null,
        address: null,
    },



    init: function(modalId,calendar){

        this.modalId = modalId;
        this.calendar = calendar;

    },

    save: function(){

    },
    edit: function(pkg){

    },
    update: function(pkg){

    },
    remove: function(pkg){

    }

}


///////////////// Accommodation ////////////////

function getAccomodationList(refresh){
    //return;
    $('#acc-loader').show();
    if(refresh == 1){
        $('#acc-list-block').html('');
    }
    $.ajax({
        url: Routing.generate('acc'),
        type: 'POST',
        format: 'json',
        async: false,
        data: {},
        success: function (data, textStatus, xhr) {
            if(data.asc == 'success'){
                $('#acc-loader').hide();
                if(data.acc.length){
                    for(x in data.acc){
                        var html = Accommodation.getItemTpl(data.acc[x]);
                        $('#acc-list-block').append(html);
                    }

                    if(data.next){
                        var nextBlock = $('#acc-list-block').find('a.view-more');
                        if(nextBlock){
                            nextBlock.attr('data-page-next',data.next)
                        }else{
                            nextBlock = '<div class="load-more-hotels text-center"><a class="view-more" href="">Show more</a></div>'
                            $('#acc-list-block').append(nextBlock);
                        }
                    }


                }else{
                    //$('#acc-list-block').html(nextBlock);
                }
                //hide loader
                //show list
            }
            if(data.asc == 'error'){
                //hide loader;
            }


        }
    });
}


var Accommodation = {
    getItemTpl: function(item){
        var html = '<a class="list-group-item media acc-item-block" id="acc-item-'+item.id+'" data-item-id="'+item.id+'" href="#" onclick="return false">';
        html += '<div class="pull-left">';
        html += '<img class="lgi-img hotel-img img-responsive" src="/uploads/'+item.picture+'" alt="">';
        html += '</div>';
        html += '<div class="media-body">';
        html += '<div class="col-md-8">';
        html += '<div class="lgi-heading"><strong>'+item.name+'</strong></div>';
        html += '<div class="lgi-heading"><i class="fa fa-map-marker"></i> '+item.address+'</div>';
        html += '<div class="lgi-heading acc-cat">';
        if(item.category){
            html += '<span class="label label-hotel-params">'+item.category.title+'</span>';
        }else{
            html += '<span class="label label-hotel-params"></span>';
        }

        html += '</div>';
        // html += '<small class="lgi-text">'+item.description+'</small>';
        html += '</div>';
        html += '<div class="col-md-4 price-block">';
        html += 'Price: <span class="currency">'+item.currency+'</span><span class="price">'+item.price+'</span>';
        html += '</div>';
        html += '<div class="clearfix"></div>';
        html += '</div>';
        html += '</a>';

        return html;
    },
    selectItem: function(itemId){
        $('.acc-item-block').removeClass('active');
        $('#'+itemId).addClass('active');
        this.calculate(itemId);

    },
    getCurrencySign: function(c){
        var signs = {
            eur: '&euro;',
            usd: '$'
        }

        if(c == 'EUR'){return signs.eur}
        if(c == 'USD'){return signs.usd}
        return '&curren;';
    },
    calculate: function(itemId){
        var accId = $('#'+itemId).attr('data-item-id');
        var checkout;
        var amount;
        var checkin = $('#fly_platformbundle_accitem_checkin').val();
        var duration = $('#fly_platformbundle_accitem_duration').val();
        var currency = $('#'+itemId+' .currency').text();
        var pricePerNight = parseFloat($('#'+itemId+' .price').text());

        if(!duration){
            duration = 1;
            $('#fly_platformbundle_accitem_duration').val(1);
        }

        /// Calculate
        /*Convert date checkin*/
        var checkinArr = checkin.split('/');
        var checkoutDate = new Date(checkinArr[1]+'/'+checkinArr[0]+'/'+checkinArr[2]);
        /*Add duration to date checkin = checkout*/
        checkoutDate.setDate(checkoutDate.getDate()+parseInt(duration));
        /*convert checkout date to string*/
        var monthCheckout = checkoutDate.getMonth();
        monthCheckout++;
        monthCheckout = (monthCheckout/10 < 1) ? '0'+monthCheckout : monthCheckout;
        var dayCheckout = checkoutDate.getDate();
        dayCheckout = (dayCheckout/10 < 1) ? '0'+dayCheckout : dayCheckout;
        var yearCheckout = checkoutDate.getFullYear();
        checkout = dayCheckout+'/'+monthCheckout+'/'+yearCheckout;

        /*Calculate amount duration*pricePerNight */
        amount = parseFloat(duration * pricePerNight);
        amount = Math.round(amount * 100) / 100;
        /*show calculated amount*/
        $('#acc-price-amount').html(this.getCurrencySign(currency)+amount);

        /// Set the values to checkout, and accomodation ID
        /*set checkout field value*/
        $('#fly_platformbundle_accitem_checkout').val(checkout);
        /*set accomodation id value*/
        $('#fly_platformbundle_accitem_acc').val(accId);

    },
    claculadeDurationEvent: function(){
        var accId = $('#fly_platformbundle_accitem_acc').val();
        if(accId){
            var itemId = 'acc-item-'+accId;
            this.calculate(itemId);
        }else{
            // alert('accomodetion not select')
        }
    }
}


////////////////// END Accommodation /////////////


///////////////// Helpers

function resetForm(id){
    var form = $('#'+id);
    form[0].reset();
}

function setDatePicker(inputName,format,single,timed,startDate,endDate){
    var attr = {
        showDropdowns: true
    };

    if(format){
        attr.locale =   {format: format}
    }

    if(single){
        attr.singleDatePicker = true;
    }

    if(timed){
        attr.timePicker = true;
    }

    if(startDate){
        attr.startDate = startDate;
    }

    if(startDate){
        attr.endDate = endDate;
    }

    $('input[name="'+inputName+'"]').daterangepicker(attr);
}

function validateFormPackage(){
    $("form[name='form-package']").validate({
        rules: {
            pkgName: "required",
        },
        messages: {
            firstname: "Please enter Package name"
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
}

function validateFormAcc(){
    $("form[name='fly_platformbundle_acc']").validate({
        rules: {
            "fly_platformbundle_acc[name]": "required"
        },
        messages: {
            "fly_platformbundle_acc[name]": "Please enter Name"
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
}

function validateFormAct(){
    $("form[name='fly_platformbundle_acc']").validate({
        rules: {
            "fly_platformbundle_acc[name]": "required"
        },
        messages: {
            "fly_platformbundle_acc[name]": "Please enter Name"
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
}


/*Create new Package*/
function  addPackage(type){
    var event = {};
    var type = type;
    var bgColorEvent = '#ccc';
    var start = null;
    var end = null;
    switch(type){
        case 'acc':
            bgColorEvent='#204d74';
            start = $('#pkg-hotel-checkin').val();
            end =  $('#pkg-hotel-checkout').val();
            break;
        case 'act':
            bgColorEvent='#5cb85c';
            start = $('#pkg-act-start').val();
            end =  $('#pkg-act-end').val();
            break;
        case 'other':
            bgColorEvent='#ec971f';
            start = $('#pkg-other-start').val();
            end =  $('#pkg-other-end').val();
            break;
        default: alert('Unknow Package type'); $('#newPkgModal').modal('hide'); return;
    }

    event.title=$('#pkgName').val();
    event.color = bgColorEvent;
    event.textColor = '#fff';
    event.start = start;
    event.end = end;
    event.editable = true;
    event.description = ''
    //console.log(event);

    $('#calendar').fullCalendar( 'addEventSource', [event] );
    $('#newPkgModal').modal('hide');
}

/*Calendar events manipulation*/
function addItemToCalendar(item,type){
    // console.log(item,type);
    var event = {};
    var type = type;
    var bgColorEvent = '#ccc';
    var start = null;
    var end = null;
    switch(type){
        case 'accItem':
            // bgColorEvent='#204d74';
            event.entId = item.id,
            event.type = type;
            event.title=item.acc.name;
            event.start = item.checkin.date;
            event.end =   item.checkout.date;
            event.address = item.acc.address;
            event.img = item.acc.picture;
            event.description = item.acc.description;
            event.price = item.acc.price;
            event.currency = item.acc.currency;
            event.category = item.acc.category.title;
            event.duration = item.duration;
            event.backgroundColor = '#204d74';
            break;
        case 'actItem':
            event.entId = item.id,
            event.color = '#204d74';
            event.type = type;
            event.title=item.act.name;
            event.start = item.checkin.date;
            event.end =   item.checkout.date;
            event.address = item.act.address;
            event.price = item.act.price;
            event.currency = item.act.currency;
            event.backgroundColor='#ff9800';
            break;
        default: alert('Unknow Package type'); $('#newPkgModal').modal('hide'); return;
    }


    event.textColor = '#fff';
    event.editable = true;
    //console.log(event);

    $('#calendar').fullCalendar( 'addEventSource', [event] );
    $('#newPkgModal').modal('hide');
}



/*Google Map*/
function  gmapInit(prefix, idAddress, idLat, idLong){

//    console.log(prefix)
//    console.log('pac-input-'+prefix)
//    console.log('start Google Map Init')
    // var MapInit = this.map;
    var map=null;
    var input = null;
    var input = document.getElementById('pac-input-'+prefix);
//            var types = null;
    var autocomplete = null;
    var infowindow = null;
    var marker = null;
    var place = null;

    map = new google.maps.Map(document.getElementById(prefix+'-map'),{
        center: {lat: 48.841878, lng: 2.357919},
        zoom: 5
    });

//            types = document.getElementById('type-selector-'+prefix);
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
//            map.controls[google.maps.ControlPosition.TOP_LEFT].push(types);

    autocomplete = new google.maps.places.Autocomplete(input);
    autocomplete.bindTo('bounds', map);

    infowindow = new google.maps.InfoWindow();
    marker = new google.maps.Marker({
        map: map,
        anchorPoint: new google.maps.Point(0, -29)
    });


    autocomplete.addListener('place_changed', function() {
        infowindow.close();
        marker.setVisible(false);
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            window.alert("Autocomplete's returned place contains no geometry");
            return;
        }

        //set the value of place
        //console.log(place);
        //console.log(place.formatted_address);
        //console.log(place.geometry.location.lat());
        //console.log(place.geometry.location.lng());

        $('#'+idAddress).val(place.formatted_address);
        $('#'+idLat).val(place.geometry.location.lat());
        $('#'+idLong).val(place.geometry.location.lng());
//                $('#fos_user_group_form_is_wheretogo').prop('checked',false);
//                $('label[for="fos_user_group_form_is_wheretogo"]').removeAttr('style');
//                GroupStepForm.map = true;

        // If the place has a geometry, then present it on a map.
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);  // Why 17? Because it looks good.

        }
        marker.setIcon(/** @type {google.maps.Icon} */({
            url: place.icon,
            size: new google.maps.Size(71, 71),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(17, 34),
            scaledSize: new google.maps.Size(35, 35)
        }));
        marker.setPosition(place.geometry.location);
        marker.setVisible(true);
//                GroupStepForm.MapMarker = marker;

        var address = '';
        if (place.address_components) {
            address = [
                (place.address_components[0] && place.address_components[0].short_name || ''),
                (place.address_components[1] && place.address_components[1].short_name || ''),
                (place.address_components[2] && place.address_components[2].short_name || '')
            ].join(' ');
        }

        infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
        infowindow.open(map, marker);
//                GroupStepForm.MapInfoWindow = infowindow;
    });

    // Sets a listener on a radio button to change the filter type on Places
    // Autocomplete.
    //function setupClickListener(id, types) {
    //    var radioButton = document.getElementById(id);
    //    console.log(radioButton);
    //    radioButton.addEventListener('click', function() {
    //        autocomplete.setTypes(types);
    //    });
    //}
    //
    //setupClickListener('changetype-all', []);
    //setupClickListener('changetype-address', ['address']);
    //setupClickListener('changetype-establishment', ['establishment']);
    //setupClickListener('changetype-geocode', ['geocode']);



}

function showHideModal(id,act){
    if(act == 'show'){
        $(id).modal('show');
    }else{
        var form = $(id).find('form');
        if(form){
            form[0].reset();
            var validator = form.validate();
            validator.resetForm();
        }
        $(id).modal('hide');

        $('#acc-item-edit-form').remove();
    }
}

/**
 * Upload file storage api
 */
function imageFileStorage(inputId,imageAreaId){
    // var img = new Image();
    // img.src = localStorage.theImage;

    //Equivalent of getElementById
    var fileInput = $('#'+inputId)[0];//returns a HTML DOM object by putting the [0] since it's really an associative array.
    var file = fileInput.files[0]; //there is only '1' file since they are not multiple type.

    var reader = new FileReader();
    reader.onload = function(e) {
        // Create a new image.
        var img = new Image();

        img.src = reader.result;
        localStorage.theImage = reader.result; //stores the image to localStorage
        $(imageAreaId).attr('src',img.src);
    }

    reader.readAsDataURL(file);//attempts to read the file in question.
}

/**
 * upload file ajax
 */



