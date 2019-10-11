var tableName = "sliders"

//Update Visibility
$("input[name='hide']").click(function() {
    var btn = $(this);
    changeVisibility(btn, tableName);
});

$("input[name='show']").click(function() {
    var btn = $(this);
    changeVisibility(btn, tableName);
});

//Delete Content
$("input[name='delete']").click(function() {
    var btn = $(this);
    
    deleteContent(btn, tableName);
});

//Edit Content
$("input[name='edit']").click(function() {
    var btn = $(this);
    
    editContent(btn, tableName);
});