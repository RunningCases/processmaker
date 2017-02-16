<?php
/**
 * cliUpgrade.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2011 Colosa Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 * @author Alexandre Rosenfeld <alexandre@colosa.com>
 * @package workflow-engine-bin-tasks
 */
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
