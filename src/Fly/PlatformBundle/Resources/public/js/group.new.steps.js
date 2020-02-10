$(function(){
   $(document).delegate('.create-group-steps', 'click',function(e){
       e.preventDefault();
       $('.group-form-step').removeClass('fadeOutLeft').removeClass('fadeInRight');
       $('#modalGroupNew').modal('show');
       return false;
    });




});



/**Object**/
GroupStepForm = {
    selector: null,
    progressBgm: 'bgm-lightgreen',
    labelBgm: '',
    map: null,
    MapInit: null,
    MapMarker: null,
    MapInfoWindow: null,
    errorMessages: {
        maxError: "You can choose up to %s%",
        minError: "You should choose at least  %s%",
        requiredFields: "Please fill required field %s%",
        uniqueFields: "%s%",
        maxCharFields: "Maximum text characters length - %s%",
        geoFieldsAll: 'Please search Address OR check  "We don\'t know yet, we\'ll decide!"',
        whenToGoFieldsAll: 'Please select Departure Date and Way Back Date OR check  "We don\'t know yet, we\'ll decide!"',
        whenToGoFieldsDeparture: 'Please select Departure Date',
        whenToGoFieldsWayBack: 'Please select Way Back Date',
    },
    form: {
        title: '',
        steps: [
        ]
    },
    stepActive: 1,

    /****************** STEP FORM ACTIONS *******************/

    stepAction: function(btn){
        var step = btn.attr('data-form-block');
        //var stepNext = Math.floor(step)+1;
        var formBlock = $('#form-step-'+step);

        var stepType = $('#form-step-'+step).attr('data-step-type');
        //console.log(step,stepType);

        //var stepNext = this.form.steps[Math.floor(this.stepActive)];
        var direction = btn.attr('data-form-step');
        var formBlockEffect = (direction == 'next')?'fadeOutLeft':'fadeOutRight';
        var formBlockNextEffect = (direction == 'next')?'fadeInRight':'fadeInLeft';

        var formBlockNumNext = (direction == 'next') ? Math.floor(step)+1 : Math.floor(step)-1 ;
        var formBlockNext = $('#form-step-'+formBlockNumNext);
        var formBlockNextType =formBlockNext.attr('data-step-type');

        //console.log(step,stepType,formBlockNumNext,formBlockNextType);

        if(direction == 'next'){
            if(stepType=='groupName'){
                //alert('check Group Name, Description, Image');
                var valid = this.validateAction(step);
                if(valid === true){
                    this.slideAction(formBlock,formBlockEffect,formBlockNext,formBlockNextEffect,formBlockNumNext,true);
                }else{
                    this.errorsAction(valid);
                    return false;
                }
            }
            if(stepType == 'choices'){
                /*validate*/
                var valid = this.validateAction(step);
                if(valid === true){
                    this.slideAction(formBlock,formBlockEffect,formBlockNext,formBlockNextEffect,formBlockNumNext,true);
                }else{
                    this.errorsAction(valid);
                    return false;
                }
            }

            if(stepType=='geo'){
                var valid = this.validateAction(step);
                if(valid === true){
                    this.slideAction(formBlock,formBlockEffect,formBlockNext,formBlockNextEffect,formBlockNumNext,true);
                }else{
                    this.errorsAction(valid);
                    return false;
                }
            }

            if(stepType=='whenToGo'){
                var valid = this.validateAction(step);
                if(valid === true){
                    this.slideAction(formBlock,formBlockEffect,formBlockNext,formBlockNextEffect,formBlockNumNext,true);
                }else{
                    this.errorsAction(valid);
                    return false;
                }
            }

            this.slideAction(formBlock,formBlockEffect,formBlockNext,formBlockNextEffect,formBlockNumNext,true);

        }else{
            //var stepNext = this.form.steps[formBlockNumNext];
            this.slideAction(formBlock,formBlockEffect,formBlockNext,formBlockNextEffect,formBlockNumNext,false);
        }

        if(formBlockNextType=='geo'){
            if(!GroupStepForm.MapInit){
                setTimeout(function() {
                    GroupStepForm.gmapInit(formBlockNumNext);
                },1500);
            }
        }



    },

    slideAction: function(formBlock,formBlockEffect,formBlockNext,formBlockNextEffect,formBlockNumNext,next){
        removeFadeClasses(formBlock);
        formBlock.addClass(formBlockEffect);
        setTimeout(function(){
            formBlock.css('display','none');
            removeFadeClasses(formBlockNext);
            formBlockNext.addClass(formBlockNextEffect).css('display','block');

            GroupStepForm.progressAction(GroupStepForm.stepActive,formBlockNumNext,next);
            GroupStepForm.stepActive = formBlockNumNext;

        },600);
    },

    progressAction: function(fromNum, toNum, next){
        //var bgm = 'bgm-lightgreen';
        //console.log(fromNum, toNum, next);

        /*active Li*/
        var activeLi = $('#form-progress-'+toNum).parent('li');
        $('.form-step-progress li').removeClass('active');
        activeLi.addClass('active');
        /*btns*/
        var fromBtn = $('#form-progress-'+fromNum+' button');
        var toBtn = $('#form-progress-'+toNum+' button');

        if(!fromBtn.hasClass(this.progressBgm) && next){
            fromBtn.addClass(this.progressBgm)
        }

        if(toBtn.hasClass('disabled')){
            toBtn.removeClass('disabled')
        }
    },

    progressSlideAction: function(toStep){


        var activeStep = Math.floor(this.stepActive);
        var targetStep = Math.floor(toStep);

        if(targetStep == activeStep){
            return false;
        }


        var activeBlockEffect = (targetStep > activeStep)?'fadeOutLeft':'fadeOutRight';
        var targetBlockEffect = (targetStep > activeStep)?'fadeInRight':'fadeInLeft';

        var activeBlock = $('#form-step-'+activeStep);
        var targetBlock = $('#form-step-'+targetStep);

        removeFadeClasses(activeBlock);
        activeBlock.addClass(activeBlockEffect);
        setTimeout(function(){
            activeBlock.css('display','none');
            removeFadeClasses(targetBlock);
            targetBlock.addClass(targetBlockEffect).css('display','block');

            GroupStepForm.progressAction(GroupStepForm.stepActive,targetStep,null);
            GroupStepForm.stepActive = targetStep;

        },600);
    },

    dismissAction: function(){
        GroupStepForm.stepActive = 1;
        //form-step-progress
        $('.form-step-progress li').removeClass('active');
        $('.form-step-progress li:first-child').addClass('active');
        $('.form-step-progress button').removeClass(this.progressBgm);
        $('.form-step-progress button').each(function(){
            if($(this).attr('data-form-block') != '1' ){
                $(this).addClass('disabled');
            }
        });

        // reset the form
        $('#formSteps')[0].reset();
        // buttons choices
        $('label.fstlabel').removeAttr('style');
        //image wrap
        $('#group-image-wrap').html('');
        // hide step modals
        $('.form-inner-step, .form-first-step, .form-last-step').hide();
        //show first step
        $('.form-first-step').show();

        //map
        if(GroupStepForm.MapInit){
            if(GroupStepForm.MapMarker){
                GroupStepForm.MapMarker.setVisible(false);
            }
            if(GroupStepForm.MapInfoWindow){
                GroupStepForm.MapInfoWindow.close();
            }
            //GroupStepForm.MapInit = null;
            GroupStepForm.MapMarker = null;
            GroupStepForm.MapInfoWindow = null;
        }



    },

    submitAction: function(){
        //var _forObj = GroupStepForm;
        //var steps = _forObj.form.steps;
        //var formArr = $('#formSteps').serializeArray();
        //var collectionGoals = [];
        //var colectionInvite = [];
        //$.each(formArr, function(){
        //    var keyName = this.name.split('_');
        //    var keyNameArray = this.name.split('[');
        //    var isArray = (keyNameArray.length > 1) ? true : false;
        //    var key = (keyNameArray.length > 1) ? keyNameArray[0] : keyName[0];
        //    //console.log(keyName,keyNameArray);
        //    for(x in steps){
        //        if(steps[x].name == key){
        //            if(steps[x].type == 'choices'){
        //                var keyName = this.name.split('_');
        //                steps[x].values.push(this.value);
        //            }
        //            if(steps[x].type == 'numeric' ){
        //                steps[x].values.push(this.value);
        //            }
        //            if(steps[x].type == 'periods' ){
        //                var key = this.name.split('-'+steps[x].name+'-');
        //
        //                if(key[2] == 'first'){
        //
        //                    steps[x].values.first = this.value ;
        //
        //                }else if(key[2] == 'last'){
        //
        //                    steps[x].values.last = this.value ;
        //
        //                }else{
        //                    steps[x].values.empty = true ;
        //                }
        //
        //            }
        //            if(steps[x].type == 'collection'){
        //                var collectionData = steps[x].collectionData;
        //                var str = this.name;
        //                for(var c in collectionData ){
        //                    var expr = new RegExp('\\w{1,}\\[(\\d)\\]\\['+collectionData[c].slug+'\\]','i');
        //                    var match = str.match(expr);
        //                    if(match){
        //                        var arrIndex = parseInt(match[1]);
        //                        steps[x].values.push( {idx: arrIndex, slug: collectionData[c].slug, value:this.value} );
        //                    }
        //                }
        //            }
        //            if(steps[x].type == 'geo'){
        //                if(key == 'geo-'+steps[x].name+'-address' ){
        //                    steps[x].values.address = this.value ;
        //                }
        //                if(key == 'geo-'+steps[x].name+'-lat' ){
        //                    steps[x].values.lat = this.value ;
        //                }
        //                if(key == 'geo-'+steps[x].name+'-lng' ){
        //                    steps[x].values.lng = this.value ;
        //                }
        //            }
        //        }
        //
        //    }
        //
        //});

        //console.log(formArr);
        //console.log(steps);

        //$.ajax({
        //    url: Routing.generate('fly_platform_group_create'),
        //    type: 'POST',
        //    format: 'json',
        //    data: {steps: steps },
        //    success: function (data, textStatus, xhr) {
        //
        //        console.log(data);
        //        checkAjxLogging(xhr.status);
        //        if(data.asc == 'success'){
        //            console.log('success')
        //        }
        //        if(data.asc == 'error'){
        //            console.log('error');
        //        }
        //    }
        //});
        //
        //return false;



        // bind to the form's submit event
            // inside event callbacks 'this' is the DOM element so we first
            // wrap it in a jQuery object and then invoke ajaxSubmit
            //$('#formSteps').ajaxSubmit({
            //    success: function(data,textStatus,jqXHR){
            //        console.log('success');
            //    },
            //    error: function(jqXHR,textStatus,errorThrown){
            //        console.log('error');
            //    },
            //    complete: function(){
            //        console.log('compleat');
            //    }
            //});
            //
            //// !!! Important !!!
            //// always return false to prevent standard browser submit and page navigation
            //return false;
    },



    /**
     *
     * @returns {boolean}
     */
    validateAction: function(step){

        var form = this;

        // validate choices
        if($('#form-step-'+step).attr('data-step-type') == 'choices'){
            var maxChoice = $('#form-step-'+step).attr('data-max-choice');
            var minChoice = $('#form-step-'+step).attr('data-min-choice');
            var checkedAmount  = this.selector.find('#form-step-'+step+' .modal-body input[type="checkbox"]:checked').length;
            if(checkedAmount == 0){
                checkedAmount  = this.selector.find('#form-step-'+step+' .modal-body input[type="radio"]:checked').length;
            }
            /*check max*/
            if(checkedAmount > maxChoice){
                var err = {errKey:'maxError', value: maxChoice};
                return err;
            }

            /*check  min*/
            if(checkedAmount < minChoice || checkedAmount == 0){
                var err = {errKey:'minError', value: minChoice};
                return err;
            }
        }

        // validate first step
        if($('#form-step-'+step).attr('data-step-type') == 'groupName'){
            var fields = [
                {id:'fos_user_group_form_name',name:'Group Name'},
                {id:'fos_user_group_form_description',name:'Group Description'},
                {id:'fos_user_group_form_picture',name:'Picture'},
            ];
            for (var f in fields){

                if(!$('#'+fields[f].id).val()){
                    var err = {errKey:'requiredFields', value: fields[f].name};
                    $('label[for="'+fields[f].id+'"]').addClass('text-danger');
                    return err;

                }else{
                    // check unique group name
                    if(fields[f].name == 'Group Name'){
                        var check = form.checkGroupName($('#'+fields[f].id).val());
                        if(check !== false){
                            if(check.asc == 'error'){
                                var err = {errKey:'uniqueFields', value: check.msg};
                                $('#'+fields[f].id+'_error').removeClass('hide').html(check.msg);
                                return err;
                            }else{
                                $('#'+fields[f].id+'_error').addClass('hide').html('');
                            }

                        }
                    }

                    // check max chars for Group Description
                    if(fields[f].name == 'Group Description'){
                        //var check = form.checkGroupName($('#'+fields[f].id).val());
                        if($('#'+fields[f].id).val().length > 160){
                                var err = {errKey:'maxCharFields', value: 160 };
                                $('label[for="'+fields[f].id+'"]').addClass('text-danger');
                                return err;
                            }
                        }
                }


            }


        }

        //validate geo
        if($('#form-step-'+step).attr('data-step-type') == 'geo'){

            var dismiss = $('#fos_user_group_form_is_wheretogo').prop('checked');
            var address = $('#fos_user_group_form_geoAddress').val();
            var lat = $('#fos_user_group_form_geoLat').val();
            var lng = $('#fos_user_group_form_geoLng').val();

            console.log('validate geo: ');
            console.log('validate geo: '+dismiss);
            console.log('validate geo: '+address);
            console.log('validate geo: '+lat);
            console.log('validate geo: '+lng);

            if(!dismiss && !address){
                var err = {errKey:'geoFieldsAll', value: ''};
                return err;
            }

        }

        //validate whenToGo
        if($('#form-step-'+step).attr('data-step-type') == 'whenToGo'){

            var departure = $('#fos_user_group_form_departure_date');
            var wayback = $('#fos_user_group_form_wayback_date');
            var dismiss = $('#fos_user_group_form_is_whentogo');

            //console.log(departure);
            //console.log(wayback);
            //console.log(dismiss);

            if(!departure.val() && !wayback.val() && !dismiss.prop('checked')){
                var err = {errKey:'whenToGoFieldsAll', value: ''};
                return err;
            }

            if(!dismiss.prop('checked')){
                if(!departure.val()){
                    var err = {errKey:'whenToGoFieldsDeparture', value: ''};
                    return err;
                }
                if(!wayback.val()){
                    var err = {errKey:'whenToGoFieldsWayBack', value: ''};
                    return err;
                }
            }



        }

        return true;
    },

    /**
     *
     * @param obj
     * @returns {boolean}
     */
    validateItemsAction: function(obj){

        //var step = this.form.steps[Math.floor(this.stepActive) - 1];
        var step = obj.parent('label').attr('data-form-block');
        var maxChoice = $('#form-step-'+step).attr('data-max-choice');
        //console.log(obj.parent('label').attr('data-form-block'));
        if(obj.attr('type') == 'checkbox' || obj.attr('type') == 'radio' ){
            // count selected items
            var checkedAmount  = this.selector.find('#form-step-'+step+' .modal-body input[type="checkbox"]:checked').length;
            if(checkedAmount == 0){
                checkedAmount  = this.selector.find('#form-step-'+step+' .modal-body input[type="radio"]:checked').length;
            }
            // validate according maxChoice
            if(checkedAmount > maxChoice){
                var err = {errKey:'maxError', value: maxChoice};
                obj.prop('checked',false);
                return err;
            }

        }

        return true;

    },

    /**
     *
     * @returns {boolean}
     */
    errorsAction: function(error){
        this.errorMessagesAction(error);
        return false;

    },

    errorMessagesAction: function(error){
        var errKey = error.errKey;
        var errVal = error.value;

        var message = this.errorMessages[errKey];
        if(errVal){
            message = message.replace('%s%',errVal);
        }


        alert(message);
    },

    /*File reader api*/
    fileUpload: function(id,wrapId,evt){
        // Check for the various File API support.
        if (window.File && window.FileReader && window.FileList && window.Blob) {
            var files = evt.target.files; // FileList object
            // Only process image files.
            if (files[0].type.match('image.*')) {
                var reader = new FileReader();
                // Closure to capture the file information.
                reader.onload = (function(theFile) {
                    return function(e) {
                        // Render thumbnail.
                        var html = '<img class="img-thumbnail img-responsive"  src="'+ e.target.result +'" title="' + escape(theFile.name) + '"/>';
                        $('#'+wrapId).html(html);
                    };
                })(files[0]);

                // Read in the image file as a data URL.
                reader.readAsDataURL(files[0]);

            }

            //console.log(files);
        } else {
             alert('The File APIs are not fully supported in this browser.');
        }
    },

/************************** ACTIONS INIT ***************************/
    actionsInit: function(){
        var form = this;


        // clear label text-danger class if input or textarea focused
        $(document).delegate('input, textarea','focus',function(){
            $('label[for="'+$(this).attr('id')+'"]').removeClass('text-danger');
            $('label[for="'+$(this).attr('id')+'"]').closest('div.alert.alert-danger').addClass('hide').html('');

        });



        /*progress*/
        $(document).delegate('.form-step-progress button.disabled','click',function(){
           return false;
        });

        $(document).delegate('.form-step-progress button','click',function(){
            if($(this).hasClass('disabled')){
                return false;
            }else{
                form.progressSlideAction($(this).attr('data-form-block'))
            }

        });

        /*dismiss form*/
        $(document).delegate('.form-step-dismiss','click',function(){
            form.dismissAction();
        });



        /*submit*/
        $(document).delegate('.form-submit', 'click',function(){
            form.submitAction();
        });


        /*debug*/
        $(document).delegate('.form-debug', 'click',function(){
            form.submitAction();
        });

        /*check/uncheck the item*/
        $(document).delegate('.fstlabel input','click',function(e){

            if($(this).attr('type') == 'radio'){
                var block = $(this).closest('.form-step-data-block');
                var labels = block.find('label');
                $.each(labels, function(){
                    if($(this).find('input').prop('checked')){
                        $(this).css('background-color','#4caf50').css('color','#fff');
                    }else{
                        $(this).css('background-color','#fff').css('color','#5e5e5e');
                    }

                });
            }else{
                var valid = form.validateItemsAction($(this));
                //console.log(valid.errKey);
                if(true === valid){
                    if($(this).prop('checked')){
                        $(this).parent('label').css('background-color','#4caf50').css('color','#fff');
                    }else{
                        $(this).parent('label').css('background-color','#fff').css('color','#5e5e5e');
                    }
                }else{
                    form.errorsAction(valid);
                }
            }

        });

        /*steps*/
        $(document).delegate('.group-form-step .form-step', 'click',function(){
            form.stepAction($(this));
        });

        /*periods*/

        $('#group-whenToGo-first').datetimepicker({
            inline: true,
            format: 'YYYY-MM-DD',


        });
        $('#group-whenToGo-last').datetimepicker({
            inline: true,
            format: 'YYYY-MM-DD',
            useCurrent: false,

        });

        form.periodSetDate('whenToGo');
        /*WhenToGo dismiss*/
        $(document).delegate('#fos_user_group_form_is_whentogo','click',function(e){
            if($(this).prop('checked')){
                $('#fos_user_group_form_wayback_date').val('');
                $('#fos_user_group_form_is_whentogo').val('');
            }
        });

        $("#group-whenToGo-first").on("dp.change", function (e) {
            //console.log("DateTimePicker minDate");
            form.periodSetDate('whenToGo');
            $('#group-whenToGo-last').data("DateTimePicker").minDate(e.date);
        });
        $("#group-whenToGo-last").on("dp.change", function (e) {
            //console.log("DateTimePicker maxDate");
            form.periodSetDate('whenToGo');
            $('#group-whenToGo-first').data("DateTimePicker").maxDate(e.date);
        });

        $(document).delegate('#fly_user_group_step_is_whentogo','change',function(){
            //console.log('whenToGo-dismiss');
            if($(this).prop('checked')){
                $('#whenToGo-row').css('opacity','0');
                setTimeout(function(){
                    $('#whenToGo-row').css('display','none');
                },300);
            }else{
                $('#whenToGo-row').css('display','block');
                setTimeout(function(){
                    $('#whenToGo-row').css('opacity','1');
                },100);


            }
        })

        /*collection add item*/
        $(document).delegate('.add_collection_item','click',function(){
            var stepObj = form.form.steps[Math.floor(form.stepActive)-1];
            var data = stepObj.collectionData;

            var stepBlock = $('#'+stepObj.name);
            var dataBlock = $('<div class="row collection-item-block well"></div>');
            /*check items count*/
            if(!stepObj.collectionCount){
                stepObj.collectionCount = 0 ;
            }

            for(x=0; x<data.length; x++){
                var colWidth = (data.length == 1)?'10':Math.ceil(10/data.length);
                var cssClass = '';
                if(data[x].attr){
                    var cssClass = ( data[x].attr.class)?data[x].attr.class:"";
                }

                dataBlock.append('<div class="col-md-'+colWidth+'">' +
                    '<label>'+data[x].title+'</label>' +
                    '<input class="form-control '+cssClass+' " type="'+data[x].type+'"  id="'+stepObj.name+'_'+stepObj.collectionCount+'_'+x+'" name="'+stepObj.name+'['+stepObj.collectionCount+']['+data[x].slug+']"/>' +
                    '</div>');


            }
            dataBlock.append('<div class="col-md-2"><a href="#" class="remove-collection-item btn btn-xs btn-danger"><i class="md md-remove-circle"></i></a></div>');
            dataBlock.append('<div class="clearfix"></div>');
            stepBlock.append(dataBlock);
            stepObj.collectionCount++;

            $('.datetimesimple').datetimepicker();

        });


        /* geo dismiss*/
        $(document).delegate('#fos_user_group_form_is_wheretogo','click',function(){
            if($(this).prop('checked')){
                if(GroupStepForm.MapInit){
                    if(GroupStepForm.MapMarker){
                        GroupStepForm.MapMarker.setVisible(false);
                        GroupStepForm.MapInfoWindow.close();
                        $('#fos_user_group_form_geoAddress').val('');
                        $('#fos_user_group_form_geoLat').val('');
                        $('#fos_user_group_form_geoLng').val('');
                        $('#pac-input').val('');
                    }

                }
            }
        });

        /*collection remove item*/
        $(document).delegate('.remove-collection-item','click',function(){
            $(this).parent().parent('.collection-item-block').remove();
        });

        /*Group pictures*/
        $(document).delegate('#fos_user_group_form_picture','change',function(e){
            form.fileUpload($(this).attr('id'),'group-image-wrap',e);
        });



    },

    /****************** STEP FORM ACTIONS END *******************/


    /****************** Google Map Functional *******************/

    gmapInit: function(stepId){

        GroupStepForm.map = true;
        //console.log('start Google Map Init')
        //var MapInit = this.map;
        map = new google.maps.Map(document.getElementById('map'),{
            center: {lat: 48.841878, lng: 2.357919},
            zoom: 5
        });
        GroupStepForm.MapInit = map;
        var input = /** @type {!HTMLInputElement} */(
            document.getElementById('pac-input'));
        var types = document.getElementById('type-selector');
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(types);

        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);

        var infowindow = new google.maps.InfoWindow();
        var marker = new google.maps.Marker({
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

            $('#fos_user_group_form_geoAddress').val(place.formatted_address);
            $('#fos_user_group_form_geoLat').val(place.geometry.location.lat());
            $('#fos_user_group_form_geoLng').val(place.geometry.location.lng());
            $('#fos_user_group_form_is_wheretogo').prop('checked',false);
            $('label[for="fos_user_group_form_is_wheretogo"]').removeAttr('style');
            GroupStepForm.map = true;

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
            GroupStepForm.MapMarker = marker;

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
            GroupStepForm.MapInfoWindow = infowindow;
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



    },


    /**
     * Init form
     * @param selector
     */
    init: function(selector){
        this.selector = selector;
        //var steps = this.getFormSteps();
        //this.createSteps();

        /*step actions init */
        this.actionsInit();


    },

    getFormSteps: function(){
        var steps = [];
        $.ajax({
            url: Routing.generate('fly_platform_create_steps'),
            type: 'POST',
            format: 'json',
            //data: {steps: steps },
            success: function (data, textStatus, xhr) {

                console.log(data);
                checkAjxLogging(xhr.status);
                if(data.asc == 'success'){
                    console.log('success')
                }
                if(data.asc == 'error'){
                    console.log('error');
                }
            }
        });

        this.form.steps = steps;
    },

    periodSetDate: function(name){
        var start = $("#group-"+name+"-first").find('td.day.active');
        var startDate = start.attr('data-day');
        var end = $("#group-"+name+"-last").find('td.day.active');
        var endDate = end.attr('data-day');
        if(startDate){
            startDate = startDate.split('/');
            if(startDate.length == 3){
                $('#fos_user_group_form_departure_date').val(startDate[2]+'-'+startDate[0]+'-'+startDate[1]);
            }
        }
        if(endDate){
            endDate = endDate.split('/');
            if(endDate.length == 3){
                $('#fos_user_group_form_wayback_date').val(endDate[2]+'-'+endDate[0]+'-'+endDate[1]);
            }
        }

    },

    checkGroupName: function(name){
        var res = false;
        $.ajax({
            url: Routing.generate('fly_platform_group_check',{name:name}),
            type: 'POST',
            format: 'json',
            async: false,
            //data: {steps: steps },
            success: function (data, textStatus, xhr) {

                //console.log(data);
                checkAjxLogging(xhr.status);
                 res = data;
            },
            error: function(data,statusText, xhr){
               console.log(statusText);

            }
        });
        return res;
    }
}

function removeFadeClasses(formBlock){
    formBlock.removeClass('fadeOutLeft').removeClass('fadeOutRight').removeClass('fadeInRight').removeClass('fadeInLeft');
}
