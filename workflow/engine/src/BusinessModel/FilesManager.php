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
                    break;
                case 'public':
                    $sDirectory = PATH_DATA_PUBLIC . $sProcessUID . PATH_SEP;
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
                    $extention = end(explode(".", $aFile['FILE']));
                    if ($extention == 'docx' || $extention == 'doc' || $extention == 'html' || $extention == 'php' || $extention == 'jsp' ||
                        $extention == 'xlsx' || $extention == 'xls' || $extention == 'js' || $extention == 'css' || $extention == 'txt') {
                        $sEditable = true;
                    } else {
                        $sEditable = false;
                    }
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

    /**
     * Return the Process File Manager
     *
     * @param string $sProcessUID {@min 32} {@max 32}
     * @param string $userUID {@min 32} {@max 32}
     * @param array  $aData
     *
     * return array
     *
     * @access public
     */
    public function addProcessFilesManager($sProcessUID, $userUID, $aData)
    {
        try {
            if ($aData['path'] == 'templates/' || $aData['path'] == 'folder/') {
                switch ($aData['path']) {
                    case 'templates/':
                        $sDirectory = PATH_DATA_MAILTEMPLATES . $sProcessUID . PATH_SEP . $aData['file_name'];
                        $sEditable = false;
                        break;
                    case 'folder/':
                        $sDirectory = PATH_DATA_PUBLIC . $sProcessUID . PATH_SEP . $aData['file_name'];
                        break;
                    default:
                        $sDirectory = PATH_DATA_MAILTEMPLATES . $sProcessUID . PATH_SEP . $aData['file_name'];
                        break;
                    }
            } else {
                throw (new \Exception( 'invalid value specified for `prf_path`. Expecting `templates/` or `folder/`'));
            }
            $extention = end(explode(".", $aData['file_name']));
            if ($extention == 'docx' || $extention == 'doc' || $extention == 'html' || $extention == 'php' || $extention == 'jsp' ||
                $extention == 'xlsx' || $extention == 'xls' || $extention == 'js' || $extention == 'css' || $extention == 'txt') {
                $sEditable = true;
            } else {
                $sEditable = false;
            }
            $sPkProcessFiles = \G::generateUniqueID();
            $oProcessFiles = new \ProcessFiles();
            $sDate = date( 'Y-m-d H:i' );
            $oProcessFiles->setPrfUid( $sPkProcessFiles );
            $oProcessFiles->setProUid( $sProcessUID );
            $oProcessFiles->setUsrUid( $userUID );
            $oProcessFiles->setPrfUpdateUsrUid( '' );
            $oProcessFiles->setPrfPath( $sDirectory );
            $oProcessFiles->setPrfType( 'file' );
            $oProcessFiles->setPrfEditable( $sEditable );
            $oProcessFiles->setPrfCreateDate( $sDate );
            $oProcessFiles->save();
            $fp = fopen($sDirectory, 'w');
            $content = $aData['content'];
            fwrite($fp, $content);
            fclose($fp);
            $oProcessFile = array('prf_uid' => $oProcessFiles->getPrfUid(),
                                  'pro_uid' => $oProcessFiles->getProUid(),
                                  'usr_uid' => $oProcessFiles->getUsrUid(),
                                  'prf_update_usr_uid' => $oProcessFiles->getPrfUpdateUsrUid(),
                                  'prf_path' => $oProcessFiles->getPrfPath(),
                                  'prf_type' => $oProcessFiles->getPrfType(),
                                  'prf_editable' => $oProcessFiles->getPrfEditable(),
                                  'prf_create_date' => $oProcessFiles->getPrfCreateDate(),
                                  'prf_update_date' => $oProcessFiles->getPrfUpdateDate());
            return $oProcessFile;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return the Process Files Manager
     *
     * @param string $sProcessUID {@min 32} {@max 32}
     *
     * return array
     *
     * @access public
     */
    public function getProcessFilesManagerDownload($sProcessUID)
    {
        try {
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return the Process Files Manager
     *
     * @param string $sProcessUID {@min 32} {@max 32}
     *
     * return array
     *
     * @access public
     */
    public function uploadProcessFilesManager($sProcessUID)
    {
        try {
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of unique ids of a file
     *
     * @param string $path
     *
     * return array
     */
    public function getFileManagerUid($path)
    {
        try {
            $criteria = new \Criteria("workflow");
            $criteria->addSelectColumn(\ProcessFilesPeer::PRF_UID);
            $criteria->add(\ProcessFilesPeer::PRF_PATH, $path, \Criteria::EQUAL);
            $rsCriteria = \ProcessFilesPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $rsCriteria->next();
            return $rsCriteria->getRow();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Return the Process Files Manager
     *
     * @param string $sProcessUID {@min 32} {@max 32}
     * @param string $path
     * @param string $userUID {@min 32} {@max 32}
     * @param array  $aData
     *
     * return array
     *
     * @access public
     */
    public function updateProcessFilesManager($sProcessUID, $userUID, $aData, $path)
    {
        try {
            $arrayTaskUid = $this->getFileManagerUid($path);
            if ($aData['path'] == 'templates/' || $aData['path'] == 'folder/') {
                switch ($aData['path']) {
                    case 'templates/':
                        $sDirectory = PATH_DATA_MAILTEMPLATES . $sProcessUID . PATH_SEP . $aData['file_name'];
                        $sEditable = false;
                        break;
                    case 'folder/':
                        $sDirectory = PATH_DATA_PUBLIC . $sProcessUID . PATH_SEP . $aData['file_name'];
                        break;
                    default:
                        $sDirectory = PATH_DATA_MAILTEMPLATES . $sProcessUID . PATH_SEP . $aData['file_name'];
                        break;
                    }
            } else {
                throw (new \Exception( 'invalid value specified for `prf_path`. Expecting `templates/` or `folder/`'));
            }
            $extention = end(explode(".", $aData['file_name']));
            if ($extention == 'docx' || $extention == 'doc' || $extention == 'html' || $extention == 'php' || $extention == 'jsp' ||
                $extention == 'xlsx' || $extention == 'xls' || $extention == 'js' || $extention == 'css' || $extention == 'txt') {
                $sEditable = true;
            } else {
                $sEditable = false;
            }
            $sPkProcessFiles = \G::generateUniqueID();
            $oProcessFiles = new \ProcessFiles();
            $sDate = date( 'Y-m-d H:i' );
            $oProcessFiles->setPrfUid( $sPkProcessFiles );
            $oProcessFiles->setProUid( $sProcessUID );
            $oProcessFiles->setUsrUid( $userUID );
            $oProcessFiles->setPrfUpdateUsrUid( '' );
            $oProcessFiles->setPrfPath( $sDirectory );
            $oProcessFiles->setPrfType( 'file' );
            $oProcessFiles->setPrfEditable( $sEditable );
            $oProcessFiles->setPrfCreateDate( $sDate );
            $oProcessFiles->save();
            $fp = fopen($sDirectory, 'w');
            $content = $aData['content'];
            fwrite($fp, $content);
            fclose($fp);
            $oProcessFile = array('prf_uid' => $oProcessFiles->getPrfUid(),
                                  'pro_uid' => $oProcessFiles->getProUid(),
                                  'usr_uid' => $oProcessFiles->getUsrUid(),
                                  'prf_update_usr_uid' => $oProcessFiles->getPrfUpdateUsrUid(),
                                  'prf_path' => $oProcessFiles->getPrfPath(),
                                  'prf_type' => $oProcessFiles->getPrfType(),
                                  'prf_editable' => $oProcessFiles->getPrfEditable(),
                                  'prf_create_date' => $oProcessFiles->getPrfCreateDate(),
                                  'prf_update_date' => $oProcessFiles->getPrfUpdateDate());
            return $oProcessFile;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     *
     * @param string $sProcessUID {@min 32} {@max 32}
     * @param string $path
     *
     *
     * @access public 
     */
    public function deleteProcessFilesManager($sProcessUID, $path)
    {
        try {
            $sPath = explode("/", $path);
            $sfile = end(explode("/",$path));
            $main = implode(array_slice($sPath, -3, 1));
            $carpeta = '';
            $oProcessMap = new \processMap(new \DBConnection());
            $oProcessMap->deleteFile($sProcessUID,
                                     $main,
                                     $carpeta,
                                     $sfile);
        } catch (Exception $e) {
            throw $e;
        }
    }
}

