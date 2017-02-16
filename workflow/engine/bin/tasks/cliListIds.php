<?php
CLI::taskName('list-ids');
CLI::taskDescription(<<<EOT
    Complete the PRO_ID and USR_ID in the LIST_* tables.
EOT
);
CLI::taskOpt("lang", "", "lLANG", "lang=LANG");
CLI::taskArg('workspace');
CLI::taskRun("list_ids");

function list_ids($command, $args)
{
    CLI::logging("list-ids\n");

    $workspaces = get_workspaces_from_args($command);

    foreach ($workspaces as $index => $workspace) {
        $hostPort1 = explode(":", $workspace->dbInfo['DB_HOST']);
        $hostPort = $hostPort1[0].(isset($hostPort[1]) ? ";port=".$hostPort[1] : "");
        $connectionString = sprintf("%s:host=%s;dbname=%s",
                                    $workspace->dbInfo['DB_ADAPTER'], $hostPort,
                                    $workspace->dbInfo['DB_NAME']);
        $dbh = new PDO(
            $connectionString,
            $workspace->dbInfo['DB_USER'],
            $workspace->dbInfo['DB_PASS']
        );
        $dbh->query('UPDATE LIST_CANCELLED SET '
            . 'USR_ID=(SELECT USR_ID FROM USERS WHERE USERS.USR_UID=LIST_CANCELLED.USR_UID), '
            . 'PRO_ID=(SELECT PRO_ID FROM PROCESS WHERE PROCESS.PRO_UID=LIST_CANCELLED.PRO_UID);');
        $dbh->query('UPDATE LIST_COMPLETED SET '
            . 'USR_ID=(SELECT USR_ID FROM USERS WHERE USERS.USR_UID=LIST_COMPLETED.USR_UID), '
            . 'PRO_ID=(SELECT PRO_ID FROM PROCESS WHERE PROCESS.PRO_UID=LIST_COMPLETED.PRO_UID)');
        $dbh->query('UPDATE LIST_INBOX SET '
            . 'USR_ID=(SELECT USR_ID FROM USERS WHERE USERS.USR_UID=LIST_INBOX.USR_UID), '
            . 'PRO_ID=(SELECT PRO_ID FROM PROCESS WHERE PROCESS.PRO_UID=LIST_INBOX.PRO_UID);');
        $dbh->query('UPDATE LIST_MY_INBOX SET '
            . 'USR_ID=(SELECT USR_ID FROM USERS WHERE USERS.USR_UID=LIST_MY_INBOX.USR_UID), '
            . 'PRO_ID=(SELECT PRO_ID FROM PROCESS WHERE PROCESS.PRO_UID=LIST_MY_INBOX.PRO_UID);');
        $dbh->query('UPDATE LIST_PARTICIPATED_HISTORY SET '
            . 'USR_ID=(SELECT USR_ID FROM USERS WHERE USERS.USR_UID=LIST_PARTICIPATED_HISTORY.USR_UID), '
            . 'PRO_ID=(SELECT PRO_ID FROM PROCESS WHERE PROCESS.PRO_UID=LIST_PARTICIPATED_HISTORY.PRO_UID);');
        $dbh->query('UPDATE LIST_PARTICIPATED_LAST SET '
            . 'USR_ID=(SELECT USR_ID FROM USERS WHERE USERS.USR_UID=LIST_PARTICIPATED_LAST.USR_UID), '
            . 'PRO_ID=(SELECT PRO_ID FROM PROCESS WHERE PROCESS.PRO_UID=LIST_PARTICIPATED_LAST.PRO_UID);');
        $dbh->query('UPDATE LIST_PAUSED SET '
            . 'USR_ID=(SELECT USR_ID FROM USERS WHERE USERS.USR_UID=LIST_PAUSED.USR_UID), '
            . 'PRO_ID=(SELECT PRO_ID FROM PROCESS WHERE PROCESS.PRO_UID=LIST_PAUSED.PRO_UID);');
        $dbh->query('UPDATE LIST_UNASSIGNED SET '
            . 'PRO_ID=(SELECT PRO_ID FROM PROCESS WHERE PROCESS.PRO_UID=LIST_UNASSIGNED.PRO_UID);');
        $dbh->query('UPDATE LIST_UNASSIGNED_GROUP SET '
            . 'USR_ID=(SELECT USR_ID FROM USERS WHERE USERS.USR_UID=LIST_UNASSIGNED_GROUP.USR_UID); '
            );
    }

    //Done
    CLI::logging("list-ids\n");
}
