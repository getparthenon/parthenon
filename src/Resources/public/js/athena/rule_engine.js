$("#add_rule_entity").change(function() {

    var entity = $("#add_rule_entity").val();
    $("#add_rule_field").empty();

    window.entityProperties[entity].forEach( function(e, i){
        $("#add_rule_field").append($('<option></option>').attr('value', e).text(e));
    });

});
$("#add_rule_action").change(function() {

    var entity = $("#add_rule_action").val();
    $('#action-options').empty();

    window.actionOptions[entity].forEach( function(e, i){
        var html = ""

        var info = ""
        if (e.info !== undefined) {
            info = e.info
        }

        if (e.type == "text" || e.type == "email") {
            html = returnTextHtml(e.label, e.name, e.type, info)
        } else if (e.type == "select") {
            html = returnSelectHtml(e.label, e.name, e.choices, info)
        } else if (e.type == "textarea") {
            html = returnTexareatHtml(e.label, e.name, e.type, info)
        }

        $('#action-options').append(html)

        $('#option-field-'+e.name).change(function () {
            var value = $('#option-field-'+e.name).val();
            var currentData = $('#add_rule_options').val();
            if (currentData != '') {

                currentObject = JSON.parse(currentData);
            } else {
                currentObject = {}
            }
            currentObject[e.name] = value;
            $('#add_rule_options').val(JSON.stringify(currentObject))
        })
    });

});

function returnTextHtml(label, name, type, info) {
    var output = "\n" +
        "        <div class=\"form-group row\">\n" +
        "            <label for=\"filter-value\" class=\"col-sm-2 col-form-label\">"+label+"</label>\n" +
        "            <div class=\"col-sm-10\">\n" +
        "                <input type='"+type+"' id='option-field-"+name+"' name='"+name+"' value=''>\n"
    if (info !== "") {
        output = output + "                <p>"+info+"</p>\n"
    }
    output = output + "            </div>\n" +
        "        </div>"

    return output
}

function returnTexareatHtml(label, name, type, info) {
    var output =  "\n" +
        "        <div class=\"form-group row\">\n" +
        "            <label for=\"filter-value\" class=\"col-sm-2 col-form-label\">"+label+"</label>\n" +
        "            <div class=\"col-sm-10\">\n" +
        "                <textarea id='option-field-"+name+"' name='"+name+" rows='10' cols='40'></textarea>\n"
    if (info !== "") {
        output = output + "                <p>"+info+"</p>\n"
    }
    output = output + "            </div>\n" +
        "        </div>"

    return output
}

function returnSelectHtml(label, name, choices, info) {

    var html =  "\n" +
        "        <div class=\"form-group row\">\n" +
        "            <label for=\"filter-value\" class=\"col-sm-2 col-form-label\">"+label+"</label>\n" +
        "            <div class=\"col-sm-10\">\n" +
        "                <select id='option-field-"+name+"' name='"+name+"' >\n";

    for (var prop in choices) {
        html = html + "<option value=\""+ choices[prop] +"\">"+prop+"</option>";
    }
    html = html +
        "</select>"

    if (info !== "") {
        output = output + "                <p>"+info+"</p>\n"
    }

    output = output + "            </div>\n" +
        "        </div>";
    return html
}