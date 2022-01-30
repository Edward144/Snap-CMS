$("#userManagement input[type='submit']").click(function() {
    event.preventDefault();
    
    var row = $(this).closest(".userRow");
    
    var username = $.trim(row.find("td:first-child").text());
    var email = row.find("input[name='email']").val();
    var firstName = row.find("input[name='firstName']").val();
    var lastName = row.find("input[name='lastName']").val();
    var access = Math.round(row.find("input[name='accessLevel']").val());
    var cPass = row.find("input[name='cPassword']").val();
    var nPass = row.find("input[name='nPassword']").val();
    var confNPass = row.find("input[name='nPasswordConf']").val();
    
    if(access < 0 || access > 100 || access == "") {
        $("#userManagement .message").text("Access level for " + username + " must be between 0 - 100.");
        
        return;
    }
    
    if(cPass != "" || nPass != "" || confNPass != "") {
        if(nPass.length < 8) {
           $("#userManagement .message").text("New password for " + username + " must be at least 8 characters.");
            
            return;
        }
        
        if(nPass != confNPass) {
            $("#userManagement .message").text("New password for " + username + " does not match.");
            
            return;
        }
    }
    
    $.ajax({
        url: "scripts/updateUser.php",
        method: "POST",
        dataType: "json",
        data: ({username, email, firstName, lastName, access, cPass, nPass}),
        success: function(data) {
            $("#userManagement .message").text(data);
        }
    });
});

$("#userManagement input[name='deleteUser']").click(function() {
    var row = $(this).closest(".userRow");
    var username = $.trim(row.find("td:first-child").text());
    
    if(confirm("Are you sure you want to delete user " + username + "?")) {
        $.ajax({
            url: "scripts/deleteUser.php",
            method: "POST",
            dataType: "json",
            data: ({username}),
            success: function(data) {
                if(data[0] == 1) {
                    row.remove();
                    
                    $("#userManagement .message").text(data[1]);
                }
                else {
                    $("#userManagement .message").text(data[1]);
                }
            }
        });
    }
});

$("#addUser input[type='submit']").click(function() {
    event.preventDefault();
    
    var username = $("#addUser input[name='username']").val();
    var email = $("#addUser input[name='email']").val();
    var firstName = $("#addUser input[name='firstName']").val();
    var lastName = $("#addUser input[name='lastName']").val();
    var access = $("#addUser input[name='access']").val();
    var password = $("#addUser input[name='password']").val();
    var passwordConf = $("#addUser input[name='passwordConf']").val();
    
    if(username == "") {
        $("#addUser .message").text("Username is missing.");
        
        return;
    }
    
    if(access < 0 || access > 100 || access == "") {
        $("#addUser .message").text("Access level must be between 0 - 100.");
        
        return;
    }
    
    if(password == "") {
        $("#addUser .message").text("Password is missing.");
        
        return;
    }
    else if(password.length < 8) {
        $("#addUser .message").text("Password must be at least 8 characters.");
        
        return;
    }
    
    if(password != passwordConf) {
        $("#addUser .message").text("Passwords do not match.");
        
        return;
    }
    
    $.ajax({
        url: "scripts/addUser.php",
        method: "POST",
        dataType: "json",
        data: ({username, email, firstName, lastName, access, password}),
        success: function(data) {
            if(data[0] == 1) {
                $("#addUser .message").text(data[1]);
                
                location.reload();
            }
            else {
                $("#addUser .message").text(data[1]);
            }
        }
    });
    
    $("#addUser .message").text("good");
});