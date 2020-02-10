/**
 * Created by swaroge on 13.07.15.
 */

var GroupFeeds = {
    group : null,
    lastId : 0,
    lastTime: 0,
    refresh: 0,
    getFeeds : function(){
        GroupFeeds.Loader('show');
        //return;
        $.ajax({
            url: Routing.generate('group_feed_ajx_get',{'groupName':GroupFeeds.group}),
            type: 'POST',
            format: 'json',
            data: {lastId: GroupFeeds.lastId, refresh: GroupFeeds.refresh, lastTime: GroupFeeds.lastTime },
            success: function (data, textStatus, xhr) {
                /*
                    data = {
                        asc:success|error,
                        params: {lastId:int, html: string},
                        msg:null|string,
                          }
                */
                //console.log(data);
                checkAjxLogging(xhr.status);
                if(data.asc == 'success'){
                    GroupFeeds.Loader('hide');
                    GroupFeeds.lastTime = data.params.lastTime;
                    if(GroupFeeds.refresh){
                        $('#feeds-holder').prepend(data.params.html);
                    }else{
                        $('#feeds-holder').append(data.params.html);
                    }
                    GroupFeeds.setGreed();
                    GroupFeeds.setSwLightbox();
                    GroupFeeds.setSwComments();
                    if(GroupFeeds.lastId != data.params.lastId && !GroupFeeds.refresh ){
                        GroupFeeds.lastId = data.params.lastId;
                        //$('#feeds-holder').append(GroupFeeds.LoadMoreBtn());
                    }


                }
                if(data.asc == 'error'){
                    GroupFeeds.Loader('hide');
                }


            }
        });
    },
    showFeeds : function(){

    },
    Loader: function(status){
        if(status == 'show'){
            //$('.refreshFeeds i').addClass('feedLoaderRotate');
            //$('#feeds-holder').append(this.LoaderTemplate());
            //$('.load-more').remove();
        }else{
            //$('.refreshFeeds i').removeClass('feedLoaderRotate');
            //$('#feed-loader').remove();
        }
    },
    LoaderTemplate: function(){
        return '<div id="feed-loader"><img src="/img/graybarloader.svg"  /></div>'
    },
    LoadMoreBtn: function(){
        return '<div class="load-more"><button href=""  class="btn btn-default btn-icon  refreshFeeds"> <i class="md md-sync"></i></button></div>';
    },
    setGreed: function(){
        var $grid = $('.grid').imagesLoaded( function() {
            // init Masonry after all images have loaded
            $grid.masonry({
                itemSelector: '.grid-item',
                percentPosition: true
            });
        });
    },
    setLightbox: function(){
        $(document).delegate('*[data-toggle="lightbox"]', 'click', function(event) {
            event.preventDefault();
            $(this).ekkoLightbox();
        });
    },
    setSwLightbox: function(){
        $(document).delegate('.swlightbox', 'click', function(event) {
            event.preventDefault();
            var params = {};
            if($(this).attr('href')){
                params.url = $(this).attr('href');
            }
            if($(this).attr('data-type')){
                params.type = $(this).attr('data-type');
            }

            if($(this).attr('data-embed')){
                params.embed = $(this).attr('data-embed');
            }

            $(this).swlightbox(params);
        });
    },
    setSwComments: function() {

    }


}

$(function(){
    $(document).delegate('.refreshFeeds','click',function(){
       // $(this).find('i').addClass('feedLoaderRotate');
        GroupFeeds.refresh = $(this).attr('data-refresh');
        GroupFeeds.getFeeds();
        return false;
    });
})

