<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Services\OAuth2;

use \OAuth2\Response as OAuth2Response;
use ProcessMaker\Model\LicenseManager;
use ProcessMaker\Model\OauthClients;
use ProcessMaker\Model\User;
use ProcessMaker\Services\OAuth2\Server;
use Tests\MockPhpStream;
use Tests\TestCase;

class ServerTest extends TestCase
{
    private $server;

    /**
     * Setup method.
     */
    public function setUp()
    {
        parent::setUp();
        $this->server = $_SERVER;
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->createTestLicense();
    }

    /**
     * Teardown method.
     */
    public function tearDown()
    {
        parent::tearDown();
        $_SERVER = $this->server;
    }

    /**
     * Test the "postToken()" method with valid credentials.
     * @test
     * @covers \ProcessMaker\Services\OAuth2\Server::postToken()
     */
    public function it_should_test_post_token_with_valid_credentials()
    {
        $user = User::where('USR_ID', '=', 1)->get()->first();
        $oauthClients = factory(OauthClients::class)->create([
            "USR_UID" => $user->USR_UID
        ]);

        $data = '{
            "grant_type":"password",
            "scope":"*",
            "client_id":"' . $oauthClients->CLIENT_ID . '",
            "client_secret":"' . $oauthClients->CLIENT_SECRET . '",
            "username":"' . $user->USR_USERNAME . '",
            "password":"admin"
        }';
        stream_wrapper_unregister("php");
        stream_wrapper_register("php", "Tests\MockPhpStream");
        file_put_contents('php://input', $data);

        list($host, $port) = strpos(DB_HOST, ':') !== false ? explode(':', DB_HOST) : [DB_HOST, ''];
        $port = empty($port) ? '' : ";port=$port";

        Server::setDatabaseSource(DB_USER, DB_PASS, DB_ADAPTER . ":host=$host;dbname=" . DB_NAME . $port);

        $server = new Server();
        $server->postToken();
        stream_wrapper_restore("php");
        $response = ob_get_contents();
        ob_clean();
        $response = json_decode($response, true);
        $this->assertNotEmpty($response);
    }

    /**
     * Test the method "postToken()" with the return parameter of the object "OAuth2\Response".
     * @test
     * @covers \ProcessMaker\Services\OAuth2\Server::postToken()
     */
    public function it_should_test_post_token_with_return_handle_token()
    {
        $user = User::where('USR_ID', '=', 1)->get()->first();
        $oauthClients = factory(OauthClients::class)->create([
            "USR_UID" => $user->USR_UID
        ]);

        $data = '{
            "grant_type":"password",
            "scope":"*",
            "client_id":"' . $oauthClients->CLIENT_ID . '",
            "client_secret":"' . $oauthClients->CLIENT_SECRET . '",
            "username":"' . $user->USR_USERNAME . '",
            "password":"admin"
        }';
        stream_wrapper_unregister("php");
        stream_wrapper_register("php", "Tests\MockPhpStream");
        file_put_contents('php://input', $data);

        list($host, $port) = strpos(DB_HOST, ':') !== false ? explode(':', DB_HOST) : [DB_HOST, ''];
        $port = empty($port) ? '' : ";port=$port";

        Server::setDatabaseSource(DB_USER, DB_PASS, DB_ADAPTER . ":host=$host;dbname=" . DB_NAME . $port);

        $server = new Server();
        $result = $server->postToken(null, true);
        stream_wrapper_restore("php");

        $this->assertTrue($result instanceof OAuth2Response);
    }

    /**
     * Test the method "postToken()" with empty clientId.
     * @test
     * @covers \ProcessMaker\Services\OAuth2\Server::postToken()
     */
    public function it_should_test_post_token_with_empty_client_id()
    {
        $user = User::where('USR_ID', '=', 1)->get()->first();
        $oauthClients = factory(OauthClients::class)->create([
            "USR_UID" => $user->USR_UID
        ]);

        $data = '{
            "grant_type":"password",
            "scope":"*",
            "client_id":"",
            "client_secret":"",
            "username":"' . $user->USR_USERNAME . '",
            "password":"admin"
        }';
        stream_wrapper_unregister("php");
        stream_wrapper_register("php", "Tests\MockPhpStream");
        file_put_contents('php://input', $data);

        list($host, $port) = strpos(DB_HOST, ':') !== false ? explode(':', DB_HOST) : [DB_HOST, ''];
        $port = empty($port) ? '' : ";port=$port";

        Server::setDatabaseSource(DB_USER, DB_PASS, DB_ADAPTER . ":host=$host;dbname=" . DB_NAME . $port);

        $server = new Server();
        $server->postToken();
        stream_wrapper_restore("php");
        $response = ob_get_contents();
        ob_clean();
        $response = json_decode($response, true);
        $this->assertArrayHasKey("error", $response);
        $this->assertArrayHasKey("error_description", $response);
    }

    /**
     * Test the method "postToken()" for the x-pm-local-client.
     * @test
     * @covers \ProcessMaker\Services\OAuth2\Server::postToken()
     */
    public function it_should_test_post_token_for_pm_client_id()
    {
        $user = User::where('USR_ID', '=', 1)->get()->first();
        $oauthClients = OauthClients::where('CLIENT_ID', '=', 'x-pm-local-client')->get()->first();

        $data = '{
            "grant_type":"password",
            "scope":"*",
            "client_id":"' . $oauthClients->CLIENT_ID . '",
            "client_secret":"' . $oauthClients->CLIENT_SECRET . '",
            "username":"' . $user->USR_USERNAME . '",
            "password":"admin"
        }';
        stream_wrapper_unregister("php");
        stream_wrapper_register("php", "Tests\MockPhpStream");
        file_put_contents('php://input', $data);

        list($host, $port) = strpos(DB_HOST, ':') !== false ? explode(':', DB_HOST) : [DB_HOST, ''];
        $port = empty($port) ? '' : ";port=$port";

        Server::setDatabaseSource(DB_USER, DB_PASS, DB_ADAPTER . ":host=$host;dbname=" . DB_NAME . $port);
        Server::setPmClientId('x-pm-local-client');
        $server = new Server();
        $server->postToken();
        stream_wrapper_restore("php");
        $response = ob_get_contents();
        ob_clean();
        $response = json_decode($response, true);
        $this->assertNotEmpty($response);
    }

    /**
     * Create test license
     */
    private function createTestLicense()
    {
        if (!is_dir(PATH_DB)) {
            mkdir(PATH_DB);
        }
        if (!is_dir(PATH_WORKSPACE)) {
            mkdir(PATH_WORKSPACE);
        }
        $info = $this->getLicenseInfo();
        $filename = PATH_WORKSPACE . $info['name'];
        $data = $info['content'];
        file_put_contents($filename, $data);

        LicenseManager::truncate();
        factory(LicenseManager::class)->create([
            "LICENSE_DATA" => $data,
            "LICENSE_PATH" => $filename,
            "LICENSE_WORKSPACE" => env('MAIN_SYS_SYS')
        ]);
    }

    /**
     * Get test license information.
     * @return array
     */
    private function getLicenseInfo()
    {
        $content = "-------------------------------BEGIN LICENSE KEY--------------------------------
H5DcVc5NGsrakl0SXVSYjN1M2NJTEliR2VBbkdOcWI1aXRwR3FBZFlwMGRxMTNkbmgybDVLM3o1V1plS
2QzZW9KamlvZXpabGkwMW41NmdXT0ZsY1Y1bFpUT2pYbVF2N21CZVlCcHc4ZDRxcGg3Z09PQmRYaXdhV
ytDb0lXSFk2YmRmWHFFV2FCN2xxUlVwcnVmWXBGMjBtZWV3R3BzajIrTmxwaHFjTGVUb28rY3A1eWpkO
FZ3Z3RLbGJvS2NZM2QzaDd0amNxcCtoWDJBd3JlQWVYTnZnNW0raG9tUmtZdVpzSGFvbTU5MGhOTENrb
2VVaG1PUDVHNmtpNmQxaWFXUWxacUZjbWU1NFpxR1pwUzR0SGg0ZnBsOGk2TENuS1I0dmNtSWs0dUFtV
TJReVd5Y2ZaOVRvY084czZPUW42WEV3S2wydDdlaVpwVGdqNnVTa1c5MXlLR2JwSDk1b01HSGZJaUd6c
UNGaXBHWVpvRzZmbitCWkh6V3Q5dVhucU95cDJtTXBwTi9oMjFZdkxpWG1wYU9ocWJRZ0xTc3diS1VsN
UdMYVgrNWdhQ3RXWHAvekgrcmZYNW11bzZKbzcyYW5JWEt2WWlZb0lDUWNIZVJtNlY1b0tTUmhvWi9oW
kNGakttY2xZMm1wNGlKcThPZXZaNjJtWE8xazVOM2JhZUltbnRWZXBqd1lZdXVwY0JtZ2JwK2VIaDhWN
lNydkllRWo0bVlwcVI0cTZTWWRudll0NTJXakdOOHZhdGxyc3luaFgyUVpvbWNsNXFXdmJpSnQ1ZWVsO
UNRaDRuV2pZK2ZzNnlqZEsydmoyaUZxb2RjanBCVXI2M2Jsc1BEeExMSXU2UlpxN0s2dTcyM28yYVU0S
StyaTVGdm1kaThyTWVUcUxPMnVxT2JWYlcwY1hsK2RXYUJ1bjUzZkh4WHBLdThoNFNQaVppbW9YK3VsW
mlCV0xURWZuZCtlN3k5cTJTdXpLZUVkcEJtdmJDeXFyYm41b3FyazN1QTQ0RjFmK3B6YUlDb2I4ZWd3d
G0xc2IybnJvNjZvNVBacWN5V3hMbkN1WVdJbVhPSGlMdUNpWUpYdE1iQW9idDdrclpzcG9tNmoydHVjT
kt1aFp1ajZiT3J2R2FPcllCN2Y3bUJlMitEeHRhRHBMbXB1SnpCVlpTM2lXaHg3SjEyZVlGanNjRzZvd
VBPNFplMXVibXpyTEs2bk9EZnBybW92cmVjZ3ExK3JYU3BpWjkva1ZYYjE0bTZ1b1M4bWI3S29kMXFvS
nFSaTQrNG5YNWxjM2U5dFkzQ3NKYXl2Tks1eHNpK3RxQ1ZpYkNQYW0vQm5ISmxjRlhxcm5xNHNzR3BxY
nV6dUxwa2NNcVFvbTZvZkhWNWdYT2p4cEd3cHB1NjFhZXVzTGVtZHF5ZHJZcWRqcm1RZFh1QlpyYTBzd
Wlxd2JLK3VPMlp1YkRjckZpSzE0ZUlaS2JkZlhkN2NXOTV0SitndXIzWm5jYkR2MmVldG1wcWg0aTdnb
3lDVjdLKzVLZmZ4Y0N3Vks2M2dZWmxiOEdjYzJOd1ZkKzVxNjYyd2FXeW1hbXRzTFdwMDdmZG5LU3dab
0d3aTJTTmljSnRaNitkWm91MXRhYkc0YWJjek5LbXE3ZTJxYTltZ2JDcXBHeVR0NU4yc29GbXFOaXRsN
khUdmNhbDM5T3hyWjZtdkpmSWVHM1VncFpua3NlT2RwdUhVcHE1dzZtMnQ2MlppTHJndmR6SXVMK1c1b
kNDMHFWdWdKaDdWWG1JelpXR2xvbWxvNUtRaDR1VmxYcWx0YTk0ZHBhWm1JeWtWWlN2aVdSdHM5NnRnS
GQ4dEk2aVpLMTc1THkwdXJPOXVwZVpscEt1b0pKMWxMaTBlSHQrbWE2cHRPQ1B1S2JRenBldnQ1eW5tc
083VktheG4yT1N4NDUybW9kU3JzaXl1b3ExdTVxb3BjN0Q0YzY0dEplVmliQ1BaVy9CbkhKc2NGWHR0Y
W1xdUxpcnI2cUlwN3FxbDlDMzI1ZW9aSCt2Z1lWdXpJaUNaM0NiMXFtMHE0YXV0ZHFndWN2Y3dJcS9xc
XlzdHF1MTVMaWt1YTNGbU4rNXVxbnByRmlLMTRlTWJ0NmtkWGVFV2NDUXdidVYzN0hibHFEQnhMVFZ3W
Fd4eGJ5NnZIYURubjZQcU1pMGk0bUhWT2F6cXNxa21hUFZwcVdHbE8yMHJyaTJ0NHludXF4b2dxdHZtS
khjYldkMmZtaTZ0cGJJdkxOM2wrM0VwcWU2cG9URDM2TFl2T0c4dGNSbWdiQitmb0xqcldpTGZudTE1N
DZ6czk2bG01UGd0czJZamFXc2dJTnl3R1dNa0ZUYnRheWV1TDNBWjU2MmFtcUZpTHVDaFg1dlpzbmFtT
25IeXJ5ZTNMS295WmVZa2E1amJwOXRxM0p5dkg2RmRJQnBzYnV6dHA3UndzNTRvcU90c3BxMnBjK3p3Y
VpZdE14K2QzbDh0STZpWnExNzJzS292N0NyamEycnM5VG1XWk90azNhdGdyZCtxR3R3Y2RxeHVLT3N6c
m1udUpxeWozZVJtNlY1bVd6S2pvVjJuVytUcUxteW02dTF0cU9weTQrUTQ1T0lnbTNtaUgrUFZKWER4c
XFuZ3FMaFkzS3lmb1I2Z2JwK2QzdDhWOEs1M1p5a3NMZUl3SmFndXJlN1ZYSDIxbjU0ZVh0am9McDN1S
2ZBbUlxMWlvdUltSnVadGNhV3BJMnNtWnlDdDM2b2FXdC9wWWQ1bEtXY2ZjRzljWDViajNpbTJyekduW
nJEeWJQWGIydWlqMzUvZzhlQ2EzNTd6cm5leU1YQVZLNnZnWVpwYnNuTGUyTnhsTFJ5Ym9PL3huNTZnV
2F0dkt1Wmc1SGNiV2gwZm1oM2dXT0pmbjlqWnFtVGRIWjNjWEdFb1dTamlaMkRkb1owZG5kMWRuZWhwR
m1UdDVONXRHbXlwZVNlV0lyaGg0aGpwWXk2cjdpYnZLTElxWVc2YXFDa2tZaU9aOUcyazZSM2lMdUNoW
Gh2WnREV3c5N0l6c0NGeHAxcGtLVnVmNU43VlpxWTdhU3BzclRIcmJXMVpvRzZmR2VXa0l1R2lKRmt2Y
kRGbTNtdnZWT0N2YVNVWnJlenNNcmFtTmpMajQ2NWtIaUFhYmkvdDlXVmNzdCtrWCtjcmFtbDY2Nm90T
0Z2a3FhbG0zV0FiS215bDdxM3BkQ255WkxMdVhhQTFvZGhjbzl2ZW5pRmZXSjBqNXFHczNtSWdHeW5mN
EdGWWxhSjFYdHFjRlh3cHFtOHJjS3lhSUszZ0g1OFY2ZS80YWVxdEtsb2dzUnRpb0NKVmFqZXo2bW51c
WFneU9xazJIdW94b0NHZm1ocGY3bUJvYVZ4ZXFqSXZPaXpzNlhibUtPem8yK1NwcVdhZldoc2NzQmxpc
EJVMjdyT2xMeDJqNm1kZld1c2o0YUNhcmkzckxMRjNMYmV6SG1JbTYxL2VKQ2xib1NjWTZXWHArT3Zub
XQvdDM1MmdyZUFlSFZ2ZzhuZWxhaWx0cSszeFp6SXZNSlZjZUtkZElHNmUzcU9rNlRvdTltOHViNnB1R
2wvdVlHaHBuRjZsTXUwM2F5M3QrU2FvYlRnYlhsdTNxUjBkNFJac0oyNnQ2YlFwOG1TeTdsMmdOYUhZW
EtQYjNwNGhYMWlkSSthaHJONWlJQnNwMytCaFdKV2lkVjdaR2R0bkxhbnJhWEhxYVdycGJxc1pIRFVrS
nBzYjJSMmRuaUdZSW1FZkdSdm1aUjNnSHR5ZTRTaFZxN01wNG1BZUxlNnFMaTd1cEt1cXBKOWsyZmJ2Y
Vd0NDVxWXU5TnZrckRVcEhTQnEzRitZby9ScGFWOG4xTyt5YjJwaFlpamM0aC9nbXFFZUdWMGlaMkZxb
21IZldLamZuZUZZbVIra25GalptT3FjV2Q2ZElOMWVtbC91WUYyYjRQRXlxQ2FaSCs1Z1lKcWszREVwc
HZycGFXNXJLV1Z2ZDZaemNqYnVHaVJ0NEI3Zm1pMTJkYWllbi9NZjYxNGZtYk1ySnZCbTYrNHB0RE9ZN
XF6cExKTHI4V2cwR2l5a3NXMXU2clFzcDZ0ZDRpN2dvVjViMmE5MHNqZHk4QzlwdHk5dFhkdHA0aVRlM
VZqVmJXMGNYMStkYmkvdDZsb2dyVnZtWkNMbVpxanVMdTV0cVo3aWNKdFo2dWRacmlzcmFhMTVKblN2Y
zdIcTNoL3VZRjFmNEdTcFdlSmVZWjJySFIwZDVkcVo0bWhmWkZqbTR4K3VZUnVoMDNMdTZUZXNkU2ZlW
S9IZjVxSFVuKyt4Ynk5eHExWGY4eW5ocXlUZWIrWDM3T295SmVUd3R1eG1GaHU3WHRuZzJaMWY3bUJkW
GlCWkpuUXpkZWZwS09vcGJTMWFIdUp3bTFtczRWbWdicDdkbzZUcE9YQzBMaG9rYWlBZDMrNWdhbXRXY
nl6MExQbXRxV282bHR4dUtoOWtxYWxvSDFvdkpqQmxNTzlWS2FzbjJHU3g0NTJsb2RTck1xdnU2dkdzY
Vc0d3R6RDdYdVN0bXlqaWJxUGEyNXcwcmFWb3B6dHFaeTdabzYzZ0hoM2dHbVNwOUM1enFhb3I2V3hyT
U5UZTRuQ2JXZXFuV2FwdWFhaXlOYVQxN3JodUdpUnQ0QjRmWUJwb3FOb2pYR0tkNmQzZDJTb2FuQ0Nub
2VIWTQybHRvQjdhSWROeXNhV3pMektrTHUxeUtxRmlLTnpob2FDYW9aNFpubUdub2VuaVlwdFk2U0llb
1ZzWkg2RWZLWndhYlJqcXIybHg3bTVhWCs1Z1h0dmc3ZmZsSjZ1cGFpenRsV1V5N2h0YUxURWZuZCtlN
3pIcTJpdGU5VElyN3BtZ2JwK2VYbXFsV2VJZElsMXFuZDBkS2RwWm4rZWZZZGptNXB6ZG5wbmZWdUZob
U9iZUpaa2VZL0hmNWVIVXFlMnVxMXFqN3R2ZFpDbmQrL012TDkwMU1Hc3VYNlZ2TW0ybEoyWW5IeXFnM
2lOWnJTd3A3RnBmYWliaWFKdFY1ZTNxN2wrbGJyQnRKZFd4Y1N5cmJ5aXFMbVJoOWpONGJ5MHZiZG1sS
1cwcU5mWXBMMnl6V2UxdW41MXFITllzOVBBdXFYVTJyZXZ1YVZ2WnNpUVk2VnFrbE9TeDQ1NW5XK2tzc
1d5YW9QSGdtMStlOU82MjgzTXY1Zm1jSUxJYkdXQW5HT2xtNS9mb3FxdW83ZWx1cXhtZ2JwOFpwcVFpM
lZsYzNsemVJTmdpWUZ2WkdlemxuU0FkM0ZqaitSdXFwT1B5YXZJdDYrMnNtaUM0NjF1a21hZnJ2Szd1Y
mJjVzNIQ3FINkpiWTNjcUxLdm1NQ1F0TXFyMjYySGJNcU9oSCtGYjJ1c2ozNTZnbmFzcEx2SDJjVGJ2Y
mE2bHFod2dzaHNaSWlFWTI2cGJhOTdXYm0ydktlcmFYK3FnWEp3MUpDaWJWZW1zNzIxdmFLNnNzSlZjZ
UtkZElHNmUzZU9rNmJVemRiQnJYaC9xb0YwZ2JxcXBHcVNac3k2M0xxbnR1Q3BxcmpkdThwVnB0Tjlkb
1dxaDJTUGVLTGdxdEdheXJ5NXQ0V0lvM09HZ0lKcXBMcWtwNzdneU9lNndyS2trM0NDeUd4bGY1eGpsc
WlZMjdXY3FLaTB1S3RwZjdtQmMyNmJlSnRqWm5keGQzbCtZNHh1Z0dSd3JKTitkbmRqZk1lclphU1RqO
GkydXFXNnJLT3FxT1RZV1pPM2szYXpnV1oycDJwcmZKOS9oR09laW5SM2hHcDlaWVdHVkthN24yZVJkc
2U1eE1HbHJIZUl1NEtOZ2xlbHo4Nis1cnE1dVplVmljUytiR2VKdzN0a2JXMzF0SEY5Zm5XcnU3Q29hS
UsxYjVTSW8xVmxjblIyZDRGamlYNS9ZMmFwazNSMmQzRnhoS0ZrbzRtZGczYUhkSFozZW1pQzQ2MXJrb
WJIcHVlc1puL3FjMmVJcUcvS3A5M0x0NnV4b0xCdnRzbWF6YmZHbzd2SGRvRFdoMlJ6ZDd1eHE3OXFjT
GVUbjRXMGU2ckJwTlRDckx5YmwyNm1vcWFlbGVtaXFhMjNkWCs1Z1hWM2dXU1p4c25NcFo2eXVLKzJ2M
VdVd1lsa2FxcWRab3FvdEttMjRKWGx2WTNLcjhxc1pyQ3h0cm5mNlp5OFpMeXQyN200cmVXZ1ZyYmdyc
2ViMU0yMlpxdWxzVXZFeHFiVXRjNnJ2TGgwdWRKdG82SEV4R2k3eUxxV3VMN1V2dDE1d0x1WTRzQzB0c
WFkdmRCaG41K2UzMkdIdTdPMnFibTZaSXV0cUo3RXY4NmhtTHRrcDdXMVU2N0J0S1ZXdnNtcXI2cXFwc
0xVclpQQzI3ZXZ1YVc2dHJhNWRaS3VxcEo0azJmdXdMU3BtWFNwaWFhSGVablF5N2U3dkp6QVRaREpiS
ng2bjFQSnVjQ3F4TUNWbUxtdXZLMTJnNmgraXFhUG5JdUhmbWVnZm5xQ1pHaHVrbkZ0Wm1PMGNXZHJmO
ForZllGbXZLeTBxTXJGMTFWd3RYNTlnWE41d3NiRHFLamVoWCs1Z1hKempwT20yTVhTdExtN283ckF0S
3RwcStaeGlINTdaN1c2Zm5XcGMxaXozY1RGbjlyTHA2VzNtNEpOa01sc200S0hVNUxIam5xZGI2Q3J2c
kN0YW8rc2IzU1U0SSt6azNteG9lcThzOFNUbU1HRWZKeHdZN1cwY1g5K2RiYW51NjIwcm1Sd3haQ1pic
Wg4ZFhtQmM2Yk9zTUtXcU9MVHVLKzJyN1IyckoydGlhakdnSTkrYUxlNXFMUFo1cCs5dG51QTdZRjFkN
0ZiaHNIZHNMeW0zdGVrc2ErcGJVMlF5V3ljZVo5VHVzYTVwdGV5ajUyMndhMXFqN3R2ZFpLbmQ2eUppS
UpmbzRGMGgyWlVmcEo3WTJadHFuRlpoTGVOZFhlQlpydTNwcGJWdThpWGxyYXBhSUxFYllxSGlWVm9xW
lI1YzNkMGJvYWxWS09KcDROMmtIUjJhWCs1Z2FhdFdjdTR1cm52dW1aLzZuTnZpWkN1elpUVTFxU290c
Hh2WnRLL2JKK0R4bXVJaTQ3QTFvZGtjM2UwdmJHNGFuQzNrNkNIdEh1SGZXS2pmbmVGWW1SK2tuRmpab
U9xY1dkNWRJTjBkbmQwZG5kelpaR0duRlZ3dFg1NmdYT2h1cnUwVlhIc25YZDZnV08wdWQrWXVNYk92T
EtjdHJXMGlLK3QxdGlwdmJMTml1ZW9yYkRLbnFqRjA3L0tWYWJkZlhxRVdidVV1TUZVcHJ1ZlpJdU9kc
mpJdTVSK3dxNnh0SnE2cExHZDFydmd2c215b09lVHRMYWJvS0hIczZtYnBlMWpjcngraEhXQWFhaXJ1c
VdueXNiZG5LU3dab0c2aTJTSmdvbFZpdUhNdDJhdHRxKzM1WjNpeDg2L3I4cTlacXl5cDZuYzJLcDR1T
UdxbXEyd3FlK2ltTGphdHN1c2k5NnlacjJjdTQ5MXc1UFV0TmhSdmNiRHNvT3htWis3c3JxdHdyeFZxY
2JPdnVaNXlyS2s2Yk81eUZLandJS2tvcVNaNDZpc3U2WEhyYlcxdDNScGZhaWJpcU5WcWJ1MHEybU1wc
E9HaVZXYzNzUzR1N21tdEhhc3A2MktuNDFveUtteXJLVzVyTS9YbU15cGU0RHRnWFY5c1Z0b2Y1K0NoR
09lbDNWNmFtZDlaWVdHYkp0NGgyektqb3QvaGNPVnE4aTJ0N1oyZzZoK2tLZDN3TUxQd2FmbHMybVFwV
zUvbEh0VnFKam1wcGk4cWJLNHY3ZXBhSUsxYjVHUWkxVnd0WDUzZVl0VnZiM0dvYUxveEtpbHRLVjJkc
XluclltbmRXaVJ0NEI4Zm1pMzR0eWF2V2FVcWJSM2Y3ZXhjbkJ4MHJ6T29kZlpwS3E5V1lpVWo0WnQzb
0tiYTNuR3Ribk11NWRia0xHQ2VJKzdiM1dNcDNmdHpybkFsZVczdDhtYm83elZZMjZmYmFwOHFvTjlqV
2EydktheXNMV2R4c2lMYnFoOGRYbUJjNFBMdmJLWXFlelFwYkdzczJGMnJLZXRpcDZOYUxtMnE2aTRxN
mJVMUt1OVpwUzR0SGg5ZnBsclpvQ2plb2RtbUp4M1pucG5oMXVGa0dLYmFxQ2trWVdGZjRYQ29KMjJ3Y
TJudUttcHFYdW95TFNLa0lkVXBYNTRpbDlrZ1k5eloxWmpxbnRuZVg2RGRHaUN0NEI5ZkZmVXlzcW5xc
lZtZ2JxTGJKTndzS21YNHMrbHFMT21ZNC91bmEyT3FMU0FoM3VBd3JlQWU2cVZuczJ0dldlMXVuNTNxW
E5ZZjU1OWgyT2Jtbk4yZW1kOVc0V0dZcHQ0bFdHSGhJUjFrMzFnYW9WOWVYNTJnNmgramFkMzZMckVzb
FN1d1lHSFkyNXcxYWFmbTVidXFxMnVqY0MwdGJtNGk3K3lwTlBLaTI2b2ZIaUFhYitjdkxseGJxbXpsW
FdBYWJTbXdOYVg1OExqdUkvRHRMVzV1SXUvNE9LcHpHYVV1TFI0ZFg2Wm5adkMwYi9BbzkvVHNyUnNjc
0JsaG8xc2paalhvTVREeUs3U3UxQ0d0cnVwcjdtNlYzL01wNG0wZTh2R290aHdnc2hzYklpRXA1aVhwK
yt6bkx4bWpyZUFlSGFBYWJTYXpidktwcHFocUtlN3RsV1V3WWxrYjdPRmRuWjRkMjZFcFdHamtJMkRkc
EIwZG9GMGRtbXI1bkdQZm51NzM3bTNyZWFuV0lyaGg0NXRqYkNzdnI2c3Y1QjNrYVdsZVpkcmVjYTVzY
2l1bzU2MHdjRzR1V3B3dDVPZGo1eDdrc0JzcElDQmQ1YWp4ZEN0b3BlWDJhNmJmbWFPdDRCM2ZtaHBmY
WliaTZOVnBiU3RxYXh6YnIySWYyNnBzNXgrYUt1d3VNTGRvOVM5NEhXQnYzNTJncmVBZmFxVnFibTR3c
lBoYVgrb3NXbHh3cWgraW0yTjNiaW92WnEvbE1YS205cTIyRk9Tdlk1MW5zQnFjbzl2dUwyMnRKNjN3Z
ExIbkpUS2gyT21pR21scEtPeHg3U21vNVRscHFscFpvNjNnSGgxZ0dtbHA4YTMzWmlVcHFXNnJITnV6S
WlBYkhDYmxYUjNmVzV4aUo1a3FubWRnNENHZElCM2RHaUM0NjFvaVg1N3V1cXJwYmpjbUpxdzRySjVid
DZrZEgrRVdYOWJob3hmbTN5U1lZNTBoSFdkZldCemhYMXFnOGVDYTM1NzRNbmJ6Y3pBVks3QmdZNXNWc
S9Zb3B5aWxOeXRuR3QvMEsyQWZYK25nWE5zbTlIY2JXbDhacTI4dXBkN2ljSnRhYXVkWm5aM2NYR0VvV
1NqaVoyRGRvWjBkbmQwZG5lZ28yZUlkSWwxcW5oMGRLZHJXSXJoaDR0dGpkaWtzNjlaaUo2UGlHU2xhd
GlXdXNuR3FyakFsYXVscnJ1N3k3ZW5xS0hPeU9KN2tzQnNwNGhwdzV1WHVZUjhwbkJsckh0WnZLbTJ1Y
mlzbWJtc3RJWEN5ZHlxcExTb2pxakVtM3VKd20xbnFwMW1xcXkwcE1iYXBPZkMzTUZva2JlQWVIWjJnW
kswVjhXenk2cWF1cW1uN0t1YmI5Mjl5NXphMkdPNnVWZkFuOFRJbDR1OTJKYkpkTVNtMXNDbnFNZXh1M
mk5dGxXVXk5eTQzOHpLbXBQZXM3bURVb2kyeDJHZ3BaZmZzNlZwcGIrcnRibXR1cSt2VmJTZXFtQm5Zc
TI1WjhhbXZySnZwNldaMXJpMXVhWmh5Tm1aazhuT3hybk5zN2lydDNScHErWnhqSDU3dWZPM3FXYXlyS
ENIcUcrOW1NemV1TGl2cW05bXlKQmpuWUtIbzd6QXVhYldzbytkdHNHdGFvKzdiM1dTcDNlc2lZaUNYN
k9CZElkbVZINlNlMk5tYmFweFdZUzNqWHVBYWJxcnViV2UwTVNMYnFoOGU0QnBsNXpSd3NTbG01dWV0N
EI0YzN0MjQ1bmZ2czdHcTdXNHY3ZXBhSUxqcldlU1pudUE3WUYxZHJGYm1yN2x1OE9pek02aXM2NXNiM
mJJa0dLbGFvZHN5bzZKZjRXOW9xSzRzbXFEdUlKbGY4eW5qclI3dTd5cDRicTJ0cGFuY0oycWJXWnU3W
HR0ZzJiRnBicXdzcTFwZlptYmhxU21iM04zZ0duRXFMdkJzcVdmNmRldHRiVzBZNC9hYnFPVTRJMS9rR
2EydktheXNPUGJuTXBtbExpMGVIZCttWW1vdnRHeXlxYll5NjZydkZkdlpzaVFZNXlDaDVUSnViVzV5S
3lVbXNteWFvUEhnbVo5azQrSHFvcU1lbUttZTNtSlVtUituSEZqY0dPcVkzSzhmb1IxZ0dtNXRxdWpxY
2ExelpTcHAyYUJ1b3Rra29oeFpXYXFtSEYyZW01emlKRmtvNU9kZzRDR2RHaUN0NEI5cXBXcXpLWE51d
TFwZjdleGNuQnh6OE80bk5mTHBiS3ZXWWlvdnBCcHBxbWZZbzZPejdpZGdXcGJ2TUt4ckhhRHFINk1uN
CtjaVlkOVlxTitkNFZpWkg2U2NXTm1ZNnB4WjNsMGczUjJkM1IyZUhKbGtZYUxicWg4ZUlCcHY1VEdzM
0Z1cWJPVmVJQnB0S2EzNEtMWG5jN0hwN2lsdWF5SHRiWGUySnJNcmNpem5JSzNmcXR6V0wzWHNNSlZwd
DE5ZUg1eGI1NjZ1YUhackttU3k3VzJwdGF5YzZqRHU2MnJ5TEdrc251b3lMU0tpSWRVMTdPNnVLU2R2d
GFxb3FSVnRiUnhlM1dHZm1pTXNxZXBycHJVZHQyYm1tS250Ylczbk1ERHdaU3E0dEt5WnJhblliV1JwO
Wk4M01HcWRxaW51NldvcU9QWVY3dXp4N1BmcXJpdDVxZFd1Tnh0eHFYUHo3Vm12cVp0ajc3TW04K3RoY
VcvdVhTcHhNR1JtN2JBcldqR3JhYTV2dURKN1huQXUxTGxzNmk1VXBXOHhtR3FxSnp1cGxlNHRMaTJwN
3V0dGJXMVk0R3EwWnlvWXFxcnFNV295N1BDVTUvc2c3bTVyS1ZoeTlxbzIzblJ0THEzcHFlNnFXYXEzT
2lxektuTHVKcTdzMlRncHFiQjNjTzhVOS9TcUdhcnA3Mlh2cm1UMzdIVW4zZkV1YmZKdktLbXRydXJyW
UpxY0xlVG9ZK2N6ZEM5bDVXSnVvOXFibkRJcHBTcXFPeW1xbXQveG41M2VYNW91YWVoeHJmY21KU21wY
nFzYzI3TWlJQnNjSnVWZEhkOGJuR0hubWFuZVoyRGdJWjBnSGQwYUlManJXNlNacytxN0xxdHMrVmJjY
0tvaEpGVnNkTzd1citwc2syUXlXeWNlcDlUeWJuQXFzVEFsWmpKeHJpdGRvT29mb21uZDV5VXlvZGpwW
WhwdWFHcnZNNndsSnFTNTZWc2EzL0dmbmFCWm1pQ3RXK1drSXVqcDZ1bnEybU1sNU4raXFad3NwMW1xc
mE0cjhEZ2xkZk1qNDZ2a0hTQnVuNThnWkxsbU15dHg2eWNncWgrcDNTcGlaK0FrVlhlMzZXNXJhbTJtO
G0vb2RtN2gyekFqb1NBMW9kcGMzZTl2YXJBc2Fpc3Z0OTN0Y3lSZm1XdGNKZkhvWmV6MWJTZ2w1N2ZzM
WRyZjhaK2QzaCthS3EwbXNMS3pwS1pvN2lyYVl5bWszK0liVmlyazNWN2RIRjBnYU5vazRtZGpYYUdmb
lozWm9HNnFxUm9rbWJPdGQ2b3VLblduWmZEMDIrU3BxV2JmSUJzYVgxY2lvTmlubldYWlhlRWhIK1RmV
3BwaFcrRHU0NStiMmJNNGJidXpzcHZiZWFJZ0k5VWxjVERxcCtYbGVhbVdZVEJ2SDUrZ3FXQWVIbHYzT
W1qWjI5a3E3dXd0VldVd1lsbWFMT0ZkSFozY1hHRW9XU2ppWjJEZG9aMGRuZDBkbmVnbzJlSWRJbDFxM
2QwZGF4YmNjS29nWkZWMmN1d3EyeHl3R1dHaVd5TnVOSjR4c083c2NpUm9xTExzbXFEeDRKcGZudmJ2d
DNFZVlpbHJYOTZqMVNrdTZtd29wMmYzNFdwc3JxNFpvRzZmbmQ0ZkZmRnU5eVdwNnUwdXJEQW9YdUp3b
TFucXB0K2FKdXBxc2VSZXRpNjRjaTR1MlM5c0xDeVo5SGZvOGU3ZWJucFo3ZTQ1cXViYjgrNXcxUFUyT
E83dm1OdG1zcktvdUM4aFpMRnVIU20xOEdSbkwyeXJHaTR0NWk1eHRMRDdzeDN0SmZoczdtMnBwbXlnc
XFoVnF6cHRxbHB0TVd6cWF5M3VheTFWY3JFaVhxa3NhdXlySEYzeTdmRm1HU2JucmVBZTN0anlPcWsyS
HVveG9DT2ZtaXRxYWU3NWVXY3kyYVV1TFI0ZG42WnE1dTcwNjdLbU1yT3BMcXZXWWllajRkcnBXcVhZW
WlLZ1hXVWVtQnVkWDE0Z29SNGIzU0pqNUR0azQ2SFZPbXp1Y2libzd5RWZLWndhclJqZmJLOHg3bTRyR
2FCdW54bWs1Q0xwWnF1cWFlNnRwTE54NytZV0xUV2ZuYUJZMk9QNUc2a2k2ZDFxc1c3dExPenA2dlA0S
nVOWnBTNHRIZCtacGwwcVltamgzbWozZE9tcTJ4eXNXV0ZrYVdsZ1o5VHU4UExzOCs4a1ozSWI0T3hqb
mh3dDVPamo1ekx1TUdiNGJWcGtKWnVmcDIwYldkbXRHT3F2cWJHcDdpd3RMcXdzYVBVZUtTY2IzSi91W
UdLYlh1K3hKV2k0dGFzcTdsamZNZXJaYWFUajZPNHhhZXJ1cmV6cU52WXFYaG1sTGkwZUhWK21aeW90T
S9CdkpMUHk3ZXJiSExBWllhUGJJMTZsV0tOZ1lSMmtIMWxXWVY5Z25pRWdtVjBlNmpJdElxSWgxVG92c
XUycHBtdHhxS25tMVcxdEhGNmZZMW1lSGQxZkhSeVpvNkdubE5sY241MmQ0dGppWENLcG5Ddm5XYTV1N
ksxeWVSV3JzeW5qSUI0cGJ5b3JiS28wdCtjZW4vV3JyU0FmNld4YW0ySjZjQ1JaNldNcXJ1em0yOW15S
kJsbllLSFlZZUVoSFdUZldCcGhYMTRlSVI0WlhTSm5ZV3FpWWQ5WXFOK2VJVmlaWUNFZktad1o3UmpwY
XF4dUdhQnVuNTlnV1NsenAzV2xKNnVab0c2aTJlVGNMMmNtZVNGZjdtQmVIdDI0YUc2eHM2OHNuaC91W
UYxZDRHUzE1ekxwOHV1NnJ1dHMrVmJjY0tvZnBGVm1JeCt1WVJyaDAzSno2TFFhcUNra1l5T1o4bXlrY
TNLdjYyN2RvT29mb3FmajV6THZMbVgxTUdzdEphVndzZGpicWx0cTNweGEzYURkWHQwZFhoMGNtaUJoN
XB0YUhKK2RuZHpic3lJaG0xWTc4aTJ1YkN3cjNhc3A2MlFwM1dNdjd5NnZMYXJhYXZtY1lsMmsyZnNyT
ENwMkt5YnJ1TEd4NWlOcGJhQWVuRnZUWkRKYkp4Nm4xTzd3OHV6ejd5Um5iUzZySDEyZzZoK2lhZDNuS
lRLaDJldGNMZkhtNWV6aEh5WGNHTzF0SEdDZm5Xb3RiNnlzcmFqbWRSNHBKeHZjbis1Z1lkdGU4Q3dwN
S9ueW1hQnEzdHhqK1J1cEl5bmRibkxwcm1xdHErMzVOeW14cmQ3Z09PQmRIL3FjMitKa0wzTWxkZlR0c
TZ2cVc5bXlKQmpub0tIZ2NuRHQ2cld3SjJhd0xLNmFIYURxSDZLbm8rY3ZNbXlrK2V6cHJtVHFMT0VmS
1p3WkxON1dYdDBoSGx6ZUhaemQzVlZrb2VqWm1WOGRIWnBqS2FUZjRCdFdPN1RxS2U3cHFDNDBxalllN
mpHZ0lkOWdHbDJkbmlsb0dpS2NZbDRtbmgxZnFwcGNIK2ViNUttcGFCOWFMMnJycC9LeVZTbXU1OXFrW
GExdThTMm5KcTN1YTFxajhXZWZvcWRrTnVUaUlSczdzR0JpV3hXdGRlcWwxaHU3WHRxZTM1MWRIWjNkS
FozY21XUmhwbGpaWEowZG5lQlk0bCtmMk5tcVpOMGQzZHhjWW1UYithVG9ZMW94S1d6ckdhQnVxcWtiW
kpteWJLOXRySzM1cVdmczgvQnZKZXV0bVdCdlhHQlpYZkVtODZ6aDJ6S2pvZDRuVzkxcDdhdnRLM0hhS
m1wZWErMjdyeS9iWVRpdzd1K29KdHV5S2FVcXFqc3BtVnJmOForZDNoK2FLdW5xTVRJMHFPcHE3TzBhW
XltazRHQmJWaTgwcks1dHEycXVOS28yTDJObHFmSnFibG5rSys2NUpOOXZhWE51dXlzY21heXJIQ0RxR
y9Mck52UFpZRzljWVZsZDd5WHpMemFvN3pIZG9EV2gyRnJqMis2cmNDdGxyZSt6TG5iemJ4dmJlYUllS
TVzVm9DU2NtaGpZNjl1YUhsa2czU0FkM1NBZDNKWG5NbWphbTlrdXF1NXhKekl2SEZ1cWJPYWZtaU5xc
m5JNXFiWWU2akdnSWQyZ0dtMnE3UFYxS3E5bzgyKzZxeG1mK3B6Wm9tUWI1S21wWnQxZ0d5YnZLTER3c
UhNck1TZXU0bDJnTmFIWUhOM2I0TzdqbjF2WnNuZnZ0MitlWWlXclg5OGhXSnZ3Wng2YlZpWDZiaWx0Y
k8wcUxscGY2K0JlWERVa0o5dFY3U2x1ckMvbW51SnMyMW10TlorZDNwN1k4Zm1sdWE4Mzd5MnlxMjF0Y
mRvZ3RtdFo1TzNrMzYwYWJTNTJhV2Z3dGF5eVZXbTNYMTNmWEZ2ZThmRmxkQzcySjY0djdtM2cyOXJyS
TkrZVlKMnE2ZXB1dUc2MmIyNHdaZVZpYnFQWTIySWhITmpaMmluY1d4MmRZTmtkbmQrZG5kOFpaRjRwS
1p2YzNXQWFjYWp2YS9EbUpYZHhMaXJhWHkwanFKdHJYdWZnM2VMY1haOGNYZDNrS05ua25TSmY2cDNab
i9xYzJ5SmtNRExsTi9mdG1pRnFvZGtqM2lUNGFuT25iaTJ3S3FGaUsyaWozNTVnN1dDWm51VDZNaTBqW
kZ2bWVpM3EzZHRwNGlWYzIxWVk2cHhaM2wwZzNSMmQzUjJkM0psa1lhWlkyVnlkSFozZ1dPSmYzOWpac
ktGZjdtQmRYdDIzNVhndm8rT3VaQjJkb0Ztczd6YzU2RElzTDZLNTZpdHNNcWVxTVhUdjhwVnB0MTllb
1JadTVTNHdWU211NTlqaVk1Mmt0aTVwS0xGdWExb21iV1dyY1dOcU4vTHpiS2s1bkNDeUd4bGY1eGpsN
XVtM2JPZ3ViaThzN1JwZjdtQmMyK0RnNHR1cUh4NGdHbkZyTW16Y1c2cHM1dCthSzJtb3NqbXB0ak1qN
DY1a0hWNGdXYTRyTnpZbU11cHVLbmJ1Nmxtc3F4d2dLZUhlV1dibTNoemVteDZYWTEyWXB1Q2xXR1JoS
VJubnNCcWNJOXZ2cTNHdTU2eng0K1E3Wk9PaDFTNXQ3L0pwNmF6aEh5bWNHU3NlMW03cWIrcHA3cXBwY
nU3cGNaNHBLWnZjbjVvYVl5bWszK0JiVmpkMHJ1MHM3Q2l1TkNoMTQ2UGpybVFkSUJwWm9HNnFxaHhlc
lRMcnQyc1puL2JjMmFLNFllUWJZM09zcjI0bzd5TXVjbFVwckdmWXBMSGpudWRiNktheWJhMnIzYURtW
DZKcU1pMGlvcUhWT2JEcWNpVnByZlN0Wnlsb2UxamNySitnMys1Z1gyQWFiS3F3OExTcHAybnRtaUN4R
zJLZ1lsVmh1dlNwNnU2dEs2MTNKbmxlWStPdVpCMWQ0Rm1xYm5WMUt1OW83Mm03cXhtZitwelo0aW9iN
Gxqbko5d2RuOWtmMk4xaG1LbGVKVnJoNFIyZ05hSFlXcVBiNzI0dUttcHFialJ0dTYrZVlpbHJYK0FqM
VJtZnBOMllHWm9wM052YVhTRGZuWjNmblozWkhEVWtKOXRWN1c0cDd2R3BudUp3bTF2czRXbHZLaXFyY
lhUb05oN3FOQ3ZrSFY0Z3FXQWVLZXRzc3QralgrY3JybXQyMXR4d3FpQWlXMk5tbk4yZW1kOVc0V0dZc
HQ0bFdHSGhJUjFrMzFnYVlWOWVIaUVlV1YwaWFWM3RjeVJnV3lWdktqQ2wxYUoxWHRrYUcyY3JxYXJyY
itwakxDcHNxdTFWNXpKbzJkdlpMS3ZxcnhWbE1HSlpHbXpoWkcxcWFxdHVaRjYzTDdadDdsNGY3bUJkW
GVCa3RlY3k2ZkxydXE3cmJQbFczSENxSDZSVlppTWZybUVhNGROeWMraTBHcWdwSkdNam1mSnNwR3R5c
it0dTNhRHFINktuNCtjeTd5NWw5VEJyTFNXbGNMSFkyNnBiYXQ2Y1d0MmczVjdkSFI3ZEhSdGdZYVpiV
1Z5Zm5aM2MyN01pSVp0V08vSXRybXdzSzkycktldGtLZDFqTCs4dXJ5MnEybXI1bkdKZHBObjdLeXdxZ
GlzbTY3aXhzZVlqYVcyZ0hweGIwMlF5V3ljZXA5VHU4UExzOCs4a1oyMHVxeDlkb09vZm9tbmQ1eVV5b
2RuclhDM3g1dVhzNFI4bDNCanRiUnhnbjUxcUxXK3NySzJvNW5VZUtTY2IzUi91WUdIYlh2QXNLZWY1O
HBtZ2F0N2NZL2ticVNNcDNXNXk2YTVxcmF2dCtUY3BzYTNlNERqZ1hSLzZuTnZpWkM5ekpYWDA3YXVyN
mx2WnNpUVk1NkNoNEhKdzdlcTFzQ2Rtc0N5dW1oMmc2aCtpcDZQbkx6SnNwUG5zNmE1azZpemhIeW1jR
1N6ZTFsN2RJUjVjM2Q1YzNsNlZaR0dvMk5sZkhSMmFZeW1rMytBYlZqdTA2aW51NmFndU5LbzJIdW94b
0NIZllCcGRuWjRwYUJualhHTGZacDNkSDZuYVhCL25tK1NwcVdnZldpOXE2NmZ5c2xVcHJ1ZmFwRjJ0Y
nZFdHB5YXQ3bXRhby9Gbm42S29KRGJrNGlFYk83QmdZbHNWclhYcXBkWWJ1MTdhbnQrZFhSMmQzUjJkM
0psa1lhWlkyVnlkSFozZ1dPSmZuOWpacW1UZEhkM2NYS0VrMi9tazZHTmFNU2xzNnhtZ2JxcXBHbVNac
1dwMjdlRnFPMmFwTExUc1hsdTNxUjNnR3lsdG83QWVHM2VncFpqa1hiQXFjUzljWjNMcnJhcnVheFhmO
HluaHF1VGViR1g1ckc1dnFLb3Q5R3ZWWEdtdEhKbmYzNTFtSzZ3dDJhM3Jxckl2OWRUckt1d3NtZkVyT
WV4dDZXbDU4eStxMmVSczhQVW1lYk11clN4dTdabXZxMjZyNURVcFhpUW5ZYktaN08ybDNxWnc5ZkR2R
k92MDdXcnJhdThuYzUycGRDNjI1YkpkTHEwMVcybHJMcS9hS25KdkoycHgrRyszYnJMdHFIaGZHbVFwV
zZDbkdPbnI2UGZZM0s4Zm90K2FLMnBwN3UzcDhiSmkyNm9mSFY0Z1hPbHZycTBsS25ld3FpbnU2WmpqK
1J1cEpLbmRYaUdkWHQwZEh4MG9heFhpWGVUZUxHQmRIU1pkS21KcFlkNXFkRGN0cSs1cFc5bXlKQnBwV
3FybXMvSXliZkliMnVzajM1NmduYTZtckMrenNqZnVNdkdvdGh3Z3Noc1pJaUVZMjZwYmF0emNXdW93c
nUwczdPbnE2R2l4WXVMYnFoOGRJQnBjMjdNaUlSdFdPblZyYW1zWTN5NHEyU3V6S2VNZ0hpb3RiNnlzc
mJSMTZwNmY4Si9xb0szZnExeldNSFB3Y0NoMG94K3FvUm5pSjZQaDJXbGF0aW11Y2UzdDh5OXBLTEV1N
3RxajdGdmRKVGdqN09UZWIybjFicXd5SnFad0lSOHBuQmtyWHRabWJiQ3A2dTZ0N09vclpyVGRvdHVxS
HgxZDRGemxzdXpzS2ViMk1lbHVxeGpmTWVyWmF5VGo0VjJoM2x6ZDNwemVLbVRhSXQrakh5MGQzUm1zc
Xh3Z0orSGVhamJ6cVM2cjVheGpNbTdWS2E3bjJLUWpuWjNrMzVsWm9XRGRYbU5hR1ozazZDTXRJbUhiM
jNtaUgyUFZLZkN3N1dvcVZXMXRIR0NmbldsdktpdHNxaWtvY1o0cExDZWZIVjZnckp0aW9XSnJxbXpsM
zVvcnJhcXVKTnY1cE9naFlCNGRIWjNkSFozb0tObmlIU0pkYXAzZEhTbmFXWi9ubjJIWTV1YWRIWjZaN
FJOa01sc240S0huN2pCdVdlZXdHcHFob2RxcThPc21wZTh6c1BvdnNsdmJlYUllNDlVb3JmRnJGVnhwc
lJ5YVlObWxyT3FyR1NacXFPano3dmJWWEMxZm5kNGkxVzlzOEtXcU9MVHVLKzJyMk9QNUc2a2s0K0FhS
kczZ0h0K2FMdnA0NXg2Zjh4L3NvRm1xdHlhcXNUZ3NzcFZwdDE5ZDN4eGI1MjZ3cGZNdThxUXU3WElxb
1dJbzNPR2hvSnFobmhtZVlhZGlxZUxqMjFpbzRoM2hXeGtmb1I4cG5CcXRHT3RycmJHcmJXMVpvRzZmR
3liZUsrY3JiYTV1S3h6YnN5SWdHVndtOVdwc3F5aXRMblFxT3pKMG5XQnlYNTJnV1pvZ3VPdGFJcCtlN
m5wdnJLdzVwcWFydHV4akZXbTNYMTJoRmx2WnNpUVo2VnExYVBBdDdsbm5yRnFhWkRBZ29HT2FwbXowT
nZCNmJxN3dGU3V0NEdHYmFlSW1IdFZxSlR1cXFXd1pvNm9nSGQvdVlGemFKdDQzS2lYdGFlNHNNR253c
jI5cGxpMHpINTJnclI3amF0VzQ4N1B2Ni9Kckt1NVpvRzZxcVJxa21hcHQrbXFxYmZxcHBlNjA3OTNWY
WJkZlhkN2NXK094N3VUMzYzRWxiakl1V2Vld0dwcWpvZHFlb1I1YW5HSm9vS3NrWGQ5WXExK2Q0OWlaS
ENkdEcxblpMUmpyTG1vdExpcnBxaW51NmRYbk1talpHNThabmgzZ21pR2ZvUmdhTEdEZEhhQmNYR09vV
1NWbE9DTmZKQm11YnVsdXJ6amxYTExmcEovbktpNnBlQ2xsN0hhc25sdTZOTjlkMzl5cm1XR2pXem11N
Tlsa1hhN3VzeXhVblRJaDN0NmptcGxkSW1kaGFxSmgzMWlvMzUzaFdKa2ZwSnhZMlpqcW5GbmVYU0VkS
FozZFdpQ3RXK1ZrSXVobHErcGFJTEViWkdJY1pTcjNjeTRrcmFvWTQva2JxZVRqOEd2dWE5b2dyZUFmN
nFWbU0yb3dybkd0cXRtc3F4d2dKK0hlWmZRM2FhNHM2ZkJsTVRFVkthN24yS0lobzVudGJLWG9zakJyY
nJIYUpxNnZ0L09tcnE3dXB2aGJxaTRwcDI5MEdHY3BGUGJZYU80cTRGa21xK3BacWlscWNyRjE2WlZxN
0ptcUxXZ3dyeTRwcXJyeExpdnRxOWh4OWFvNThMYnVybDJzN2E3cmJXMTQ1T1l5cWw1dDkrdXJiZnJuc
WkwMG0zQW9ZdmVxNnRxbzd5U2czaHQzb0taYTNuSXpiWEliMnVzajRXQ2FycXRscmpPMzdydGU1TEFiS
1NBZ1hla21ickhvcWFia3Q2aXE2NW1qcmVBZUgyQWFYUmxrb3VXWTJodmRucG5nV09UZm45dFpxbUZmN
21CZUh0MjU1bmx6TmJDdEhoL3VZRjdnR20yM0svTXVjdXFuSUszZnFocmNISGdzc09Zek4yb3BiNnd2W
kIza2FXbGVKOVRlWS9IZjVSL2FsdTV2TCsyd0xlV3FMamF1YTk3a3NCc280aHBkMjJuaUpkN1ZhYWw0N
lNjYTMrM2ZuYUN0NENBZkZmRnhlQ2hvYkdscXJwemJzS0lmMjZwczVsK2FMbWl0YjNmbTVXVTBZMTJrY
mVBZUhlQWFlUG9tY3VueTY3cXU2Mno1YXhZaXRlSGgyN2VwSHlBYktmQ2pjRy9wZE90MTFPU3g0NTJsb
2RTaWNlOHE2M0h1NktseE5MSG1udVN3R3lrZjRGM2xhYXp3N1dZbFpmYnRaeHJmOForZDRCK2FIbHlac
GFEbVdaaWRIaG1kNEZ0aVg2SlkyYWJucmVBZUhKN2R1YWsxN3JodUtXNnBicXNab0c2cXFSd2ttYUxkY
XQ4Y1hTcVptaURqbjJIYlp1YWZYWjZXWWllajR4c2pidlprc3ZKeDJlZXdHcHlqMitwdnJXeG9hVzcyY
nFjbE5TMmJLU0VnclpzWllXY3ZLWndaN1JqbnI2dHQyYUJ1bjU1ZVh4WGtZYVpZMlZ5ZEhaM2dXT0pmb
jlqWnFtVGRIWjNjWEdFb1dTamlwMkRkb3BtZ2JwK2VvR1M0WmpGcVh1QTdZRjFlTEZibDdMaXRzYWgzc
Xk4aTdlWXRwZDNrYVdsZko5VHhiMjNzSVdJbzNPR2dZSnF0YXVwcmNqYnlMelNuTHFUM0xwcGtLVnVmN
U43VlpxWTdhU3BzclRIcmJXMVpvRzZmR2lZa0l0NG82T21zcXpFVTgyMnRGTjMzTmV0dGJXMFlaYnFWT
GpHenJ5eWRxcXJxTGk3dWRXaFdaTzNrM20wYWJpOTU1NVlpdUdIajIyTjBLaW52cXkva01oNGJkNkNsb
U9SZHNhcXo3S1JyTHFzcktuSXJWZC96S2VHczVONWYyS2tnM1NGWldHQWxtRmpabTJxY1hGNWRIVi91W
UY3Z0dtNG10UEowcUtqWkgrNWdZaHRlNVM0cTZydTFhbG9nclI3aGFOdWxjdlN2NnUzdDZ1bXVMKzMxW
lZ5eTM2SmY1eHBmN2V4YW1pSmtMSEdxdG5Xc3FldWxycVBpbmh0M29LVmEzbDJqN2lkZ21wYnhiK3hxN
2xxY0tpVG5aRHRrNUNIVk5lOXZzT2VvNi9HdEZWeG5MUnpib1MzalhxQWFiYW51NnVqeUhpa2wyOXlmN
21CZ21hVGNNS29tT3pHdHErM3RhckQzNmVWbE5hTmRwRzNnSUIrYUxmbDFhUEJ0OEdxN0dsL3Q3RnFhW
W1RbmNtaXpzKzJ1YmVZdUpESGRsU211NTlpaUk1MnFOV3lrYTI2ckt5cHlLMVhmOHluaHJPVGVYOWlwS
U4waFdWaGdKWmhZMlp0cW5GeGVYUjFmN21CZFhlQlpLclJ1c3FubXFHb3A3dTJWWlRCaVdSdnM0VjJkb
mgyYm9Ta1lhV05qWU4ya0hSMmdYUjJhYXZtY1k1K2U3anVxTGk1Nmx0eHdxaUdrVlhNNEtTdnRwaXZsN
3A0YmVqRjRsT1N4NDUzbVlkU2ZLcWduSmVoalllanJMS24wS0tha3BIRGs1bWxkNGlqbzQxVmNhYTBkS
EZyczdtcWFJSzNnSGg3YjRPWnZvYUprWkdMbWJDR3JZK2hoNVc5cEppTGFYeTBqcUprclh1ZGhuV0pkW
FY1ZEhkK2txNnFrbldRZjV5S21aZkxpSU9Vd0t5Y2dhL0poNGVlZkc5bXlKQmptNEtIWVl5RGhIYVNmM
kJxakcrRHU0NTViWDU3c0tyTnJhYWFkOFd0bnFTRWY2R3lnblo3VmJXMGNZRitkYnUxdWErc3M3R3NnN
UhjYlcxOFpvdVVrbnlscmFPQ1dMVFdmbmQ4ZTJPZTRKemhtYkRDc3NXM3AzV250YlNTcnFxU2RveC9uS
XFabDh1SWc1VEFyS3A0dmNDSW1KMldqbTZwbjRHNWFxQ2trWTJPWjlMRGxhdk12N0c4dVdwd3Q1T2hqN
XlwbzQ2QWxZbTZqMlZraUlTRm1LeVk1ckNudHFuQnVHYWRxYmk2cTZUUGRwWlRlYWU2WnB1MmxNWndpc
Vp3cXBOK2FJMktrNmZGazhHYXVwaG9rYmVBZ0g1b2p1WFVxYnl0dXJQYmFYKzNzWEp3Y2JxT3FvZkt1S
VNUajFtSW5vK0diSTFxb0tTUmhZcC9oWkYvaHBhV2xxZXJsNGVQckwyV3ZaNTVpS1d0aG9GM3FhUEF6Y
WVmcGFxY2ZLcURkWVIrYUpPTms1Q1dsTGFwcm9XSVpIKzVnWUZ0ZTNDS3BuQ3FtMzVvbXBhUnBNQ0d4N
2pBcDRlb21LV0xoWnFNa3E2cWtuV0pmNXgzZDNPcWFtV0JubjZPVmFiZGZYZUFjVzkrcXFhQ3VwcTVrS
nlpbUtTbmpvUitkNGk3Z29WNGIyYUpvb1NxaW9aL1lxU0ZhWkNsYm9LY1k0ZVBnNzlqY3J4K2czNW9hW
Cs1Z1hObm0zaTdlSWFYaVptYnNIZWFvcFJWY2V5ZGRYK0JZM09Fb211Z2lhQ0FlWWRrZDN4K2VuNnFwb
XA2Zjh4L3NZRm1vOGVCaHE2OW9IbHUzcVI0Z0d5RHRwbkt6bFNtdTU5aWlZNTJwTE9WZ0ppcmtwcWJuW
mVEWnBUZ2o3Q1RlWUpncDN4N2lsUnZ5OVY3Wm5CVnhvcDZhMyswZm4yQnY3bUJkRytEbjYxVmNLdCtlS
HVFWlpDSndtMXBzNFdYbUoxamZNZXJacWVUajcrdnVhbTB1cWwwdCtMaW1yMjN6TExic3FtMnBaeWx2S
kNJeW0ya3BHV2RtWW1ZZnFXWGRiQnFvS1NSaDQ1bjA3cGpXNURBZ255T2FvbWRxYkozdGN5UmZXeVZjS
UxJYkdpSWhJZDhnbmljZktxRGQ0UithTE90cWF5d3FNYTFvS2lYcW5lcWo2UmxoTENtcEpmSXo3S2dkM
itsdGVWV3JzeW5pWUI0bDR1WmpZZVRrcTZxa25hU2Y1eDhpblN1Ym1PSHNwR2JaNWllYzNwN2gzcDVyc
W1Gc25XblphNldtbWVld0dwcWhvZHFsSjJWZnBpNHdxaS9xNnB2YmVhSWQ0OVVWb25mdmc9PQ==
--------------------------------END LICENSE KEY---------------------------------";
        return [
            "name" => "license_7ubh3dHS2+bWqaOlnZ0.dat",
            "content" => $content
        ];
    }
}
