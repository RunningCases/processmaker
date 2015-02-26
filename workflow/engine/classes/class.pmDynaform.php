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
    public $dyn_uid = null;
    public $record = null;
    public $app_data = null;
    public $items = array();
    public $data = array();
    public $variables = array();
    public $arrayFieldRequired = array();        

    public function __construct($dyn_uid, $app_data = array())
    {        
        $this->dyn_uid = $dyn_uid;
        $this->app_data = $app_data;
        $this->getDynaform();
        
        //items
        $dynContent = G::json_decode($this->record["DYN_CONTENT"]);
        if (isset($dynContent->items)) {
            $this->items = $dynContent->items[0]->items;
            $n = count($this->items);
            for ($i = 0; $i < $n; $i++) {
                $m = count($this->items[$i]);
                for ($j = 0; $j < $m; $j++) {
                    if (isset($this->items[$i][$j]->required) && $this->items[$i][$j]->required == 1) {
                        array_push($this->arrayFieldRequired, $this->items[$i][$j]->name);
                    }
                }
            }
        }

        if(!empty($app_data) && isset($app_data["APPLICATION"])){
            //data
            $cases = new \ProcessMaker\BusinessModel\Cases();
            $this->data = $cases->getCaseVariables($app_data["APPLICATION"]);
            
            //variables
            $this->variables = array();
            
            $a = new Criteria("workflow");
            $a->addSelectColumn(ProcessVariablesPeer::VAR_NAME);
            $a->addSelectColumn(ProcessVariablesPeer::VAR_SQL);
            $a->addSelectColumn(ProcessVariablesPeer::VAR_ACCEPTED_VALUES);
            $a->addSelectColumn(ProcessVariablesPeer::VAR_DBCONNECTION);

            $c3 = $a->getNewCriterion(ProcessVariablesPeer::VAR_ACCEPTED_VALUES, "", Criteria::ALT_NOT_EQUAL);
            $c2 = $a->getNewCriterion(ProcessVariablesPeer::VAR_ACCEPTED_VALUES, "[]", Criteria::ALT_NOT_EQUAL);
            $c2->addAnd($c3);

            $c4 = $a->getNewCriterion(ProcessVariablesPeer::PRJ_UID, $this->app_data["PROCESS"], Criteria::EQUAL);

            $c1 = $a->getNewCriterion(ProcessVariablesPeer::VAR_SQL, "", Criteria::ALT_NOT_EQUAL);
            $c1->addOr($c2);
            $c1->addAnd($c4);

            $a->add($c1);

            $ds = ProcessPeer::doSelectRS($a);
            $ds->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            while ($ds->next()) {
                $row = $ds->getRow();
                //options
                $rows2 = G::json_decode($row["VAR_ACCEPTED_VALUES"]);
                $n = count($rows2);
                for ($i = 0; $i < $n; $i++) {
                    $rows2[$i] = array($rows2[$i]->keyValue, $rows2[$i]->value);
                }
                //query
                $arrayVariable = array();
                if ($row["VAR_DBCONNECTION"] !== "none" && $row["VAR_SQL"] !== "") {
                    $cnn = Propel::getConnection($row["VAR_DBCONNECTION"]);
                    $stmt = $cnn->createStatement();
                    $rs = $stmt->executeQuery(\G::replaceDataField($row["VAR_SQL"], $arrayVariable), \ResultSet::FETCHMODE_NUM);
                    while ($rs->next()) {
                        array_push($rows2, $rs->getRow());
                    }
                }
                $this->variables[$row["VAR_NAME"]] = $rows2;
            }
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
    
    private function searchValues($varName, $value) 
    {
        if (!$varName || !isset($this->variables[$varName])) {
            return "";
        }
        $options = $this->variables[$varName];
        foreach ($options as $valueOptions) {
            if ($valueOptions[0] === $value) {
                return $valueOptions[1];
            }
        }
    }

    private function mergeAppData($app_uid, &$items)
    {
        foreach ($items as $key => $value) {
            if (is_array($items[$key])) {
                $this->mergeAppData($app_uid, $items[$key]);
            } else {
                if (isset($items[$key]->name) && isset($this->data[$items[$key]->name])) {
                    if ($items[$key]->type === "grid") {
                        $rows = $this->data[$items[$key]->name];
                        foreach ($rows as $keyRow => $row) {
                            $newRow = array();
                            foreach ($row as $keyCelda => $celda) {
                                array_push($newRow, array(
                                    "value" => $celda,
                                    "label" => $this->searchValues($keyCelda, $celda)
                                ));
                            }
                            $rows[$keyRow] = $newRow;
                        }
                        $items[$key]->rows = count($rows);
                        $items[$key]->data = $rows;
                    }
                    if ($items[$key]->type !== "grid") {
                        $value = $this->data[$items[$key]->name];
                        $label = "";
                        if (isset($this->data[$items[$key]->name . "_label"])) {
                            $value = $this->data[$items[$key]->name];
                            $label = $this->data[$items[$key]->name . "_label"];
                        }
                        if (isset($this->data[$items[$key]->name . "_value"])) {
                            $value = $this->data[$items[$key]->name . "_value"];
                            $label = $this->data[$items[$key]->name];
                        }
                        $items[$key]->data = array(
                            "value" => $value,
                            "label" => $label
                        );
                    }
                }
                if (isset($items[$key]->options) && isset($this->variables[$items[$key]->name])) {
                    $options = $this->variables[$items[$key]->name];
                    $n = count($options);
                    for ($i = 0; $i < $n; $i++) {
                        $options[$i] = array(
                            "value" => $options[$i][0],
                            "label" => $options[$i][1]
                        );
                    }
                    $items[$key]->options = $options;
                }
                if (isset($items[$key]->columns)) {
                    $this->mergeAppData($app_uid, $items[$key]->columns);
                }
            }
        }
    }
    
    public function mergeDynContentAppData($app_uid, &$items)
    {
        $dynContent = G::json_decode($this->record["DYN_CONTENT"]);
        if (isset($dynContent->items)) {
            $this->items = $dynContent->items[0]->items;
        }

        $this->mergeAppData($app_uid, $items);
        $dynContent->items[0]->items = $this->items;

        $a = G::json_encode($dynContent);
        $a = str_replace("\/", "/", $a);
        $this->record["DYN_CONTENT"] = $a;
    }

    public function isResponsive()
    {
        return $this->record != null && $this->record["DYN_VERSION"] == 2 ? true : false;
    }

    public function printView($pm_run_outside_main_app, $application)
    {
        ob_clean();
        $this->mergeDynContentAppData($application, $this->items);
        
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
        
        $file = file_get_contents(PATH_HOME . 'public_html/lib/pmdynaform/build/cases_Step_Pmdynaform_View.html');
        $file = str_replace("{JSON_DATA}", $this->record["DYN_CONTENT"], $file);
        $file = str_replace("{PM_RUN_OUTSIDE_MAIN_APP}", $pm_run_outside_main_app, $file);
        $file = str_replace("{DYN_UID}", $this->dyn_uid, $file);
        $file = str_replace("{DYNAFORMNAME}", $this->record["PRO_UID"] . "_" . $this->record["DYN_UID"], $file);
        $file = str_replace("{APP_UID}", $application, $file);
        $file = str_replace("{PRJ_UID}", $this->app_data["PROCESS"], $file);
        $file = str_replace("{WORKSPACE}", $this->app_data["SYS_SYS"], $file);
        $file = str_replace("{credentials}", json_encode($clientToken), $file);
        echo $file;
        exit();
    }

    public function printEdit($pm_run_outside_main_app, $application, $headData, $step_mode = 'EDIT')
    {
        ob_clean();
        $this->mergeDynContentAppData($application, $this->items);
        
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

        $file = file_get_contents(PATH_HOME . 'public_html/lib/pmdynaform/build/cases_Step_Pmdynaform.html');
        $file = str_replace("{JSON_DATA}", $this->record["DYN_CONTENT"], $file);
        $file = str_replace("{CASE}", $headData["CASE"], $file);
        $file = str_replace("{APP_NUMBER}", $headData["APP_NUMBER"], $file);
        $file = str_replace("{TITLE}", $headData["TITLE"], $file);
        $file = str_replace("{APP_TITLE}", $headData["APP_TITLE"], $file);
        $file = str_replace("{PM_RUN_OUTSIDE_MAIN_APP}", $pm_run_outside_main_app, $file);
        $file = str_replace("{DYN_UID}", $this->dyn_uid, $file);
        $file = str_replace("{DYNAFORMNAME}", $this->record["PRO_UID"] . "_" . $this->record["DYN_UID"], $file);
        $file = str_replace("{APP_UID}", $application, $file);
        $file = str_replace("{PRJ_UID}", $this->app_data["PROCESS"], $file);
        $file = str_replace("{STEP_MODE}", $step_mode, $file);
        $file = str_replace("{WORKSPACE}", $this->app_data["SYS_SYS"], $file);
        $file = str_replace("{PORT}", $_SERVER["SERVER_PORT"] , $file); 
        $file = str_replace("{credentials}", G::json_encode($clientToken), $file);
        echo $file;
        exit();
    }

    public function printWebEntry($filename)
    {
        ob_clean();
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
        $file = file_get_contents(PATH_HOME . 'public_html/lib/pmdynaform/build/WebEntry_Pmdynaform.html');
        $file = str_replace("{JSON_DATA}", $this->record["DYN_CONTENT"], $file);        
        $file = str_replace("{DYN_UID}", $this->dyn_uid, $file);
        $file = str_replace("{PRJ_UID}",$this->record["PRO_UID"], $file);
        $file = str_replace("{WORKSPACE}", SYS_SYS, $file);
        $file = str_replace("{FILEPOST}", $filename, $file);
        $file = str_replace("{PORT}", $_SERVER["SERVER_PORT"] , $file);                
        $file = str_replace("{credentials}", G::json_encode($clientToken), $file);
        $file = str_replace("{FIELDSREQUIRED}", G::json_encode($this->arrayFieldRequired), $file);        
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
