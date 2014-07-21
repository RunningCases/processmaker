function dynaFormChanged (frm) {
    for (var i1 = 0; i1 <= frm.elements.length - 1; i1++) {
        if ((frm.elements[i1].type == "radio" || frm.elements[i1].type == "checkbox") && (frm.elements[i1].checked != frm.elements[i1].defaultChecked)) {
            return true;
        }
        if ((frm.elements[i1].type == "textarea" || frm.elements[i1].type == "text" || frm.elements[i1].type == "file") && (frm.elements[i1].value != frm.elements[i1].defaultValue)) {
            return true;
        }
        if (frm.elements[i1].tagName.toLowerCase() == "select") {
            var selectDefaultValue = frm.elements[i1].value;
            for (var i2 = 0; i2 <= frm.elements[i1].options.length - 1; i2++) {
                if (frm.elements[i1].options[i2].defaultSelected) {
                    selectDefaultValue = frm.elements[i1].options[i2].value;
                    break;
                }
            }
            if (frm.elements[i1].value != selectDefaultValue) {
                return true;
            }
        }
    }
    return false;
}

window.onload = function () {
    var data = JSON.parse(jsondata);
    var modelPMDynaform = new PMDynaform.Model.Form(data);
    var viewPMDynaform = new PMDynaform.View.Form({tagName: "div", renderTo: $(".container"), model: modelPMDynaform});

    if (pm_run_outside_main_app === 'true') {
        if (parent.showCaseNavigatorPanel) {
            parent.showCaseNavigatorPanel('DRAFT');
        }

        if (parent.setCurrent) {
            parent.setCurrent(dyn_uid);
        }
    }
};