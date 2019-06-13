$("#setup input[type='submit']").click(function() {
    event.preventDefault();
    
    var host = $("#setup input[name='hostname']").val();
    var database = $("#setup input[name='database']").val();
    var user = $("#setup input[name='username']").val();
    var pass = $("#setup input[name='password']").val();
    
    if(host == "") {
        $("#setup .message").text("Hostname is missing.");
        
        return;
    }
    
    if(database == "") {
        $("#setup .message").text("Database name is missing.");
        
        return;
    }
    
    if(user == "") {
        $("#setup .message").text("Username is missing.");
        
        return;
    }
    
    if(pass == "" && !confirm("Password is missing. Confirm this is correct.")) {
        return;
    }
    
    $.ajax({
        url: "/setup/setup.php",
        method: "POST",
        dataType: "json",
        data: ({host, database, user, pass}),
        success: function(data) {
            $("#setup .message").html(data);
        }
    });
});