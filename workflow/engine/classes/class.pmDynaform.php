<?php

/**
 * class.pmDynaform.php
 * Implementing pmDynaform library in the running case.
 * 
 * @author Roly Rudy Gutierrez Pinto
 * @package engine.classes
 */
class pmDynaform
{

    public static $instance = null;
    public $fields = null;
    public $record = null;
    public $credentials = null;
    public $lang = null;
    public $langs = null;

    public function __construct($fields)
    {
        $this->fields = $fields;
        $this->getDynaform();
        $this->getCredentials();
        if (isset($this->fields["APP_UID"])) {
            //current
            $cases = new \ProcessMaker\BusinessModel\Cases();
        } else {
            //history
            $this->fields["APP_UID"] = null;
            if (isset($this->fields["APP_DATA"]["DYN_CONTENT_HISTORY"]))
                $this->record["DYN_CONTENT"] = $this->fields["APP_DATA"]["DYN_CONTENT_HISTORY"];
        }
    }

    public function getDynaform()
    {
        if ($this->record != null) {
            return $this->record;
        }
        $a = new Criteria("workflow");
        $a->addSelectColumn(DynaformPeer::DYN_VERSION);
        $a->addSelectColumn(DynaformPeer::DYN_LABEL);
        $a->addSelectColumn(DynaformPeer::DYN_CONTENT);
        $a->addSelectColumn(DynaformPeer::PRO_UID);
        $a->addSelectColumn(DynaformPeer::DYN_UID);
        $a->add(DynaformPeer::DYN_UID, $this->fields["CURRENT_DYNAFORM"], Criteria::EQUAL);
        $ds = ProcessPeer::doSelectRS($a);
        $ds->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $ds->next();
        $row = $ds->getRow();
        $this->record = isset($row) ? $row : null;
        $this->langs = ($this->record["DYN_LABEL"] !== "" && $this->record["DYN_LABEL"] !== null) ? G::json_decode($this->record["DYN_LABEL"]) : null;
        return $this->record;
    }

    public function getCredentials()
    {
        if ($this->credentials != null) {
            return $this->credentials;
        }
        $a = $this->clientToken();
        $this->credentials = array(
            "accessToken" => $a["access_token"],
            "expiresIn" => $a["expires_in"],
            "tokenType" => $a["token_type"],
            "scope" => $a["scope"],
            "refreshToken" => $a["refresh_token"],
            "clientId" => $a["client_id"],
            "clientSecret" => $a["client_secret"]
        );
        return $this->credentials;
    }

    public function jsonr(&$json)
    {
        foreach ($json as $key => $value) {
            $sw1 = is_array($value);
            $sw2 = is_object($value);
            if ($sw1 || $sw2) {
                $this->jsonr($value);
            }
            if (!$sw1 && !$sw2) {
                //property
                $prefixs = array("@@", "@#", "@%", "@?", "@$", "@=");
                if (is_string($value) && in_array(substr($value, 0, 2), $prefixs)) {
                    $triggerValue = substr($value, 2);
                    if (isset($this->fields["APP_DATA"][$triggerValue])) {
                        $json->$key = $this->fields["APP_DATA"][$triggerValue];
                    }
                }
                //query & options
                if ($key === "type" && ($value === "text" || $value === "textarea" || $value === "dropdown" || $value === "suggest" || $value === "checkbox" || $value === "radio" || $value === "datetime")) {
                    if (!isset($json->data)) {
                        $json->data = array(
                            "value" => "",
                            "label" => ""
                        );
                    }
                    if (!isset($json->dbConnection))
                        $json->dbConnection = "none";
                    if (!isset($json->sql))
                        $json->sql = "";
                    if (!isset($json->options))
                        $json->options = array();
                    else {
                        //convert stdClass to array
                        if (is_array($json->options)) {
                            $option = array();
                            foreach ($json->options as $valueOptions) {
                                array_push($option, array(
                                    "value" => $valueOptions->value,
                                    "label" => isset($valueOptions->label) ? $valueOptions->label : ""
                                ));
                            }
                            $json->options = $option;
                        }
                    }
                    if ($json->dbConnection !== "" && $json->dbConnection !== "none" && $json->sql !== "") {
                        $cnn = Propel::getConnection($json->dbConnection);
                        $stmt = $cnn->createStatement();
                        $rs = $stmt->executeQuery(strtoupper($json->sql), \ResultSet::FETCHMODE_NUM);
                        while ($rs->next()) {
                            $row = $rs->getRow();
                            $option = array(
                                "label" => $row[1],
                                "value" => $row[0]
                            );
                            array_push($json->options, $option);
                        }
                    }
                    if (isset($json->options[0])) {
                        $json->data = $json->options[0];
                    }
                }
                //data
                if ($key === "type" && ($value === "text" || $value === "textarea" || $value === "suggest" || $value === "dropdown" || $value === "checkbox" || $value === "radio" || $value === "datetime")) {
                    $json->data = array(
                        "value" => isset($this->fields["APP_DATA"][$json->name]) ? $this->fields["APP_DATA"][$json->name] : (is_array($json->data) ? $json->data["value"] : $json->data->value),
                        "label" => isset($this->fields["APP_DATA"][$json->name . "_label"]) ? $this->fields["APP_DATA"][$json->name . "_label"] : (is_array($json->data) ? $json->data["label"] : $json->data->label)
                    );
                }
                if ($key === "type" && ($value === "checkbox")) {
                    $json->data = array(
                        "value" => isset($this->fields["APP_DATA"][$json->name]) ? $this->fields["APP_DATA"][$json->name] : array(),
                        "label" => isset($this->fields["APP_DATA"][$json->name . "_label"]) ? $this->fields["APP_DATA"][$json->name . "_label"] : "[]"
                    );
                }
                if ($key === "type" && ($value === "file") && isset($this->fields["APP_DATA"]["APPLICATION"])) {
                    $oCriteria = new Criteria("workflow");
                    $oCriteria->addSelectColumn(AppDocumentPeer::APP_DOC_UID);
                    $oCriteria->addSelectColumn(AppDocumentPeer::DOC_VERSION);
                    $oCriteria->add(AppDocumentPeer::APP_UID, $this->fields["APP_DATA"]["APPLICATION"]);
                    $oCriteria->add(AppDocumentPeer::APP_DOC_FIELDNAME, $json->name);
                    $rs = AppDocumentPeer::doSelectRS($oCriteria);
                    $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                    $links = array();
                    while ($rs->next()) {
                        $row = $rs->getRow();
                        array_push($links, "../cases/cases_ShowDocument?a=" . $row["APP_DOC_UID"] . "&v=" . $row["DOC_VERSION"]);
                    }
                    $json->data = array(
                        "value" => $links,
                        "label" => isset($this->fields["APP_DATA"][$json->name . "_label"]) ? $this->fields["APP_DATA"][$json->name . "_label"] : "[]"
                    );
                }
                //grid
                if ($key === "type" && ($value === "grid")) {
                    if (isset($this->fields["APP_DATA"][$json->name])) {
                        //rows
                        $rows = $this->fields["APP_DATA"][$json->name];
                        foreach ($rows as $keyRow => $row) {
                            //cells
                            $cells = array();
                            foreach ($json->columns as $column) {
                                //data
                                if ($column->type === "checkbox" || $column->type === "text" || $column->type === "textarea" || $column->type === "dropdown" || $column->type === "datetime") {
                                    array_push($cells, array(
                                        "value" => isset($row[$column->name]) ? $row[$column->name] : "",
                                        "label" => isset($row[$column->name . "_label"]) ? $row[$column->name . "_label"] : ""
                                    ));
                                }
                                if ($column->type === "suggest") {
                                    array_push($cells, array(
                                        "value" => isset($row[$column->name . "_label"]) ? $row[$column->name . "_label"] : "",
                                        "label" => isset($row[$column->name]) ? $row[$column->name] : ""
                                    ));
                                }
                            }
                            $rows[$keyRow] = $cells;
                        }
                        $json->rows = count($rows);
                        $json->data = $rows;
                    }
                }
                //languages
                if ($this->lang === null && $key === "language" && isset($json->language)) {
                    $this->lang = $json->language;
                }
                if ($key === "label" && isset($json->label) && $this->langs !== null && isset($this->langs->{$this->lang})) {
                    $langs = $this->langs->{$this->lang}->Labels;
                    foreach ($langs as $langsValue) {
                        if ($json->label === $langsValue->msgid)
                            $json->label = $langsValue->msgstr;
                    }
                }
                if ($key === "title" && isset($json->title) && $this->langs !== null && isset($this->langs->{$this->lang})) {
                    $langs = $this->langs->{$this->lang}->Labels;
                    foreach ($langs as $langsValue) {
                        if ($json->title === $langsValue->msgid)
                            $json->title = $langsValue->msgstr;
                    }
                }
            }
        }
    }

    public function isResponsive()
    {
        return $this->record != null && $this->record["DYN_VERSION"] == 2 ? true : false;
    }

    public function printView()
    {
        ob_clean();
        $json = G::json_decode($this->record["DYN_CONTENT"]);
        $this->jsonr($json);
        $javascrip = "" .
                "<script type='text/javascript'>\n" .
                "var jsondata = " . G::json_encode($json) . ";\n" .
                "var pm_run_outside_main_app = null;\n" .
                "var dyn_uid = '" . $this->fields["CURRENT_DYNAFORM"] . "';\n" .
                "var __DynaformName__ = '" . $this->record["PRO_UID"] . "_" . $this->record["DYN_UID"] . "';\n" .
                "var app_uid = '" . $this->fields["APP_UID"] . "';\n" .
                "var prj_uid = '" . $this->fields["PRO_UID"] . "';\n" .
                "var step_mode = null;\n" .
                "var workspace = '" . SYS_SYS . "';\n" .
                "var credentials = " . G::json_encode($this->credentials) . ";\n" .
                "var filePost = null;\n" .
                "var fieldsRequired = null;\n" .
                "var triggerDebug = null;\n" .
                "$(window).load(function () {\n" .
                "    var data = jsondata;\n" .
                "    data.items[0].mode = 'disabled';\n" .
                "    window.project = new PMDynaform.core.Project({\n" .
                "        data: data,\n" .
                "        keys: {\n" .
                "            server: location.host,\n" .
                "            projectId: prj_uid,\n" .
                "            workspace: workspace\n" .
                "        },\n" .
                "        token: credentials,\n" .
                "        submitRest: false\n" .
                "    });\n" .
                "    $(document).find('form').submit(function (e) {\n" .
                "        e.preventDefault();\n" .
                "        return false;\n" .
                "    });\n" .
                "});\n" .
                "</script>\n";

        $file = file_get_contents(PATH_HOME . 'public_html/lib/pmdynaform/build/pmdynaform.html');
        $file = str_replace("{javascript}", $javascrip, $file);
        echo $file;
        exit();
    }

    public function printEdit()
    {
        ob_clean();
        $json = G::json_decode($this->record["DYN_CONTENT"]);
        $this->jsonr($json);
        if (!isset($this->fields["APP_DATA"]["__DYNAFORM_OPTIONS"]["PREVIOUS_STEP"])) {
            $this->fields["APP_DATA"]["__DYNAFORM_OPTIONS"]["PREVIOUS_STEP"] = "";
        }
        $title = "<table width='100%' align='center'>\n" .
                "    <tr class='userGroupTitle'>\n" .
                "        <td width='100%' align='center'>" . G::LoadTranslation('ID_CASE') . " #: " . $this->fields["APP_NUMBER"] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . G::LoadTranslation('ID_TITLE') . ": " . $this->fields["APP_TITLE"] . "</td>\n" .
                "    </tr>\n" .
                "</table>\n";
        $javascrip = "" .
                "<script type='text/javascript'>\n" .
                "var jsondata = " . G::json_encode($json) . ";\n" .
                "var pm_run_outside_main_app = '" . $this->fields["PM_RUN_OUTSIDE_MAIN_APP"] . "';\n" .
                "var dyn_uid = '" . $this->fields["CURRENT_DYNAFORM"] . "';\n" .
                "var __DynaformName__ = '" . $this->record["PRO_UID"] . "_" . $this->record["DYN_UID"] . "';\n" .
                "var app_uid = '" . $this->fields["APP_UID"] . "';\n" .
                "var prj_uid = '" . $this->fields["PRO_UID"] . "';\n" .
                "var step_mode = '" . $this->fields["STEP_MODE"] . "';\n" .
                "var workspace = '" . SYS_SYS . "';\n" .
                "var credentials = " . G::json_encode($this->credentials) . ";\n" .
                "var filePost = null;\n" .
                "var fieldsRequired = null;\n" .
                "var triggerDebug = " . ($this->fields["TRIGGER_DEBUG"] === 1 ? "true" : "false") . ";\n" .
                "</script>\n" .
                "<script type='text/javascript' src='/jscore/cases/core/cases_Step.js'></script>\n" .
                "<script type='text/javascript' src='/jscore/cases/core/pmDynaform.js'></script>\n" .
                ($this->fields["PRO_SHOW_MESSAGE"] === 1 ? '' : $title ) .
                "<div style='width:100%;padding:0px 10px 0px 10px;margin:15px 0px 0px 0px;'>\n" .
                "    <img src='/images/bulletButtonLeft.gif' style='float:left;'>&nbsp;\n" .
                "    <a id='dyn_backward' href='" . $this->fields["APP_DATA"]["__DYNAFORM_OPTIONS"]["PREVIOUS_STEP"] . "' style='float:left;font-size:12px;line-height:1;margin:0px 0px 1px 5px;'>\n" .
                "    " . $this->fields["APP_DATA"]["__DYNAFORM_OPTIONS"]["PREVIOUS_STEP_LABEL"] . "" .
                "    </a>\n" .
                "    <img src='/images/bulletButton.gif' style='float:right;'>&nbsp;\n" .
                "    <a id='dyn_forward' href='" . $this->fields["APP_DATA"]["__DYNAFORM_OPTIONS"]["NEXT_STEP"] . "' style='float:right;font-size:12px;line-height:1;margin:0px 5px 1px 0px;'>\n" .
                "    " . $this->fields["APP_DATA"]["__DYNAFORM_OPTIONS"]["NEXT_STEP_LABEL"] . "" .
                "    </a>\n" .
                "</div>";
        $file = file_get_contents(PATH_HOME . 'public_html/lib/pmdynaform/build/pmdynaform.html');
        $file = str_replace("{javascript}", $javascrip, $file);
        echo $file;
        exit();
    }

    public function printWebEntry($filename)
    {
        ob_clean();
        $json = G::json_decode($this->record["DYN_CONTENT"]);
        $this->jsonr($json);
        $javascrip = "" .
                "<script type='text/javascript'>\n" .
                "var jsondata = " . G::json_encode($json) . ";\n" .
                "var pm_run_outside_main_app = null;\n" .
                "var dyn_uid = '" . $this->fields["CURRENT_DYNAFORM"] . "';\n" .
                "var __DynaformName__ = null;\n" .
                "var app_uid = null;\n" .
                "var prj_uid = '" . $this->record["PRO_UID"] . "';\n" .
                "var step_mode = null;\n" .
                "var workspace = '" . SYS_SYS . "';\n" .
                "var credentials = " . G::json_encode($this->credentials) . ";\n" .
                "var filePost = '" . $filename . "';\n" .
                "var fieldsRequired = " . G::json_encode(array()) . ";\n" .
                "var triggerDebug = null;\n" .
                "</script>\n" .
                "<script type='text/javascript' src='/jscore/cases/core/pmDynaform.js'></script>\n" .
                "<div style='width:100%;padding: 0px 10px 0px 10px;margin:15px 0px 0px 0px;'>\n" .
                "    <img src='/images/bulletButton.gif' style='float:right;'>&nbsp;\n" .
                "    <a id='dyn_forward' href='' style='float:right;font-size:12px;line-height:1;margin:0px 5px 1px 0px;'>\n" .
                "        Next Step\n" .
                "    </a>\n" .
                "</div>";

        $file = file_get_contents(PATH_HOME . 'public_html/lib/pmdynaform/build/pmdynaform.html');
        $file = str_replace("{javascript}", $javascrip, $file);
        echo $file;
        exit();
    }

    public function printPmDynaform()
    {
        $json = G::json_decode($this->record["DYN_CONTENT"]);
        $this->jsonr($json);
        $javascrip = "" .
                "<script type='text/javascript'>" .
                "var jsonData = " . G::json_encode($json) . ";" .
                "</script>";

        $file = file_get_contents(PATH_HOME . 'public_html/lib/pmdynaform/build/pmdynaform.html');
        $file = str_replace("{javascript}", $javascrip, $file);

        echo $file;
        exit();
    }

    private function clientToken()
    {
        $client = $this->getClientCredentials();
        $authCode = $this->getAuthorizationCode($client);


        $request = array(
            'grant_type' => 'authorization_code',
            'code' => $authCode
        );
        $server = array(
            'REQUEST_METHOD' => 'POST'
        );
        $headers = array(
            "PHP_AUTH_USER" => $client['CLIENT_ID'],
            "PHP_AUTH_PW" => $client['CLIENT_SECRET'],
            "Content-Type" => "multipart/form-data;",
            "Authorization" => "Basic " . base64_encode($client['CLIENT_ID'] . ":" . $client['CLIENT_SECRET'])
        );

        $request = new \OAuth2\Request(array(), $request, array(), array(), array(), $server, null, $headers);
        $oauthServer = new \ProcessMaker\Services\OAuth2\Server();
        $response = $oauthServer->getServer()->handleTokenRequest($request);
        $clientToken = $response->getParameters();
        $clientToken["client_id"] = $client['CLIENT_ID'];
        $clientToken["client_secret"] = $client['CLIENT_SECRET'];

        return $clientToken;
    }

    protected $clientId = 'x-pm-local-client';

    protected function getClientCredentials()
    {
        $oauthQuery = new ProcessMaker\Services\OAuth2\PmPdo($this->getDsn());
        return $oauthQuery->getClientDetails($this->clientId);
    }

    protected function getAuthorizationCode($client)
    {
        \ProcessMaker\Services\OAuth2\Server::setDatabaseSource($this->getDsn());
        \ProcessMaker\Services\OAuth2\Server::setPmClientId($client['CLIENT_ID']);

        $oauthServer = new \ProcessMaker\Services\OAuth2\Server();
        $userId = $_SESSION['USER_LOGGED'];
        $authorize = true;
        $_GET = array_merge($_GET, array(
            'response_type' => 'code',
            'client_id' => $client['CLIENT_ID'],
            'scope' => implode(' ', $oauthServer->getScope())
        ));

        $response = $oauthServer->postAuthorize($authorize, $userId, true);
        $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=') + 5, 40);

        return $code;
    }

    private function getDsn()
    {
        list($host, $port) = strpos(DB_HOST, ':') !== false ? explode(':', DB_HOST) : array(DB_HOST, '');
        $port = empty($port) ? '' : ";port=$port";
        $dsn = DB_ADAPTER . ':host=' . $host . ';dbname=' . DB_NAME . $port;

        return array('dsn' => $dsn, 'username' => DB_USER, 'password' => DB_PASS);
    }

}
