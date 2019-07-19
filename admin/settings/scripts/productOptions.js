$("input[name='addSpec']").click(function() {
    var row = 0;

    $(".specificationOption tr:not(.headers)").each(function() {
        if($(this).attr("id").split("spec")[1] > row) {
            row = $(this).attr("id").split("spec")[1];
        }
    });

    row = parseInt(row) + 1;

    $(".specificationOption").append(
        "<tr id='spec" + row + "'>" + 
            "<td><input type='text' name='specName'></td>" +
            "<td><input type='text' name='specValue'></td>" + 
            "<td><input class='badButton' type='button' name='deleteSpec' value='Delete Spec'></td>" +
        "</tr>"
    );
});

$(".specificationOption").on("click", "input[name='deleteSpec']", function() {
    $(this).closest("tr").remove();
});