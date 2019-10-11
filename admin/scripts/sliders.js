//Update Visibility
$("input[name='hide']").click(function() {
    var btn = $(this);
    changeVisibility(btn, 'sliders');
});

$("input[name='show']").click(function() {
    var btn = $(this);
    changeVisibility(btn, 'sliders');
});

//Delete Content
$("input[name='delete']").click(function() {
    var btn = $(this);
    
    deleteContent(btn, 'sliders');
});