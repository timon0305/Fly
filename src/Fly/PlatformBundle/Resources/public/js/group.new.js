$(function(){
    datePickerInit();
    $('.remove-goal').click(function (e) {
        e.preventDefault();
        $(this).parent().remove();
        return false;
    });
});
// setup an "add a goal" link
var $addGoalLink = $('<a href="#" class="add_group_goal btn btn-info"><i class="fa fa-plus-circle"></i> Add Goal</a>');
var $newGoalLinkLi = $('<li style="clear:both;"></li>').append($addGoalLink);

// setup an "add a tag" link
var $addTagLink = $('<a href="#" class="add_invitations_link btn btn-info"><i class="fa fa-plus-circle"></i> Add Email</a>');
var $newLinkLi = $('<li></li>').append($addTagLink);

jQuery(document).ready(function () {
    // Get the ul that holds the collection of tags
    var $collectionHolderGoals = $('ul.group_goals');
    // add the "add a tag" anchor and li to the tags ul
    $collectionHolderGoals.append($newGoalLinkLi);
    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolderGoals.data('index', $collectionHolderGoals.find(':input').length);
    $addGoalLink.on('click', function (e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();
        // add a new tag form (see code block below)
        addGoalForm($collectionHolderGoals, $newGoalLinkLi);
    });

    // Get the ul that holds the collection of tags
    var $collectionHolder = $('ul.invitations');
    // add the "add a tag" anchor and li to the tags ul
    $collectionHolder.append($newLinkLi);
    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);
    $addTagLink.on('click', function (e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();
        // add a new tag form (see code block below)
        addTagForm($collectionHolder, $newLinkLi);
    });

});

function addGoalForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');
    // get the new index
    var index = $collectionHolder.data('index');
    // Replace '$$name$$' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm = prototype.replace(/__name__/g, index);
    ;
    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);
    // Display the form in the page in an li, before the "Add a tag" link li
    var $newFormLi = $('<li class="goal-form-row"></li>').append(prepareNewForm($(newForm)));
    // also add a remove button, just for this example
    //$newFormLi.append('<a href="#" class="remove-goal btn btn-xs btn-danger"><i class="md md-remove-circle"></i></a>');
    $newLinkLi.before($newFormLi);
    datePickerInit();
    // handle the removal, just for this example
    $('.remove-goal').click(function (e) {
        e.preventDefault();
        $(this).closest('li').remove();
        return false;
    });
}

function addTagForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');
    // get the new index
    var index = $collectionHolder.data('index');
    // Replace '$$name$$' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm = prototype.replace(/__name__/g, index);
    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);
    // Display the form in the page in an li, before the "Add a tag" link li
    var $newFormLi = $('<li></li>').append(newForm);
    // also add a remove button, just for this example
    $newFormLi.append('<a href="#" class="remove-invitations btn btn-xs btn-danger"><i class="md md-remove-circle"></i></a>');
    $newLinkLi.before($newFormLi);
    datePickerInit();
    // handle the removal, just for this example
    $('.remove-invitations').click(function (e) {
        e.preventDefault();
        $(this).closest('li').remove();
        return false;
    });
}

//function datePickerInit(){
//    $('.goal-date-datepicker').datepicker({format: 'yyyy-mm-dd'});
//    //$('.goal-date-datepicker-val').datepicker({format: 'yyyy-mm-dd'});
//    $('.goal-date-datepicker').on("changeDate", function (event) {
//        //alert($(this).datepicker('getFormattedDate'));
//        $(this).next().val(
//            $(this).datepicker('getFormattedDate')
//        )
//    });
//}

function datePickerInit(){
    $('.goal-date-datepicker-val').datetimepicker({format: 'YYYY-MM-DD HH:mm'});
}

function prepareNewForm(content){
    console.log(content);
    var newContent = $('<div></div>');
    //content.find('div.form-group .goal-date-datepicker-val').addClass('hide');
    //content.find('div.form-group .goal-date-datepicker-val').before('<div class="goal-date-datepicker"></div>');
    content.find('div.form-group').each(function(){
        newContent.append( $('<div class="col-md-5"></div>').append($(this)));
    });
    newContent.append('<div class="col-md-2"><a href="#" class="remove-goal btn btn-xs btn-danger"><i class="md md-remove-circle"></i></a></div>')
    return newContent;
}