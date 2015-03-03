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
    private $debugMode = false;
    public $dyn_uid = null;
    public $record = null;
    public $app_data = null;
    public $credentials = null;
    public $items = array();
    public $data = array();
    public $variables = array();
    public $arrayFieldRequired = array();

    public function __construct($dyn_uid, $app_data = array())
    {
        $this->dyn_uid = $dyn_uid;
        $this->app_data = $app_data;
        $this->getDynaform();
        $this->getCredentials();
        if (isset($app_data["APPLICATION"])) {
            $cases = new \ProcessMaker\BusinessModel\Cases();
            $this->data = $cases->getCaseVariables($app_data["APPLICATION"]);
        }
    }

    public function getDynaform()
    {
        if ($this->record != null) {
            return $this->record;
        }
        $a = new Criteria("workflow");
        $a->addSelectColumn(DynaformPeer::DYN_VERSION);
        $a->addSelectColumn(DynaformPeer::DYN_CONTENT);
        $a->addSelectColumn(DynaformPeer::PRO_UID);
        $a->addSelectColumn(DynaformPeer::DYN_UID);
        $a->add(DynaformPeer::DYN_UID, $this->dyn_uid, Criteria::EQUAL);
        $ds = ProcessPeer::doSelectRS($a);
        $ds->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $ds->next();
        $row = $ds->getRow();
        $this->record = isset($row) ? $row : null;
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
                    if (isset($this->app_data[$triggerValue])) {
                        $json->$key = $this->app_data[$triggerValue];
                    }
                }
                //data
                if ($key === "type" && ($value === "text" || $value === "textarea" || $value === "dropdown")) {
                    $json->data = array(
                        "value" => isset($this->data[$json->name]) ? $this->data[$json->name] : "",
                        "label" => isset($this->data[$json->name . "_label"]) ? $this->data[$json->name . "_label"] : ""
                    );
                }
                if ($key === "type" && ($value === "suggets")) {
                    $json->data = array(
                        "value" => isset($this->data[$json->name . "_label"]) ? $this->data[$json->name . "_label"] : "",
                        "label" => isset($this->data[$json->name]) ? $this->data[$json->name] : ""
                    );
                }
                //query & options
                if ($key === "type" && ($value === "text" || $value === "textarea" || $value === "dropdown" || $value === "suggets")) {
                    if (!isset($json->dbConnection))
                        $json->dbConnection = "none";
                    if (!isset($json->sql))
                        $json->sql = "";
                    if (!isset($json->options))
                        $json->options = array();
                    if ($json->dbConnection !== "none" && $json->sql !== "") {
                        $cnn = Propel::getConnection($json->dbConnection);
                        $stmt = $cnn->createStatement();
                        $rs = $stmt->executeQuery(\G::replaceDataField($json->sql, array()), \ResultSet::FETCHMODE_NUM);
                        while ($rs->next()) {
                            $row = $rs->getRow();
                            $option = array(
                                "label" => $row[1],
                                "value" => $row[0]
                            );
                            array_push($json->options, $option);
                        }
                        $json->data = isset($json->options[0]) ? $json->options[0] : $json->data;
                    }
                }
                //grid
                if ($key === "type" && ($value === "grid")) {
                    if (isset($this->data[$json->name])) {
                        //rows
                        $rows = $this->data[$json->name];
                        foreach ($rows as $keyRow => $row) {
                            //cells
                            $cells = array();
                            foreach ($json->columns as $column) {
                                //data
                                if ($column->type === "text" || $column->type === "textarea" || $column->type === "dropdown") {
                                    array_push($cells, array(
                                        "value" => $row[$column->name],
                                        "label" => $row[$column->name . "_label"]
                                    ));
                                }
                                if ($column->type === "suggest") {
                                    array_push($cells, array(
                                        "value" => $row[$column->name . "_label"],
                                        "label" => $row[$column->name]
                                    ));
                                }
                            }
                            $rows[$keyRow] = $cells;
                        }
                        $json->rows = count($rows);
                        $json->data = $rows;
                    }
                }
            }
        }
    }

    public function isResponsive()
    {
        return $this->record != null && $this->record["DYN_VERSION"] == 2 ? true : false;
    }

    public function printView($pm_run_outside_main_app, $application)
    {
        ob_clean();
        $json = G::json_decode($this->record["DYN_CONTENT"]);
        $this->jsonr($json);

        $a = $this->clientToken();
        $clientToken = array(
            "accessToken" => $a["access_token"],
            "expiresIn" => $a["expires_in"],
            "tokenType" => $a["token_type"],
            "scope" => $a["scope"],
            "refreshToken" => $a["refresh_token"],
            "clientId" => $a["client_id"],
            "clientSecret" => $a["client_secret"]
        );

        $javascrip = "" .
                "<script type='text/javascript'>\n" .
                "var jsondata = " . G::json_encode($json) . ";\n" .
                "var pm_run_outside_main_app = '" . $pm_run_outside_main_app . "';\n" .
                "var dyn_uid = '" . $this->dyn_uid . "';\n" .
                "var __DynaformName__ = '" . $this->record["PRO_UID"] . "_" . $this->record["DYN_UID"] . "';\n" .
                "var app_uid = '" . $application . "';\n" .
                "var prj_uid = '" . $this->app_data["PROCESS"] . "';\n" .
                "var step_mode = null;\n" .
                "var workspace = '" . $this->app_data["SYS_SYS"] . "';\n" .
                "var credentials = " . G::json_encode($clientToken) . ";\n" .
                "var filePost = null;\n" .
                "var fieldsRequired = null;\n" .
                "$(window).load(function () {\n" .
                "    var data = jsondata;\n" .
                "    data.items[0].mode = 'view';\n" .
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

        $this->debug();
        echo $file;
        exit();
    }

    public function printEdit($pm_run_outside_main_app, $application, $headData, $step_mode = 'EDIT')
    {
        ob_clean();
        $json = G::json_decode($this->record["DYN_CONTENT"]);
        $this->jsonr($json);
        $title = "<table width='100%' align='center'>\n" .
                "    <tr class='userGroupTitle'>\n" .
                "        <td width='100%' align='center'>" . $headData["CASE"] . " #: " . $headData["APP_NUMBER"] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $headData["TITLE"] . ": " . $headData["APP_TITLE"] . "</td>\n" .
                "    </tr>\n" .
                "</table>\n";
        $javascrip = "" .
                "<script type='text/javascript'>\n" .
                "var jsondata = " . G::json_encode($json) . ";\n" .
                "var pm_run_outside_main_app = '" . $pm_run_outside_main_app . "';\n" .
                "var dyn_uid = '" . $this->dyn_uid . "';\n" .
                "var __DynaformName__ = '" . $this->record["PRO_UID"] . "_" . $this->record["DYN_UID"] . "';\n" .
                "var app_uid = '" . $application . "';\n" .
                "var prj_uid = '" . $this->app_data["PROCESS"] . "';\n" .
                "var step_mode = '" . $step_mode . "';\n" .
                "var workspace = '" . $this->app_data["SYS_SYS"] . "';\n" .
                "var credentials = " . G::json_encode($this->credentials) . ";\n" .
                "var filePost = null;\n" .
                "var fieldsRequired = null;\n" .
                "</script>\n" .
                "<script type='text/javascript' src='/jscore/cases/core/cases_Step.js'></script>\n" .
                "<script type='text/javascript' src='/jscore/cases/core/pmDynaform.js'></script>\n" .
                ($this->app_data["PRO_SHOW_MESSAGE"] === 1 ? '' : $title ) .
                "<div style='width:100%;padding:0px 10px 0px 10px;margin:15px 0px 0px 0px;'>\n" .
                "    <img src='/images/bulletButtonLeft.gif' style='float:left;'>&nbsp;\n" .
                "    <a id='dyn_backward' href='' style='float:left;'>\n" .
                "    </a>\n" .
                "    <img src='/images/bulletButton.gif' style='float:right;'>&nbsp;\n" .
                "    <a id='dyn_forward' href='' style='float:right;font-size:12px;line-height:1;margin:0px 5px 1px 0px;'>\n" .
                "        Next Step\n" .
                "    </a>\n" .
                "</div>";

        $file = file_get_contents(PATH_HOME . 'public_html/lib/pmdynaform/build/pmdynaform.html');
        $file = str_replace("{javascript}", $javascrip, $file);

        $this->debug();
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
                "var dyn_uid = '" . $this->dyn_uid . "';\n" .
                "var __DynaformName__ = null;\n" .
                "var app_uid = null;\n" .
                "var prj_uid = '" . $this->record["PRO_UID"] . "';\n" .
                "var step_mode = null;\n" .
                "var workspace = '" . SYS_SYS . "';\n" .
                "var credentials = " . G::json_encode($this->credentials) . ";\n" .
                "var filePost = '" . $filename . "';\n" .
                "var fieldsRequired = " . G::json_encode($this->arrayFieldRequired) . ";\n" .
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

        $this->debug();
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

        $this->debug();
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

    private function debug()
    {
        if ($this->debugMode) {
            echo "<pre>";
            echo G::json_encode($json);
            echo "</pre>";
        }
    }

}
