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

//Add Input To Structure
$(".formInputs input[name='addInput']").click(function() {
	var type = $(".formInputs select[name='inputTypes']").val();
	var output;
	var general = 
		`<div class="input-group form-group">
			<div class="input-group-prepend">
				<span class="input-group-text">Input Type</span>
			</div>
			<input type="text" class="form-control" name="type" value="` + type + `" disabled>
			<div class="input-group-append">
				<div class="input-group-text">
					<input type="checkbox" name="required">
				</div>
				<span class="input-group-text">Required?</span>
			</div>
		</div>
		<div class="input-group form-group">
			<div class="input-group-prepend">
				<span class="input-group-text">Label</span>
			</div>
			<input type="text" class="form-control" name="label">
			<div class="input-group-append">
				<div class="input-group-text">
					<input type="checkbox" name="hidelabel">
				</div>
				<span class="input-group-text">Hide Label?</span>
			</div>
		</div>
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text">Placeholder</span>
			</div>
			<input type="text" class="form-control" name="placeholder">
		</div>`;
		
	$(".formInputs").parent().find(".alert").remove();
	
	switch(type) {
		case 'general': 
			output = 
				`<div class="input-group form-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Input Type</span>
					</div>
					<input type="text" class="form-control" name="type" value="general" disabled>
				</div>
				<div class="form-group mb-0">
					<textarea class="form-control" name="value" placeholder="Enter some to be displayed to the user..."></textarea>
				</div>`;
			break;
		case 'number':
			output = general +
				`<div class="input-group form-group mt-3">
					<div class="input-group-prepend">
						<span class="input-group-text">Min Value</span>
					</div>
					<input type="text" class="form-control" name="min">
				</div>
				<div class="input-group form-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Max Value</span>
					</div>
					<input type="text" class="form-control" name="max">
				</div>
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Step (Decimal Places)</span>
					</div>
					<input type="text" class="form-control" name="step">
				</div>`;
			break;
		case 'select':
			output = general +
				`<div class="input-group mt-3">
					<div class="input-group-prepend">
						<span class="input-group-text">Options</span>
					</div>
					<textarea class="form-control" name="options" placeholder="Option 1, Option 2, Option 3, etc..."></textarea>
				</div>`;
			break;
		case 'radio': 
			output = general +
				`<div class="input-group mt-3">
					<div class="input-group-prepend">
						<span class="input-group-text">Options</span>
					</div>
					<textarea class="form-control" name="options" placeholder="Option 1, Option 2, Option 3, etc..."></textarea>
				</div>`;
			break;
		case 'file': 
			output = general +
				`<div class="input-group mt-3">
					<div class="input-group-prepend">
						<span class="input-group-text">Allow Multiple Files</span>
					</div>
					<div class="input-group-append">
						<div class="input-group-text">
							<input type="checkbox" name="multiple">
						</div>
					</div>
				</div>`;
			break;
		default: 
			output = general;
			break;
	}
	
	if(output != null) {
		output = "<li class='list-group-item'><div class='form-group d-flex justify-content-end'><input type='button' class='btn btn-danger' name='delete' value='Delete Input'></div>" + output + "</li>";
		$(output).insertBefore($(this).parents(".list-group-item").first());
	}
	else {
		$("<div class='alert alert-danger mt-3'>Could not add input</div>").insertAfter(".formInputs");
	}
});