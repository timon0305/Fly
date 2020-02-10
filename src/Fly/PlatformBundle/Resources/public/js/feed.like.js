
var FeedLike = {
    feedId : 0,
    liked: 0,
    proccess : function(){
        var like = this;
        $.ajax({
            url: Routing.generate('feedlike_ajx'),
            type: 'POST',
            format: 'json',
            data: {feedId: like.feedId, action: 'like' },
            success: function (data, textStatus, xhr) {
                checkAjxLogging(xhr.status);
                if(data.asc == 'success'){
                    $('#feed-like-'+data.feedId).html(data.stat);
                    if(data.action == 'like'){
                        $('#feed-like-'+data.feedId).parent().append('<span class="feed-liked"> - You like it</span> ');
                    }else{
                        $('#feed-like-'+data.feedId).parent().find('.feed-liked').remove();
                    }
                }
                console.log(data);
                //console.log(comment.params());
            },
            error: function(jqXHR,textStatus,errorThrown){
                showAjxActionAlerts(500);
            }
        });
    }
}

$(function(){

    $(document).delegate('.tvbs-likes','click',function(){
       var like =  Object.create(FeedLike) ;
       var feedId = $(this).attr('data-feed');

       like.feedId = feedId;
       like.proccess();
    });

});