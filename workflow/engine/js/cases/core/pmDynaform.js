function ajax_post(action, form, method, callback, asynchronous) {
    document.getElementById("dyn_forward").onclick();
    window.onload = function () {
        method();
    };
}
function dynaFormChanged(frm) {
    return false;
}
function clearData(data) {
    for (var i in data) {
        if (data[i] instanceof Array || data[i] instanceof Object)
            data[i] = clearData(data[i]);
        if (i === "optionsSql")
            data[i] = [];
    }
    return data;
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
    function loadAjaxParams () {
        var url;
        var action;
        var method;

        if (filePost) {
            url = location.protocol + '//' + location.host;
            //In case the form is in review
            if (filePost.indexOf('Supervisor') >= 0){
                action = 'cases_SaveDataSupervisor?UID=' + dyn_uid;
                url += '/sys' + workspace + '/en/neoclassic/cases/' + action;
            } else if(filePost.indexOf('Email') >= 0){ //In case the form is sent as Email response
                action = filePost;
                url += '/sys' + workspace + '/en/neoclassic/services/' + action;
            } else { //In case the form is in web entry
                action = prj_uid + '/' + filePost;
                url += '/sys' + workspace + '/en/neoclassic/' + action;
            }
            method = 'POST';
        } else if (app_uid){ //In case the form is in running cases
            url = location.protocol + '//' + location.host;
            action = "cases_SaveData?UID=" + dyn_uid + "&APP_UID=" + app_uid;
            url += '/sys' + workspace + '/en/neoclassic/cases/' + action;
            method = 'POST';
        }
        return {
            url: url,
            action: action,
            method: method
        };
    }
    var data = jsondata;
    window.project = new PMDynaform.core.Project({
        data: data,
        formAjax: loadAjaxParams(),
        keys: {
            server: location.host,
            projectId: prj_uid,
            workspace: workspace
        },
        token: credentials,
        submitRest: false,
        onLoad: function () {
            var dyn_content_history = document.createElement("input");
            dyn_content_history.type = "hidden";
            dyn_content_history.name = "form[DYN_CONTENT_HISTORY]";
            dyn_content_history.value = JSON.stringify(clearData(jsondata));
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
            form.setAttribute("encType", "multipart/form-data");
            form.appendChild(dyn_content_history);
            form.appendChild(dynaformname);
            form.appendChild(appuid);
            form.appendChild(arrayRequired);
            var dyn_forward = document.getElementById("dyn_forward");
            dyn_forward.onclick = function () {
                if (window.project.getForms()[0].isValid()) {
                    form.submit();
                }
                return false;
            };
            if (triggerDebug === true) {
                showdebug();
            }
        }
    });
});