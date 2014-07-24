function dynaFormChanged (frm) {
    for (var i1 = 0; i1 <= frm.elements.length - 1; i1++) {
        if ((frm.elements[i1].type === "radio" || frm.elements[i1].type === "checkbox") && (frm.elements[i1].checked !== frm.elements[i1].defaultChecked)) {
            return true;
        }
        if ((frm.elements[i1].type === "textarea" || frm.elements[i1].type === "text" || frm.elements[i1].type === "file") && (frm.elements[i1].value !== frm.elements[i1].defaultValue)) {
            return true;
        }
        if (frm.elements[i1].tagName.toLowerCase() === "select") {
            var selectDefaultValue = frm.elements[i1].value;
            for (var i2 = 0; i2 <= frm.elements[i1].options.length - 1; i2++) {
                if (frm.elements[i1].options[i2].defaultSelected) {
                    selectDefaultValue = frm.elements[i1].options[i2].value;
                    break;
                }
            }
            if (frm.elements[i1].value !== selectDefaultValue) {
                return true;
            }
        }
    }
    return false;
}

function validateNameField (form, type) {
    var i, j = 0, dt, name;
    dt = form.getElementsByTagName(type);
    for (i = 0; i < dt.length; i++) {
        name = dt[i].name;
        if (!name)
            name = "field" + type + j;
        dt[i].name = "form[" + name + "]";
        j++;
    }
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

    var form = document.getElementsByTagName("form")[0];
    validateNameField(form, "input");
    validateNameField(form, "textarea");
    validateNameField(form, "select");

    var type = document.createElement("input");
    type.type = "hidden";
    type.value = "ASSIGN_TASK";
    type.name = "TYPE";

    var uid = document.createElement("input");
    uid.type = "hidden";
    uid.value = dyn_uid;
    uid.name = "UID";

    var position = document.createElement("input");
    position.type = "hidden";
    position.value = "10000";
    position.name = "POSITION";

    var action = document.createElement("input");
    action.type = "hidden";
    action.value = "ASSIGN";
    action.name = "ACTION";

    var dynaformname = document.createElement("input");
    dynaformname.type = "hidden";
    dynaformname.value = __DynaformName__;
    dynaformname.name = "__DynaformName__";

    var appuid = document.createElement("input");
    appuid.type = "hidden";
    appuid.value = app_uid;
    appuid.name = "APP_UID";

    form.action = "cases_SaveData?UID=" + dyn_uid + "&APP_UID=" + app_uid;
    form.method = "post";
    form.appendChild(type);
    form.appendChild(uid);
    form.appendChild(position);
    form.appendChild(action);
    form.appendChild(dynaformname);
    form.appendChild(appuid);

};