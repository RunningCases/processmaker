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

$(window).load(function () {
    var data = JSON.parse(jsondata);

    window.dynaform = new PMDynaform.core.Project({
        data: data
    });

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
    type.name = "TYPE";
    type.value = "ASSIGN_TASK";

    var uid = document.createElement("input");
    uid.type = "hidden";
    uid.name = "UID";
    uid.value = dyn_uid;

    var position = document.createElement("input");
    position.type = "hidden";
    position.name = "POSITION";
    position.value = "10000";

    var action = document.createElement("input");
    action.type = "hidden";
    action.name = "ACTION";
    action.value = "ASSIGN";

    var dynaformname = document.createElement("input");
    dynaformname.type = "hidden";
    dynaformname.name = "__DynaformName__";
    dynaformname.value = __DynaformName__;

    var appuid = document.createElement("input");
    appuid.type = "hidden";
    appuid.name = "APP_UID";
    appuid.value = app_uid;

    form.action = "cases_SaveData?UID=" + dyn_uid + "&APP_UID=" + app_uid;
    form.method = "post";
    form.appendChild(type);
    form.appendChild(uid);
    form.appendChild(position);
    form.appendChild(action);
    form.appendChild(dynaformname);
    form.appendChild(appuid);
    
    var dyn_forward = document.getElementById("dyn_forward");
    dyn_forward.href = "cases_Step?TYPE=" + type.value + "&UID=" + dyn_uid + "&POSITION=" + position.value + "&ACTION=" + action.value + "";
});