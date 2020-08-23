//Create Contact
$("#createContact").submit(function() {
    $("<div class='spinner-border ml-1'><span class='sr-only'>Processing...</span></div>").insertAfter($(this).find(":submit")).parent(".form-group")
    $(this).find(":submit").prop("disabled", true);
});

//Delete Contact
$("input[name='delete']").click(function() {
    var btn = $(this);
    
    $(".alert").remove();
    
    if(confirm("Are you sure you want to delete this content?")) {
        $.ajax({
            url: root_dir + "admin/scripts/manageContact.php",
            method: "POST",
            dataType: "json",
            data: ({id: btn.attr("data-id"), method: "deleteContact"}),
            success: function(data) {
                if(data[0] == 1) {
                    window.location.reload();
                }
                else {
                    $(btn).parents(".col").first().append(
                        `<div class="alert alert-` + (data[0] == 0 ? 'danger' : 'success') + ` mt-3">
                            ` + data[1] + `
                        </div>`
                    );
                }
            }
        });
    }
});

//Edit Contact
$("#contactList input[name='edit']").click(function() {
    window.location.href = window.location.href.split("?")[0] + "?id=" + $(this).attr("data-id");
});

//Return to List
$("input[name='return']").click(function() {
    window.location.href = window.location.href.split("?")[0];
});

//Update Contact
$("#updateContact").submit(function() {
	var valid = true;
	var sitekey = $(this).find("input[name='sitekey']");
	var secretkey = $(this).find("input[name='secretkey']");
	var emails = $(this).find("textarea[name='emails']");
	var vEmails = [];
	var structure = {};
	
	$(this).find(".alert, .invalid-feedback").remove();
	$(this).find(".is-invalid").removeClass("is-invalid");
	
	if(sitekey.val() != '' && secretkey.val() == '') {
		secretkey.addClass("is-invalid");
		$("<div class='invalid-feedback'>Secretkey must also be set for reCaptcha to work</div>").insertAfter(secretkey);
		valid = false;
	}
	else if(secretkey.val() != '' && sitekey.val() == '') {
		sitekey.addClass("is-invalid");
		$("<div class='invalid-feedback'>Site must also be set for reCaptcha to work</div>").insertAfter(sitekey);
		valid = false;
	}
	
	$.each(emails.val().split(","), function(index, email) {
		if(email.indexOf("@") < 0 || email.split("@")[1].indexOf(".") < 0 || email.split("@")[1].split(".")[1].length <= 0) {
        	emails.addClass("is-invalid");
			$("<div class='invalid-feedback'>An email appears to be invalid</div>").insertAfter(emails);
			valid = false;
		}
		else {
			vEmails.push(email);
		}
	});
	
	if(valid == true) {		
		structure['emails'] = vEmails;
		$(this).find("input[name='structure']").val(JSON.stringify(structure));
		
		$(this).find(":submit").prop("disabled", true);
		$("<div class='spinner-border ml-1'><span class='sr-only'>Processing...</span></div>").insertAfter($(this).find(":submit"));
	}
	else {
		event.preventDefault();
	}
	
	console.log(structure);
});