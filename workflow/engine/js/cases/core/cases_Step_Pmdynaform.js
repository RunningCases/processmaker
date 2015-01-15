function dynaFormChanged(frm) {
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
$(window).load(function () {
    if (pm_run_outside_main_app === 'true') {
        if (parent.showCaseNavigatorPanel) {
            parent.showCaseNavigatorPanel('DRAFT');
        }

        if (parent.setCurrent) {
            parent.setCurrent(dyn_uid);
        }
    }

    var data = jsondata;
    window.project = new PMDynaform.core.Project({
        data: data,
        keys: {
            server: location.host,
            projectId: prj_uid,
            workspace: workspace
        },
        token: credentials,
        submitRest: false
    });
    new PMDynaform.core.Proxy({
        url: "http://" + window.project.keys.server + "/" + window.project.keys.apiName + "/" + window.project.keys.apiVersion + "/" + window.project.keys.workspace + "/cases/" + app_uid + "/variables",
        method: 'GET',
        data: {},
        keys: window.project.token,
        successCallback: function (xhr, response) {
            window.project.setData2(response);
        }
    });

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
    var form = document.getElementsByTagName("form")[0];
    form.action = "cases_SaveData?UID=" + dyn_uid + "&APP_UID=" + app_uid;
    form.method = "post";
    form.appendChild(type);
    form.appendChild(uid);
    form.appendChild(position);
    form.appendChild(action);
    form.appendChild(dynaformname);
    form.appendChild(appuid);

    var dyn_forward = document.getElementById("dyn_forward");
    dyn_forward.onclick = function () {
        form.submit();
        return false;
    };
});