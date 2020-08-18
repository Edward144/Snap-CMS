//Apply Filters
$("#filterSupport").submit(function() {
    event.preventDefault();
    
    var queryString = "?filter=";
    var i = 0;
    
    $("#filterSupport input[type='checkbox']").each(function() {
        if($(this).is(":checked")) {
            queryString += $(this).attr("name").toLowerCase() + "+";
            i++;
        }
    });
    
    if(i > 0) {
        location.href = window.location.href.split("?")[0] + queryString.slice(0, -1);
    }
});

//Clear Filters
$("#filterSupport input[name='clearFilter']").click(function() {
    location.href = window.location.href.split("?")[0];
});

//View Request
$("#supportList input[name='view']").click(function() {
    var requestId = $(this).attr("data-request-id");
    
    window.location.href = window.location.href.split("?")[0] + "?id=" + requestId;
});

//Return to List
$("input[name='return']").click(function() {
    window.location.href = window.location.href.split("?")[0];
});

//Close Request
$("input[name='close']").click(function() {
    if(!confirm("Are you sure you want to close this request?")) {
        event.preventDefault();
        $(this).siblings(".spinner-border").remove();
        $(this).prop("disabled", false);
    }
    else {
        $("<div class='spinner-border ml-1'><span class='sr-only'>Processing...</span></div>").insertAfter(this).parent(".form-group")
        $(this).prop("disabled", true);
        $(this).parents("form").first().submit();
    }
})