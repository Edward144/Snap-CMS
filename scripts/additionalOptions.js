$(".additionalOptions .optionTab > h3:not(.noClick)").click(function() {
    if($(this).closest(".optionTab").attr("id") == "active") {
        $(this).closest(".optionTab").attr("id", "");
    }
    else {
        $(".additionalOptions .optionTab").attr("id", "");
        $(this).closest(".optionTab").attr("id", "active");
    }
});