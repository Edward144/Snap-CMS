$("#generalSettings input[type='submit']").click(function() {
    event.preventDefault();
    
    var homepage = $("#generalSettings select[name='homepage']").val();
    
    if($("#generalSettings input[name='hidePosts']").is(":checked")) {
        var hidePosts = 1;
    }
    else {
        var hidePosts = 0;
    }
    
    $.ajax({
        url: "scripts/generalSettings.php",
        method: "POST",
        dataType: "json",
        data: ({homepage, hidePosts}),
        success: function(data) {
            $("#generalSettings .message").text(data);
        }
    });
});