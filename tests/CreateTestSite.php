<?php

namespace Tests;

trait CreateTestSite
{

    public function createDBFile(string $workspace)
    {
        if (!file_exists(PATH_DB . $workspace)) {
            mkdir(PATH_DB . $workspace);
        }

        if (!file_exists(PATH_DB . $workspace . PATH_SEP . "db.php")) {
            $myfile = fopen(PATH_DB . $workspace . PATH_SEP . "db.php", "w");
            $content = ""
                    . "<?php\n"
                    . "define ('DB_ADAPTER',     'mysql' );\n"
                    . "define ('DB_HOST',        '" . env('DB_HOST') . "' );\n"
                    . "define ('DB_NAME',        '" . env('DB_DATABASE') . "' );\n"
                    . "define ('DB_USER',        '" . env('DB_USERNAME') . "' );\n"
                    . "define ('DB_PASS',        '" . env('DB_PASSWORD') . "' );\n"
                    . "define ('DB_RBAC_HOST',   '" . env('DB_HOST') . "' );\n"
                    . "define ('DB_RBAC_NAME',   '" . env('DB_DATABASE') . "' );\n"
                    . "define ('DB_RBAC_USER',   '" . env('DB_USERNAME') . "' );\n"
                    . "define ('DB_RBAC_PASS',   '" . env('DB_PASSWORD') . "' );\n"
                    . "define ('DB_REPORT_HOST', '" . env('DB_HOST') . "' );\n"
                    . "define ('DB_REPORT_NAME', '" . env('DB_DATABASE') . "' );\n"
                    . "define ('DB_REPORT_USER', '" . env('DB_USERNAME') . "' );\n"
                    . "define ('DB_REPORT_PASS', '" . env('DB_PASSWORD') . "' );\n";
            fwrite($myfile, $content);
        }
    }
}
