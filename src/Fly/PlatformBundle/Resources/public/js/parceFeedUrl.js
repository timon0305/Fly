/**
 * Created by swaroge on 13.07.15.
 */


var FeedContent = {
    url: '',
    type: '',
    group: GroupName,
    feedContent: '',
    feedCategory: null,
    feedResources: [],
    addResource: function(obj){
        this.feedResources.push(obj);
    },
    setGroup: function(group){
        this.group = group;
    },
    getGroup: function(){
        return this.group;
    },
    mainTemplate: function(url,data){
        var html ='<div class="col-md-12  mt20 mb20">';
        if(data.image){
            html += '<a href="'+data.resourceUrl+'" target="_blank" ><img src="'+data.image+'" class="img-responsive" /></a>';
        }
        html += '</div>';
        html +='<div class="col-md-12">';
        if(data.title) {
            html += '<h4><a href="' + data.resourceUrl + '" target="_blank" >' + data.title + '</a></h4>';
        }
        if(data.description) {
            html += '<p>' + data.description + '</p>';
        }
        html += '</div>';
        html += '<div class="clearfix"></div>';
        return html;
    },
    imageTemplate: function(url){
        var html ='<div class="col-md-12  mt20 mb20">';
        html += '<a href="' + url + '" target="_blank" ><img src="'+url+'" class="img-responsive" /></a>';
        html += '</div>';
        html += '<div class="clearfix"></div>';
        return html;
    },
    start: function(url){
        this.url = url;
        this.parseContent(url);
    },
    parseContent: function(url){

        var added = this.isLinkAdded(url);
        //console.log(added);
        if(added){
            return false;
        }else{

        }

        //overlayPage('yes');
        $('#feed-urls-block').append('<div class="link-block col-md-3 mb20" data-url="'+url+'"><div class="link-action"><a class="remove-link-block"><i class="md  md-clear"></i></a></div><div class="feed-content-box"><div class="loader-box"><img src="/img/ring-alt.svg"></div></div></div>');
        $.ajax({
            url: Routing.generate('fly_platform_geturlcontent'),
            type: 'POST',
            format: 'json',
            asinc: false,
            data: {url: url},
            success: function (data) {
                FeedContent.addLink(url,data.data, data.type);
                FeedContent.addResource(data.data);
                console.log(data.data);
                //overlayPage('no');
                //setTimeout(function(){},2000);
            }
        });
    },
    getFeedUrls: function(text){
        var expression = /[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/gi;
        var regex = new RegExp(expression);
        var urls = text.match(regex);
        if(urls && urls.length > 3){

            //this.showAlert('Cant add more then 3 links','warning');
            return false;
        }
        return urls;
    },
    addLink: function(url,data, type){
        //var template = this.createTemplate(type, url, data);
        var template = this.mainTemplate(url, data);

        $('#feed-urls-block .link-block').each(function(){
           if($(this).attr('data-url') == url){
               $(this).remove('.loader-box');
               $(this).find('div.feed-content-box').html(template);
           }
        });
        //$('#feed-urls-block').append(template);
    },
    isLinkAdded: function(url){
        var exist = false;
        $.each($('.link-block'), function(){
            console.log( $(this).attr('data-url') == url );
            if($(this).attr('data-url') == url){
                exist = true;
            }
        });
        return exist;
    },
    removeLink: function(obj){

            var newObj = [];
            for(x in FeedContent.feedResources){
                if(obj.attr('data-url') != FeedContent.feedResources[x].resourceUrl){
                    newObj.push(FeedContent.feedResources[x]);
                }
            }
            FeedContent.feedResources = newObj;
        obj.remove();
    },
    postFeed: function(){
        if(!GroupName ){
            return false;
        }

        if(!FeedContent.feedContent && FeedContent.feedResources.length == 0){
            FeedContent.showAlert("Please fill the form", 'danger');
            return false;
        }

        //overlayPage('yes');
        $.ajax({
            url: Routing.generate('group_feed_ajx_post',{'groupName':GroupName}),
            type: 'POST',
            format: 'json',
            data: {feedCategory: FeedContent.feedCategory ,feedContent: FeedContent.feedContent,  feedResources: FeedContent.feedResources},
            success: function (data, textStatus, xhr) {
               checkAjxLogging(xhr.status);
                if(data.asc == 'success'){
                    FeedContent.showAlert(data.msg, 'success');
                    window.location.href = Routing.generate('fos_user_group_show',{'groupName':GroupName})
                    //setTimeout(function(){
                    //    window.location.href = Routing.generate('fos_user_group_show',{'groupName':GroupName})
                    //},1000);
                }
                if(data.asc == 'error'){
                    FeedContent.showAlert(data.msg, 'danger');
                    //overlayPage('no');
                }


            }
        });
    },
    showAlert: function(msg,type){
        $.growl(msg, {
//                ele: 'body', // which element to append to
            type: type, // (null, 'info', 'danger', 'success')
            offset: 70, // 'top', or 'bottom'
            align: 'right', // ('left', 'right', or 'center')
            width: 'auto', // (integer, or 'auto')
//                delay: 4000, // Time while the message will be displayed. It's not equivalent to the *demo* timeOut!
//                allow_dismiss: true, // If true then will display a cross to close the popup.
//                stackup_spacing: 10 // spacing between consecutively stacked growls.);
            animate: {
                enter: 'animated fadeInRight',
                exit: 'animated fadeOutRight'
            }
        });
    }

}

$(function(){

    $('textarea.feed-content').on('paste, keyup',function(e){
        //e.preventDefault();
        var x = e.keyCode;
        if(x == 45 || x == 17  ){
            return;
        }

        FeedContent.feedContent = $(this).val();
        var urls = FeedContent.getFeedUrls($(this).val());
        if(urls){
            for(x in urls){
               FeedContent.start(urls[x]);
            }
        }
    });
    $('select.feed-category').on('change',function(e){
        FeedContent.feedCategory = $(this).val();
    });

    $('#post-feed').click(function(){
        console.log(FeedContent.feedContent, FeedContent.feedResources, GroupName);
        FeedContent.postFeed();
    });

    $(document).delegate('.remove-link-block','click', function(){
        //console.log($(this).closest('div.link-block').attr('data-url'));
        FeedContent.removeLink($(this).closest('div.link-block'));
    })

});


