<?php
namespace BusinessModel;

use \G;

class FilesManager
{
    /**
     * Return the Process Files Manager
     *
     * @param string $sProcessUID {@min 32} {@max 32}
     *
     * return array
     *
     * @access public
     */
    public function getProcessFilesManager($sProcessUID)
    {
        try {
            $aDirectories[] = array('name' => "templates",
                                    'type' => "folder",
                                    'path' => PATH_DATA_MAILTEMPLATES . $sProcessUID . PATH_SEP,
                                    'editable' => false);
            $aDirectories[] = array('name' => "public",
                                    'type' => "folder",
                                    'path' => PATH_DATA_PUBLIC . $sProcessUID . PATH_SEP,
                                    'editable' => false);
            return $aDirectories;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return the Process Files Manager Path
     *
     * @param string $sProcessUID {@min 32} {@max 32}
     * @param string $sMainDirectory
     *
     * return array
     *
     * @access public
     */
    public function getProcessFilesManagerPath($sProcessUID, $sMainDirectory)
    {
        try {
            switch ($sMainDirectory) {
                case 'mailTemplates':
                    $sDirectory = PATH_DATA_MAILTEMPLATES . $sProcessUID . PATH_SEP;
                    $sEditable = false;
                    break;
                case 'public':
                    $sDirectory = PATH_DATA_PUBLIC . $sProcessUID . PATH_SEP;
                    $sEditable = true;
                    break;
                default:
                    die();
                    break;
            }
            \G::verifyPath($sDirectory, true);
            $aTheFiles = array();
            $aDirectories = array();
            $aFiles = array();
            $oDirectory = dir($sDirectory);
            while ($sObject = $oDirectory->read()) {
                if (($sObject !== '.') && ($sObject !== '..')) {
                    $sPath = $sDirectory . $sObject;
                    if (is_dir($sPath)) {
                        $aDirectories[] = array('PATH' => ($sCurrentDirectory != '' ? $sCurrentDirectory . PATH_SEP : '') . $sObject, 'DIRECTORY' => $sObject );
                    } else {
                        $aAux = pathinfo($sPath);
                        $aAux['extension'] = (isset($aAux['extension'])?$aAux['extension']:'');
                        $aFiles[] = array('FILE' => $sObject, 'EXT' => $aAux['extension'] );
                    }
                }
            }
            foreach ($aFiles as $aFile) {
                 $aTheFiles[] = array('name' => $aFile['FILE'],
                                      'type' => "file",
                                      'path' => $sDirectory.$aFile['FILE'],
                                      'editable' => $sEditable);
            }
            return $aTheFiles;
        } catch (Exception $e) {
            throw $e;
        }
    }
 


}

