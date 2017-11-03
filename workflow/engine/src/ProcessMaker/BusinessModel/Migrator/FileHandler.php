<?php

namespace ProcessMaker\BusinessModel\Migrator;
use ProcessMaker\Util;

class FileHandler
{
    /**
     * @param $prj_uid
     * @return array
     */
    public function getFilesToExclude($prj_uid)
    {
        try {
            $arrayPublicFileToExclude = array("wsClient.php");
            $criteria = new \Criteria("workflow");
            $criteria->addSelectColumn(\WebEntryPeer::WE_DATA);
            $criteria->add(\WebEntryPeer::PRO_UID, $prj_uid, \Criteria::EQUAL);
            $criteria->add(\WebEntryPeer::WE_METHOD, "WS", \Criteria::EQUAL);

            $rsCriteria = \WebEntryPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $arrayPublicFileToExclude[] = $row["WE_DATA"];
                $arrayPublicFileToExclude[] = preg_replace("/^(.+)\.php$/", "$1Post.php", $row["WE_DATA"]);
            }

            return $arrayPublicFileToExclude;

        } catch (\Exception $e) {
            \Logger::log($e);
        }
    }

    /**
     * @param $prj_uid
     * @param $arrayPublicFileToExclude
     * @param $target
     * @return array
     */
    public function getTemplatesOrPublicFiles($prj_uid, $arrayPublicFileToExclude = array(), $target)
    {
        $workflowFile = array();
        $workspaceTargetDir = ($target === 'PUBLIC') ? 'public' : 'mailTemplates';
        $workspaceDir = PATH_DATA . "sites" . PATH_SEP . config("system.workspace") . PATH_SEP;

        $templatesDir = $workspaceDir . $workspaceTargetDir . PATH_SEP . $prj_uid;
        $templatesFiles = Util\Common::rglob("$templatesDir/*", 0, true);

        foreach ($templatesFiles as $templatesFile) {
            if (is_dir($templatesFile)) {
                continue;
            }

            $filename = basename($templatesFile);

            if ($target == "PUBLIC" && in_array($filename, $arrayPublicFileToExclude)) {
                continue;
            }
            $filePath = $prj_uid . PATH_SEP . $filename;
            $workflowFile[$target][] = array(
                "filename" => $filename,
                "filepath" => str_replace("\\", "/", $filePath),
                "file_content" => file_get_contents($templatesFile)
            );
        }
        return $workflowFile;
    }

}