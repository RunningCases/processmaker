<?php
CLI::taskName('list-ids');
CLI::taskDescription(<<<EOT
    Complete the PRO_ID and USR_ID in the LIST_* tables.
EOT
);
CLI::taskOpt("lang", "", "lLANG", "lang=LANG");
CLI::taskArg('workspace');
CLI::taskRun("list_ids");
G::LoadClass("wsTools");

function list_ids($command, $args)
{
    CLI::logging("list-ids INIT\n");

    $workspaces = get_workspaces_from_args($command);

    foreach ($workspaces as $index => $workspace) {
        $hostPort1 = explode(":", $workspace->dbInfo['DB_HOST']);
        $hostPort = $hostPort1[0] . (isset($hostPort[1]) ? ";port=" . $hostPort[1] : "");
        $connectionString = sprintf("%s:host=%s;dbname=%s",
                                    $workspace->dbInfo['DB_ADAPTER'], $hostPort,
                                    $workspace->dbInfo['DB_NAME']);
        $dbh = new PDO(
            $connectionString,
            $workspace->dbInfo['DB_USER'],
            $workspace->dbInfo['DB_PASS']
        );
        foreach(workspaceTools::$populateIdsQueries as $query) {
            echo ".";
            $dbh->query($query);
        }
        echo "\n";
    }

    //Done
    CLI::logging("list-ids DONE\n");
}
