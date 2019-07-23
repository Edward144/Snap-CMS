$("#companyInfo input[type='submit']").click(function() {
    event.preventDefault();
    
    var logoData = $("#companyInfo input[name='logo']").prop('files')[0];
    var logo = new FormData();
    var logoUrl = '/admin/images/default-logo.png';
    
    logo.append('file', logoData);
    
    $.ajax({
        url: 'scripts/logoUpload.php',
        type: "POST",
        dataType: "json",
        data: logo,
        processData: false, 
        contentType: false,
        success: function(data) {
            logoUrl = data;
            
            var name = $("#companyInfo input[name='companyName']").val();
            var add1 = $("#companyInfo input[name='address1']").val();
            var add2 = $("#companyInfo input[name='address2']").val();
            var add3 = $("#companyInfo input[name='address3']").val();
            var add4 = $("#companyInfo input[name='address4']").val();
            var postcode = $("#companyInfo input[name='postcode']").val();
            var country = $("#companyInfo select[name='country']").val();
            var phone = $("#companyInfo input[name='phone']").val();
            var fax = $("#companyInfo input[name='fax']").val();
            var email = $("#companyInfo input[name='email']").val();
            var vat = $("#companyInfo input[name='vat']").val();
            var reg = $("#companyInfo input[name='reg']").val();

            /*if(name == "") {
                $("#companyInfo .message").text("Company Name is required.");

                return;
            }*/

            if(country == null) {
                country == "";
            }

            $.ajax({
                url: "scripts/updateCompany.php",
                method: "GET",
                dataType: "json",
                data: ({name, add1, add2, add3, add4, postcode, country, phone, fax, email, vat, reg, country, logoUrl}),
                success: function(data) {
                    $("#companyInfo .message").text(data);
                }
            });
        }
    });
});

$("#companyInfo input[name='postcode']").on("keyup", function() {
    var postcode = formatPostcode($(this).val());
    
    $(this).val(postcode);
});

$("#companyInfo input[name='clearLogo']").click(function() {
    $.ajax({
        url: "scripts/removeLogo.php",
        method: "GET",
        dataType: "json",
        success: function(data) {            
            if(data == 1) {
                location.reload();
            }
            else {
                $("#companyInfo .message").text(data);
            }
        }
    });
});

$("#socialLinks input[type='submit']").click(function() {
    event.preventDefault(); 
    var facebook = $("#socialLinks input[name='facebook']").val();
    var twitter = $("#socialLinks input[name='twitter']").val();
    var youtube = $("#socialLinks input[name='youtube']").val();
    var instagram = $("#socialLinks input[name='instagram']").val();
    var linkedin = $("#socialLinks input[name='linkedin']").val();
    
    $.ajax({
       url: "scripts/updateSocial.php",
        method: "POST",
        dataType: "json",
        data: ({facebook, twitter, youtube, instagram, linkedin}),
        success: function(data) {
            $("#socialLinks .message").text(data);
        }
    });
});