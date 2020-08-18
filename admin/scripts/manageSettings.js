//Add Spinners to Submit Buttons
$("form").submit(function() {
    $("<div class='spinner-border ml-1'><span class='sr-only'>Processing...</span></div>").insertAfter($(this).find(":submit"));
    $(this).find(":submit").prop("disabled", true);
});

//Confirm Post Type Deletion
$("#postTypes").submit(function() {
    if(!confirm("Are you sure you want to delete this post type? All pages associated will also be deleted.")) {
        event.preventDefault();
        $(this).find(":submit").prop("disabled", false);
        $(this).find(".spinner-border").remove();
    }
});