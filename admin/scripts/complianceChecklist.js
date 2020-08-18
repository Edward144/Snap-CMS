function uniqueId(increment = 0) {
    return btoa(Date.now() + increment).replace(/=/g, "");
}

function makeSortable() {
    //Main Sections
    $("#checklistStructure").sortable({ connectWith: "#checklistStructure", containment: "#checklistStructure" });

    //Sub Sections
    $(".subSections > .col").sortable({ connectWith: ".subSections > .col", containment: ".subSections > .col", items: "> .subSection" });

    //Questions
    $(".questions > .col").sortable({ connectWith: "#checklistStructure", items: "> .form-group:not(#mc)" });
}

makeSortable();

//Add Main Section    
$("#checklist").on("click", "input[name='createSection']", function() {
    var btn = $(this);
    btn.prop("disabled", true);

    var sectionCount = $(".mainSection").length + 1;
    var sectionTemplate = 
        `<div class="mainSection border-bottom border-light">
            <div class="row bg-dark header">
                <div class="col-md py-2">
                    <input type="text" class="form-control" name="mainName" placeholder="Section Name" value="Section ` + sectionCount + `">
                </div>

                <div class="col-2 py-2">
                    <input type="button" class="btn btn-danger" name="deleteSection" value="Delete Section">
                </div>

                <div class="col-1 py-2 ml-auto expander">
                    <a class="btn btn-light" href="#main` + uniqueId() + `" data-toggle="collapse"><span class="fa fa-pencil-alt"></span></a>
                </div>
            </div>

            <div class="row subSections bg-light collapse" id="main` + uniqueId() + `">
                <div class="col">
                    <form class="mt-3">
                        <div class="form-group">
                            <input type="button" class="btn btn-info" name="createSubsection" value="Create Sub-section">
                        </div>
                    </form>
                </div>
            </div>
        </div>`;

    $("#checklistStructure").append(sectionTemplate);
    makeSortable();

    setTimeout(function() {
        btn.prop("disabled", false);
    }, 500);
});

//Add Sub Section    
$("#checklist").on("click", "input[name='createSubsection']", function() {
    var btn = $(this);
    btn.prop("disabled", true);

    var subCount = $(this).parents(".subSections").first().find(".subSection").length + 1;
    var subsectionTemplate = 
        `<div class="subSection border-bottom border-light">
                <div class="row bg-secondary header">
                    <div class="col-md py-2">
                        <input type="text" class="form-control" name="subName" placeholder="Sub-Section Name" value="Sub-Section ` + subCount + `">
                    </div>

                    <div class="col-2 py-2">
                        <input type="button" class="btn btn-danger" name="deleteSection" value="Delete Section">
                    </div>

                    <div class="col-1 py-2 ml-auto expander">
                        <a class="btn btn-light" href="#sub` + uniqueId() + `" data-toggle="collapse"><span class="fa fa-pencil-alt"></span></a>
                    </div>
                </div>

                <div class="row questions collapse" id="sub` + uniqueId() + `">
                    <div class="col pt-2">
                        <div class="form-group question" id="mc">
                            <div class="row">
                                <div class="col-sm-10">
                                    <label><span class="fas fa-ellipsis-h mr-1"></span>Multiple Choice Question ` + uniqueId(1) + `</label>
                                    <input type="text" class="form-control" name="` + uniqueId(1) + `" placeholder="Enter the question here that users will answer by selecting: Yes, Working Towards, No">
                                </div>
                            </div>
                        </div>

                        <form class="mt-3">
                            <div class="form-group float-none float-sm-left mr-2">
                                <input type="button" class="btn btn-info" name="createQuestion" value="Add Question">
                            </div>

                            <div class="form-group">
                                <input type="button" class="btn btn-info" name="createDocument" value="Add Document Uploader">
                            </div>
                        </form>
                    </div>
                </div>
            </div>`;

    $(subsectionTemplate).insertBefore($(this).parents("form").first());
    makeSortable();

    setTimeout(function() {
        btn.prop("disabled", false);
    }, 500);
});

//Add Question / Document Uploader
$("#checklist").on("click", "input[name='createQuestion'], input[name='createDocument']", function() {
    var btn = $(this);
    btn.prop("disabled", true);

    var questionTemplate = 
        `<div class="form-group question">
            <div class="row">
                <div class="col">
                    <label><span class="fa fa-question-circle mr-1"></span>Question ` + uniqueId() + `</label>
                    <input type="text" class="form-control" name="` + uniqueId() + `" placeholder="Enter your question here">
                </div>

                <div class="col-sm-2 d-flex align-items-end mt-sm-0 mt-3">
                    <button name="deleteQuestion" class="btn btn-danger">&times;</button>
                </div>
            </div>
        </div>`;

    var documentTemplate = 
        `<div class="form-group document">
            <div class="row">
                <div class="col">
                    <label><span class="fa fa-cloud-upload-alt mr-1"></span>Document Uploader ` + uniqueId() + `</label>
                    <input type="text" class="form-control" name="` + uniqueId() + `" placeholder="Enter the name of the required document here">
                </div>

                <div class="col-sm-2 d-flex align-items-end mt-sm-0 mt-3">
                    <button name="deleteQuestion" class="btn btn-danger">&times;</button>
                </div>
            </div>
        </div>`;

    if($(this).attr("name") == "createQuestion") {
        $(questionTemplate).insertBefore($(this).parents("form").first());
    }
    else if($(this).attr("name") == "createDocument") {
        $(documentTemplate).insertBefore($(this).parents("form").first());
    }

    makeSortable();

    setTimeout(function() {
        btn.prop("disabled", false);
    }, 500);
});

//Delete Section
$("#checklist").on("click", ".mainSection > .header input[name='deleteSection']", function() {
    if(confirm("Are you sure you want to delete this section?")) {
        $(this).parents(".mainSection").first().remove();
    }
});

//Delete Sub Section
$("#checklist").on("click", ".subSection > .header input[name='deleteSection']", function() {
    if(confirm("Are you sure you want to delete this sub-section?")) {
        $(this).parents(".subSection").first().remove();
    }
});

//Delete Question
$("#checklist").on("click", "button[name='deleteQuestion']", function() {
    if(confirm("Are you sure you want to delete this question?")) {
        $(this).parents(".question").first().remove();
    }
});

//Save Checklist
$("#checklist input[type='submit']").click(function() {
    event.preventDefault();

    var btn = $(this);
    var sections = {};
    var i = 0;

    btn.prop("disabled", true);
    $("<div class='spinner-border ml-1'><span class='sr-only'>Processing...</span></div>").insertAfter(btn);
    $("#checklist").find(".alert").remove();

    $("#checklistStructure > .mainSection").each(function() {
        var subSections = {};
        var j = 0;

        $(this).find(".subSection").each(function() {
            var questions = {};
            var k = 0; 

            $(this).find(".question, .document").each(function() {
                questions[k] = {
                    id: $(this).find("input[type='text']").first().attr("name"),
                    type: ($(this).attr("id") == "mc" ? "multipleChoice" : ($(this).hasClass("question") ? "question" : "document")),
                    name: $(this).find("input[type='text']").first().val()
                }
                k++;
            });

            subSections[j] = {
                id: $(this).children(".questions").attr("id").split("sub")[1],
                name: $(this).find("input[name='subName']").first().val(),
                questions: questions
            }

            j++;
        });

        sections[i] = {
            id: $(this).children(".subSections").attr("id").split("main")[1],
            name: $(this).find("input[name='mainName']").first().val(),
            subSections: subSections
        }

        i++;
    });

    $.ajax({
        url: root_dir + "admin/scripts/complianceChecklist.php",
        method: "POST",
        dataType: "json",
        data: ({sections, method: "saveStructure"}),
        success: function(data) {
            $("<div class='alert alert-" + data[0] + "'>" + data[1] + "</div>").insertAfter(btn.parent(".form-group"));                
            btn.prop("disabled", false);
            $(".spinner-border").remove();
        }
    });
});