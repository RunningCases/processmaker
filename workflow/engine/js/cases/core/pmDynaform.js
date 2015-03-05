function ajax_post(action, form, method, callback, asynchronous) {
    document.getElementById("dyn_forward").onclick();
    window.onload = function () {
        method();
    };
}
function dynaFormChanged(frm) {
    return true;
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
    if (step_mode)
        data.items[0].mode = step_mode.toLowerCase();
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
    var arrayRequired = document.createElement("input");
    arrayRequired.type = "hidden";
    arrayRequired.name = "DynaformRequiredFields";
    arrayRequired.value = fieldsRequired;
    var form = document.getElementsByTagName("form")[0];
    form.action = filePost ? filePost : "cases_SaveData?UID=" + dyn_uid + "&APP_UID=" + app_uid;
    form.method = "post";
    form.enctype = "multipart/form-data";
    form.appendChild(type);
    form.appendChild(uid);
    form.appendChild(position);
    form.appendChild(action);
    form.appendChild(dynaformname);
    form.appendChild(appuid);
    form.appendChild(arrayRequired);
    var dyn_forward = document.getElementById("dyn_forward");
    dyn_forward.onclick = function () {
        form.submit();
        return false;
    };
    if (triggerDebug === true) {
        showdebug();
    }
});