
var FeedComment = {
    id : 0,
    feedId : 0,
    text : '',
    commentBlock: null,
    user: null,
    created: null,
    params: function(){
        return {id: this.id, feedId: this.feedId, text: this.text}
    },
    addContent : function(){
        //console.log('add');
        this.commentBlock.children('li').last().before(this.template());
    },
    updateContent: function(){

    },
    create: function(){
        var comment = this;
        $.ajax({
            url: Routing.generate('feedcomment_ajx_create'),
            type: 'POST',
            format: 'json',
            data: {params: comment.params(), action: 'create' },
            success: function (data, textStatus, xhr) {
                checkAjxLogging(xhr.status);
                comment.created = data.comment.date;
                comment.user = data.comment.user;
                comment.id = data.comment.id;
                comment.text = data.comment.content;
                comment.addContent();
                comment.emptyTextarea(data.comment.feed.id);
                //console.log(data);
                //console.log(comment.params());
            },
            error: function(jqXHR,textStatus,errorThrown){
                showAjxActionAlerts(500);
            }
        });

    },
    edit: function(id){

    },
    update: function(){
        var comment = this;
        //$.ajax({
        //    url: Routing.generate('feedcomment_ajx_create'),
        //    type: 'POST',
        //    format: 'json',
        //    data: {params: comment.params(), action: 'create' },
        //    success: function (data, textStatus, xhr) {
        //        checkAjxLogging(xhr.status);
        //        console.log(data);
        //        //console.log(comment.params());
        //    },
        //    error: function(jqXHR,textStatus,errorThrown){
        //        showAjxActionAlerts(500);
        //    }
        //});
        //console.log(this);
    },
    delete: function(id){
        var comment = this;
        if(!this.id){
            return false;
        }
        $.ajax({
            url: Routing.generate('feedcomment_ajx_delete'),
            type: 'DELETE',
            format: 'json',
            data: {params: comment.params(), action: 'delete' },
            success: function (data, textStatus, xhr) {
                checkAjxLogging(xhr.status);
                if(data.asc=='success'){
                    $('#comment-'+data.id).addClass('fadeOut').remove();
                }
                //comment.created = data.comment.date;
                //comment.user = data.comment.user;
                //comment.id = data.comment.id;
                //comment.text = data.comment.content;
                //comment.addContent();
                //comment.emptyTextarea(data.comment.feed.id);
                console.log(data);
                //console.log(comment.params());
            },
            error: function(jqXHR,textStatus,errorThrown){
                showAjxActionAlerts(500);
            }
        });
    },
    template: function(){
        var userPhoto = '';
        //console.log(this.user.photo_fb);
        var wrap = $('<li class="media animated fadeIn" style="display:block;" id="comment-'+this.id+'"></li>');
        var user = $('<a href="" class="tvh-user pull-left"><img class="img-responsive" src="'+this.user.photo_sm+'" alt=""></a>');
        var body = $('<div class="media-body"></div>');

        var editBtn = $('<ul class="actions">' +
            '<li>' +
            '<div class="btn-group">' +
            '<button class="btn btn-default btn-icon waves-effect waves-button waves-float" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
            '<i class="md md-more-vert"></i>' +
            '</button>' +
            '<ul class="dropdown-menu dropdown-menu-right">' +
            '<li>' +
            '<a href="#" class="comment-remove-action" data-comment="'+this.id+'" ><i class="md md-close"></i> Delete</a>' +
            '</li>' +
            ' </ul>' +
            '</div>' +
            '</li>' +
            '</ul>');
        var editBlock = $('<div class="m-t-10 comment-edit" ></div>');
        editBlock.append('<textarea  class="form-control auto-size comment-new-text" placeholder="Write a comment..." data-autosize-on="true" style="overflow: hidden; word-wrap: break-word; height: 43px;">'+this.text+'</textarea>');
        editBlock.append('<button data-comment="'+this.id+'" class="m-t-15 btn btn-sm btn-success btn-sm waves-effect comment-udate">Save</button>');

        body.append(editBtn);
        body.append('<strong class="d-block comment-user">'+this.user.name+'</strong>');
        body.append('<small class="c-gray comment-date">'+this.created+'</small>');
        body.append('<div class="m-t-10 comment-text">'+this.text+'</div>');
        body.append(editBlock);

        wrap.append(user).append(body);
        return wrap;
    },
    emptyTextarea: function(id){
        $('textarea[data-feed="'+id+'"]').val('');
    }
};


$(function(){
    $(document).delegate('.submit-comment','click',function(){
        var comment = Object.create(FeedComment);

        comment.feedId = $(this).attr('data-feed');
        comment.commentBlock = $('.comment-block-'+comment.feedId);
        comment.text = $(this).parent().find('.comment-post-text').val();
        comment.create();
        //$(this).parent().find('.comment-post-text').val('');


    });

    //edit show
    $(document).delegate('.comment-edit-action','click',function(){
        $(this).parent().find('.comment-edit').toggle();
        $(this).parent().find('.comment-text').toggle();
        return false;
    });

    //edit save
    $(document).delegate('.comment-udate','click',function(){
        var comment = Object.create(FeedComment);
        comment.id = $(this).attr('data-comment');
        //feed.commentBlock = $('.comment-block-'+feed.feedId);
        comment.text = $(this).parent().find('.comment-new-text').val();
        comment.update();

        $(this).parent().toggle();
        $(this).closest('div.media-body').find('.comment-text').toggle();
    });




    // show all comments
    $(document).delegate('.tvc-more','click',function(){

        var feedId = $(this).attr('data-feed');

        if($(this).hasClass('all')){

            var cnt =  $('.comment-block-'+feedId+' li').length;
            $('.comment-block-'+feedId+' li').each(function(){
                var block = $(this);
                cnt--;
                if(cnt > 3){
                    if (block.css('display') == 'block'){
                        block.addClass('animated').addClass('fadeOut').removeClass('fadeIn');

                        setTimeout(function(){
                            block.css('display','none');
                        },500);
                    }
                }
            });

            $(this).removeClass('all');
            $(this).find('.less-comments').toggle();
            $(this).find('.more-comments').toggle();


        }else{

            $('.comment-block-'+feedId+' li').each(function(){
                if ($(this).css('display') != 'block'){
                    $(this).addClass('animated').addClass('fadeIn').removeClass('fadeOut').css('display','block');
                }
            });
            $(this).addClass('all');
            $(this).find('.less-comments').toggle();
            $(this).find('.more-comments').toggle();

        }


        return false;
    });

    //remove comment

    $(document).delegate('.comment-remove-action','click',function(e){
        e.preventDefault();
        //alert('ok'); return false;
        var comment = Object.create(FeedComment);
        comment.id = $(this).attr('data-comment');
        comment.delete();
        return false;
    });
});
