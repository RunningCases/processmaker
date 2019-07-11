<?php

use ProcessMaker\Plugins\PluginRegistry;

global $G_TMP_MENU;
global $RBAC;
$partnerFlag = (defined('PARTNER_FLAG')) ? PARTNER_FLAG : false;

/*----------------------------------********---------------------------------*/
$conf = new Configurations();
$sAudit = $conf->getConfiguration('AUDIT_LOG', 'log');
$licensedFeatures = PMLicensedFeatures::getSingleton();
/*----------------------------------********---------------------------------*/
if ($RBAC->userCanAccess('PM_SETUP') === 1) {
    $pmSetupPermission = true;
    if ($RBAC->userCanAccess('PM_SETUP_LOGO') === 1) {
        $G_TMP_MENU->AddIdRawOption(
            'LOGO', '../admin/pmLogo',
            G::LoadTranslation('ID_LOGO'),
            'icon-pmlogo.png', '', 'settings'
        );
    }
    if ($RBAC->userCanAccess('PM_SETUP_EMAIL') === 1) {
        $G_TMP_MENU->AddIdRawOption(
            "EMAIL_SERVER", "../emailServer/emailServer",
            G::LoadTranslation("ID_EMAIL_SERVER_TITLE"),
            "icon-email-settings1.png", "", "settings"
        );
    }
    if ($RBAC->userCanAccess('PM_SETUP_CALENDAR') === 1) {
        $G_TMP_MENU->AddIdRawOption(
            'CALENDAR', 'calendarList',
            G::LoadTranslation('ID_CALENDAR'),
            'icon-calendar.png', '', 'settings'
        );
    }
    if ($RBAC->userCanAccess('PM_SETUP_PROCESS_CATEGORIES') === 1) {
        $G_TMP_MENU->AddIdRawOption(
            'PROCESS_CATEGORY', '../processCategory/processCategoryList',
            G::LoadTranslation('ID_PROCESS_CATEGORY'),
            "rules.png", '', 'settings'
        );
    }
}

if ($RBAC->userCanAccess('PM_SETUP') === 1) {
    if ($RBAC->userCanAccess('PM_SETUP_SKIN') === 1) {
        $G_TMP_MENU->AddIdRawOption(
            'SKINS', 'skinsList',
            G::LoadTranslation('ID_SKINS'),
            'icon-skins.png', '', 'settings'
        );
    }
    if (!$partnerFlag) {
        /**
         * Remove heartbeat config from core, it will probably be used again
         * when the functionality will be redesigned.
         */
        if ($RBAC->userCanAccess('PM_SETUP_HEART_BEAT') === 1 && false) {
            $G_TMP_MENU->AddIdRawOption(
                'HEARTBEAT', 'processHeartBeatConfig',
                G::LoadTranslation('ID_HEARTBEAT_CONFIG'),
                "heartBeat.jpg", '', 'settings'
            );
        }
    }
    if ($RBAC->userCanAccess('PM_SETUP_ENVIRONMENT') === 1) {
        $G_TMP_MENU->AddIdRawOption(
            'ENVIRONMENT_SETTINGS', 'environmentSettings',
            G::LoadTranslation('ID_ENVIRONMENT_SETTINGS'),
            "", '', 'settings'
        );
    }
}

if ($RBAC->userCanAccess('PM_SETUP') === 1) {
    if ($RBAC->userCanAccess('PM_SETUP_CLEAR_CACHE') === 1) {
        $G_TMP_MENU->AddIdRawOption(
            'CLEAR_CACHE', 'clearCompiled',
            G::LoadTranslation('ID_CLEAR_CACHE'),
            'icon-rebuild-clean.png', "", 'settings'
        );
    }
    if ($RBAC->userCanAccess('PM_SETUP_PM_TABLES') === 1) {
        $G_TMP_MENU->AddIdRawOption(
            'PM_TABLES', '../pmTables',
            G::LoadTranslation('ID_ADDITIONAL_TABLES'),
            'icon-tables.png', '', 'settings'
        );
    }
    if ($RBAC->userCanAccess('PM_SETUP_LOGIN') === 1) {
        $G_TMP_MENU->AddIdRawOption(
            'LOGIN', 'loginSettings',
            G::LoadTranslation('LOGIN'),
            "", '', 'settings'
        );
    }
    if ($RBAC->userCanAccess('PM_SETUP_DASHBOARDS') === 1) {
        $G_TMP_MENU->AddIdRawOption(
            'DASHBOARD', '../DashboardModule/dashletsList',
            ucfirst(G::LoadTranslation('ID_DASHBOARD')),
            '', '', 'settings'
        );
        /*----------------------------------********---------------------------------*/
        if ($licensedFeatures->verifyfeature('r19Vm5DK1UrT09MenlLYjZxejlhNUZ1b1NhV0JHWjBsZEJ6dnpJa3dTeWVLVT0=')) {
            $G_TMP_MENU->AddIdRawOption(
                'STRATEGIC_DASHBOARD', '../strategicDashboard/dashboardList',
                ucfirst(G::LoadTranslation('ID_STRATEGIC_DASHBOARD')),
                '', '', 'settings'
            );
        }
        if ($licensedFeatures->verifyfeature('7qhYmF1eDJWcEdwcUZpT0k4S0xTRStvdz09')) {
            $G_TMP_MENU->AddIdRawOption(
                'PMGMAIL', '../pmGmail/formPMGmail',
                ucfirst(G::LoadTranslation('ID_PMGMAIL')),
                '', '', 'settings'
            );
        }
        /*----------------------------------********---------------------------------*/
    }
}
//tools options
if ($RBAC->userCanAccess('PM_SETUP_ADVANCE') === 1) {
    if ($RBAC->userCanAccess('PM_SETUP_LANGUAGE') === 1) {
        $G_TMP_MENU->AddIdRawOption(
            'LANGUAGES', 'languages',
            G::LoadTranslation('ID_LANGUAGES'),
            'icon-language.png', '', 'settings'
        );
    }
    if ($RBAC->userCanAccess('PM_SETUP_CASES_LIST_CACHE_BUILDER') === 1) {
        $G_TMP_MENU->AddIdRawOption(
            'APPCACHEVIEW_SETUP', '../setup/appCacheViewConf',
            G::LoadTranslation('ID_APPCACHE_SETUP'),
            "", '', 'settings'
        );
    }
    if (!$partnerFlag) {
        if ($RBAC->userCanAccess('PM_SETUP_PLUGINS') === 1) {
            $G_TMP_MENU->AddIdRawOption(
                'PLUGINS', 'pluginsMain',
                G::LoadTranslation('ID_PLUGINS_MANAGER'),
                'icon-plugins.png', '', 'plugins'
            );
        }
    }
}

//users options
if ($RBAC->userCanAccess('PM_USERS') === 1) {
    $G_TMP_MENU->AddIdRawOption('USERS', '../users/users_List', G::LoadTranslation('ID_USERS_LIST'),
        'icon-webservices.png', '', 'users');
    $G_TMP_MENU->AddIdRawOption('GROUPS', '../groups/groups', G::LoadTranslation('ID_GROUPS'), '', '', 'users');
    $G_TMP_MENU->AddIdRawOption(
        'DEPARTAMENTS', '../departments/departments',
        G::LoadTranslation('ID_DEPARTMENTS_USERS'),
        '', '', 'users'
    );
    $G_TMP_MENU->AddIdRawOption('ROLES', '../roles/roles_List',
        G::LoadTranslation('ID_ROLES'),
        '', '', 'users'
    );
}

if ($RBAC->userCanAccess('PM_USERS') === 1 && $RBAC->userCanAccess('PM_SETUP_USERS_AUTHENTICATION_SOURCES') === 1) {
    $G_TMP_MENU->AddIdRawOption(
        'AUTHSOURCES', '../authSources/authSources_List',
        G::LoadTranslation('ID_AUTH_SOURCES'),
        '', '', 'users'
    );
    $G_TMP_MENU->AddIdRawOption('UX', '../admin/uxList', G::LoadTranslation('ID_USER_EXPERIENCE'), '', '', 'users');
}

if ($RBAC->userCanAccess('PM_SETUP_ADVANCE') === 1) {
    $G_TMP_MENU->AddIdRawOption('SYSTEM', '../admin/system', G::LoadTranslation('ID_SYSTEM'), '', '', 'settings');
    $G_TMP_MENU->AddIdRawOption(
        'INFORMATION', '../setup/systemInfo?option=processInfo',
        G::LoadTranslation('ID_SYSTEM_INFO'),
        '', '', 'settings'
    );
}

if ($RBAC->userCanAccess('PM_SETUP') === 1 && $RBAC->userCanAccess('PM_SETUP_LOGS') === 1) {
    $G_TMP_MENU->AddIdRawOption('EVENT', '../events/eventList', G::LoadTranslation('ID_EVENTS_CLASSIC'), '', '',
        'logs');
    $G_TMP_MENU->AddIdRawOption(
        'LOG_CASE_SCHEDULER', '../cases/cases_Scheduler_Log',
        G::LoadTranslation('ID_CASE_SCHEDULER_CLASSIC'),
        "icon-logs-list.png", '', 'logs'
    );
    $G_TMP_MENU->AddIdRawOption("CRON", "../setup/cron", G::LoadTranslation("ID_CRON_ACTIONS"), null, null, 'logs');
    $G_TMP_MENU->AddIdRawOption(
        'EMAILS', '../mails/emailList',
        ucfirst(strtolower(G::LoadTranslation('ID_EMAILS'))),
        '', '', 'logs'
    );
    /*----------------------------------********---------------------------------*/
    if (isset($sAudit) && $sAudit !== false && $licensedFeatures->verifyfeature
        ('vtSeHNhT0JnSmo1bTluUVlTYUxUbUFSVStEeXVqc1pEUG5EeXc0MGd2Q3ErYz0=')
    ) {
        $G_TMP_MENU->AddIdRawOption(
            'AUDIT_LOG', '../setup/auditLog',
            ucfirst(G::LoadTranslation('ID_AUDITLOG_DISPLAY')),
            '', '', 'logs'
        );
    }
    /*----------------------------------********---------------------------------*/
}

/*----------------------------------********---------------------------------*/
if ($RBAC->userCanAccess('PM_SETUP_LOG_FILES') === 1) {
    $G_TMP_MENU->AddIdRawOption(
        'STANDARD_LOGGING', '../FileLogs/fileList',
        G::LoadTranslation('ID_STANDARD_LOGGING'), '', '', 'logs'
    );
}
/*----------------------------------********---------------------------------*/


if ($RBAC->userCanAccess('PM_SETUP') === 1) {
    $G_TMP_MENU->AddIdRawOption(
        'PM_REQUIREMENTS', '../setup/systemInfo',
        G::LoadTranslation('ID_PROCESSMAKER_REQUIREMENTS_CHECK'),
        '', '', 'settings'
    );
    $G_TMP_MENU->AddIdRawOption(
        'PHP_INFO', '../setup/systemInfo?option=php',
        G::LoadTranslation('ID_PHP_INFO'),
        '', '', 'settings'
    );
    /*----------------------------------********---------------------------------*/
    if ($licensedFeatures->verifyfeature('vtSeHNhT0JnSmo1bTluUVlTYUxUbUFSVStEeXVqc1pEUG5EeXc0MGd2Q3ErYz0=')) {
        $G_TMP_MENU->AddIdRawOption(
            "AUDIT_LOG", "auditLogConfig",
            G::LoadTranslation("ID_AUDITLOG_DISPLAY"),
            "", "", "settings"
        );
    }
    /*----------------------------------********---------------------------------*/
}
/*----------------------------------********---------------------------------*/
if (!file_exists(PATH_DATA_SITE . "plugin.singleton")) {
    require_once PATH_CORE . 'methods' . PATH_SEP . 'enterprise' . PATH_SEP . 'enterprise.php';
    $enterprise = new enterprisePlugin('enterprise');
    $enterprise->enable();
    $enterprise->setup();
}
$pmLicenseManagerO = PmLicenseManager::getSingleton();
$licenseStatusInfo = $pmLicenseManagerO->getCurrentLicenseStatus();
$licStatusMsg = null;

if ((isset($pmLicenseManagerO->plan)) && ($pmLicenseManagerO->plan != "")) {
    $lines = explode(" - ", $pmLicenseManagerO->plan);
    if (isset($lines[0])) {
        $licStatusMsg .= "<br><i><small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $lines[0] . "</small></i>";
    }
    if ((isset($lines[1])) && ($lines[1] != $lines[0])) {
        $licStatusMsg .= "<br><i><small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $lines[1] . "</small></i>";
    }
}

if ($licenseStatusInfo["message"] != "") {
    $licStatusMsg = "&nbsp;<font color=\"red\">(" . $licenseStatusInfo["message"] . ")</font>";
}
/*----------------------------------********---------------------------------*/
if ($RBAC->userCanAccess('PM_SETUP') == 1) {
    /*----------------------------------********---------------------------------*/
    $G_TMP_MENU->AddIdRawOption(
        'PMENTERPRISE', '../enterprise/addonsStore',
        G::LoadTranslation('ID_MENU_NAME') . $licStatusMsg,
        '', '', 'plugins'
    );
    if ($RBAC->userCanAccess('PM_SETUP_CUSTOM_CASES_LIST') == 1) {
        $G_TMP_MENU->AddIdRawOption(
            'CASES_LIST_SETUP', '../cases/casesListSetup',
            G::LoadTranslation('ID_CUSTOM_CASES_LISTS'),
            '', '', 'settings');
    }
    /*----------------------------------********---------------------------------*/
}

/*----------------------------------********---------------------------------*/
$pluginRegistry = PluginRegistry::loadSingleton();
$status = $pluginRegistry->getStatusPlugin('actionsByEmail');

if ((string)($status) !== 'enabled' &&
    $licensedFeatures->verifyfeature('zLhSk5TeEQrNFI2RXFEVktyUGpnczV1WEJNWVp6cjYxbTU3R29mVXVZNWhZQT0=') &&
    $RBAC->userCanAccess('PM_SETUP_LOGS') == 1
) {
    $G_TMP_MENU->AddIdRawOption(
        'PM_ACTIONS_BY_EMAIL_LOGS',
        '../actionsByEmail/ActionByEmail.php',
        G::LoadTranslation("ID_ACTIONS_BY_EMAIL_LOG"),
        '', '', 'logs'
    );
}
/*----------------------------------********---------------------------------*/
