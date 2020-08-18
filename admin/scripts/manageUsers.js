//Show Current User Edit Form
function showEdit(userId) {
    $(".modal, .modal-backdrop, .card .alert").remove();

    $.ajax({
        url: root_dir + "admin/scripts/manageUsers.php",
        method: "POST",
        dataType: "json",
        data: ({userId, method: "pullUser"}),
        success: function(data) {
            if(data[0] == 1) {
                var user = data[1];

                $("body").append(
                    `<div class="modal fade show" tab-index="-1" role="dialog" style="pointer-events: none;">
                        <div class="modal-dialog modal-dialog-centered" style="pointer-events: none;">
                            <div class="modal-content" style="pointer-events: auto;">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit ` + user['first_name'] + ` ` + user['last_name'] + `</h5>

                                    <button type="button" class="close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <form id="editUser">
                                        <input type="hidden" name="userId" value="` + user['id'] + `">

                                        <div class="form-group">
                                            <label for="firstName">First Name</label>
                                            <input type="text" class="form-control" name="firstName" value="` + user['first_name'] + `" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="lastName">Last Name</label>
                                            <input type="text" class="form-control" name="lastName" value="` + user['last_name'] + `">
                                        </div>

                                        <div class="form-group">
                                            <label for="email">Email Address</label>
                                            <input type="text" class="form-control" name="email" value="` + user['email'] + `" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="username">Username</label>
                                            <input type="text" class="form-control" name="username" value="` + user['username'] + `" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input type="password" class="form-control" name="password">
                                            <small class="text-muted">Leave blank if you do not want to change the password</small>
                                        </div>

                                        <div class="form-group">
                                            <label for="passwordConf">Confirm Password</label>
                                            <input type="password" class="form-control" name="passwordConf">
                                            <small class="text-muted">Leave blank if you do not want to change the password</small>
                                        </div>

                                        <div class="form-group d-flex align-items-center">
                                            <input type="submit" class="btn btn-primary" value="Save Changes">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-backdrop fade show"></div>`
                );

                $(".modal").show();
                $(".modal input").first().focus();    
            }
            else {
                $(".card#" + userId).children(".modal-body").prepend("<div class='alert alert-danger'>Could not locate user to edit</div>");
            }
        }
    });
}

//Hide Current User Edit Form
$("body").on("click", ".modal-backdrop, .modal .close", function() {
    $(".modal, .modal-backdrop").animate({
        "opacity": 0
    }, 500, function() {
        $(".modal, .modal-backdrop").remove();
        window.location.reload();
    });
});

//Generate User Password
function randomString(length, chars) {
    var result = '';
    for (var i = length; i > 0; --i) result += chars[Math.floor(Math.random() * chars.length)];
    return result;
}

$("#createUser input[name='generatePassword']").click(function() {
    var rString = randomString(12, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');

    $("#createUser input[name='password'], #createUser input[name='passwordConf']").val(rString);

    $(this).parents("form").first().siblings(".alert").remove();

    $("<div class='alert alert-info'>Generated password: <span class='user-select-all'>" + rString + "</span></div>").insertAfter("#createUser");
});

//Validate Create User Form
$("#createUser").submit(function() {
    var valid = true;
    var email = $("#createUser").find("input[name='email']");
    var password = $("#createUser").find("input[name='password']");
    var passwordConf = $("#createUser").find("input[name='passwordConf']");

    $(".is-invalid").removeClass("is-invalid");
    $(".invalid-feedback").remove();

    //Validate email
    if(email.val().split("@")[1].indexOf(".") < 0 || email.val().split("@")[1].split(".")[1].length <= 0) {
        email.addClass("is-invalid");
        $("<div class='invalid-feedback'>Email does not appear to include a domain extension</div>").insertAfter(email);
        valid = false;
    }

    //Validate password
    if(password.val().length < 8) {
        password.addClass("is-invalid");
        $("<div class='invalid-feedback'>Password must be at least 8 characters</div>").insertAfter(password);
        valid = false;
    }
    else if(!/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])/.test(password.val())) {
        password.addClass("is-invalid");
        $("<div class='invalid-feedback'>Password must contain at least one upper, one lower, and one digit</div>").insertAfter(password);
        valid = false;
    }
    else if(password.val() != passwordConf.val()) {
        passwordConf.addClass("is-invalid");
        $("<div class='invalid-feedback'>Passwords do not match</div>").insertAfter(passwordConf);
        valid = false;
    }

    if(valid == true) {
        $(this).find(":submit, input[name='generatePassword']").prop("disabled", true);
        $("<div class='spinner-border ml-1'><span class='sr-only'>Logging you in...</span></div>").insertAfter($(this).find(":submit"));
    }
    else {
        event.preventDefault();
    }
});

//Validate Edit User Form
$("body").on("submit", "#editUser", function() {
    event.preventDefault();
    
    $(this).siblings(".alert").remove();
    $(this).find(".is-invalid").removeClass("is-invalid");
    $(this).find(".invalid-feedback").remove();
    
    var valid = true;
    var email = $(this).find("input[name='email']");
    var password = $(this).find("input[name='password']");
    var passwordConf = $(this).find("input[name='passwordConf']");
    
    //Validate email
    if(email.val().split("@")[1].indexOf(".") < 0 || email.val().split("@")[1].split(".")[1].length <= 0) {
        email.addClass("is-invalid");
        $("<div class='invalid-feedback'>Email does not appear to include a domain extension</div>").insertAfter(email);
        valid = false;
    }

    //Validate password
    if(password.val() != '' || passwordConf.val() != '') {
        if(password.val().length < 8) {
            password.addClass("is-invalid");
            $("<div class='invalid-feedback'>Password must be at least 8 characters</div>").insertAfter(password);
            valid = false;
        }
        else if(!/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])/.test(password.val())) {
            password.addClass("is-invalid");
            $("<div class='invalid-feedback'>Password must contain at least one upper, one lower, and one digit</div>").insertAfter(password);
            valid = false;
        }
        else if(password.val() != passwordConf.val()) {
            passwordConf.addClass("is-invalid");
            $("<div class='invalid-feedback'>Passwords do not match</div>").insertAfter(passwordConf);
            valid = false;
        }
    }
    
    if(valid == true) {
        var formData = $(this).serialize();
        $(this).find(":submit, input[name='generatePassword']").prop("disabled", true);
        $("<div class='spinner-border ml-1'><span class='sr-only'>Logging you in...</span></div>").insertAfter($(this).find(":submit"));
        
        $.ajax({
            url: root_dir + "admin/scripts/manageUsers.php",
            method: "POST",
            dataType: "json",
            data: ({formData, method: "editUser"}),
            success: function(data) {
                if(data[0] == 1) {
                    $("<div class='alert alert-success'>" + data[1] + "</div>").insertAfter("#editUser");
                }
                else {
                    $("<div class='alert alert-danger'>" + data[1] + "</div>").insertAfter("#editUser");
                }
                
                $(".spinner-border").remove();
                $(this).find(":submit, input[name='generatePassword']").prop("disabled", false);
            }
        })
    }
});

//Delete User
function deleteUser(userId) {
    var card = $(".card#" + userId + " .card-body");
    
    if(confirm("Are you sure you want to delete this user?")) {
        $.ajax({
            url: root_dir + "admin/scripts/manageUsers.php",
            method: "POST",
            dataType: "json",
            data: ({userId, method: "deleteUser"}),
            success: function(data) {
                if(data[0] == 1) {
                    window.location.reload();
                }
                else {
                    card.find(".alert").remove();
                    card.append("<div class='alert alert-danger mt-3'>" + data[1] + "</div>");
                }
            }
        });
    }
}