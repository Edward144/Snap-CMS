$("#login input[type='submit']").click(function() {
    event.preventDefault();
    
    var user = $("#login input[name='username']").val();
    var pass = $("#login input[name='password']").val();
    
    if(user == "") {
        $("#login .message").text("Username is missing.");
        
        return;
    }
    
    if(pass == "") {
        $("#login .message").text("Password is missing.");
        
        return;
    }
    
    $.ajax({
        url: "/scripts/login.php",
        method: "POST",
        dataType: "json",
        data: ({user, pass}),
        success: function(data) {            
            $("#login .message").text(data[1]);
            
            if(data[0] == 1) {
                location.reload();
            }
        }
    });
});

$("#reset input[type='submit']").click(function() {
    event.preventDefault();
    
    var email = $("#reset input[name='email']").val();
    
    if(email == "") {
        $("#reset .message").text("Email is missing.");
        
        return;
    }
    
    if(email.indexOf("@") < 0) {
        $("#reset .message").text("Email is invalid.");
        
        return;
    }
    
    $.ajax({
        url: "/scripts/sendResetLink.php",
        method: "POST",
        dataType: "json",
        data: ({email}),
        success: function(data) {            
            $("#reset .message").text(data);
        }
    });
});

$("#newPassword input[type='submit']").click(function() {
    event.preventDefault();
    
    var pass = $("#newPassword input[name='password']").val();
    var passConf = $("#newPassword input[name='passwordConf']").val();
    var email = $("#newPassword input[name='email']").val();
        
    if(pass == "") {
        $("#newPassword .message").text("Password is missing.");
        
        return;
    }
    
    if(pass != passConf) {
        $("#newPassword .message").text("Passwords do not match.");
        
        return
    }
    
    $.ajax({
        url: "/scripts/resetPassword.php",
        method: "POST",
        dataType: "json",
        data: ({pass, email}),
        success: function(data) {            
            $("#newPassword .message").text(data);
            $("#newPassword .message").after("<a href='/login'>Login</a>");
        }
    });
});