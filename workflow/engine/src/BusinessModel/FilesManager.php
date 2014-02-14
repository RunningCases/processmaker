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
     * @param string $path
     *
     * return array
     *
     * @access public
     */
    public function getProcessFilesManagerPath($sProcessUID, $path)
    {
        try {
            $sMainDirectory = current(explode("/", $path));
            if (strstr($path,'/')) {
                $sSubDirectory = substr($path, strpos($path, "/")+1). PATH_SEP ;
            } else {
                $sSubDirectory = '';
            }
            switch ($sMainDirectory) {
                case 'templates':
                    $sDirectory = PATH_DATA_MAILTEMPLATES . $sProcessUID . PATH_SEP . $sSubDirectory;
                    break;
                case 'public':
                    $sDirectory = PATH_DATA_PUBLIC . $sProcessUID . PATH_SEP . $sSubDirectory;
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
                        $aTheFiles[] = array('name' => $sObject,
                                             'type' => "folder",
                                             'path' => $sDirectory);
                    } else {
                        $aAux = pathinfo($sPath);
                        $aAux['extension'] = (isset($aAux['extension'])?$aAux['extension']:'');
                        $aFiles[] = array('FILE' => $sObject, 'EXT' => $aAux['extension'] );
                    }
                }
            }
            foreach ($aFiles as $aFile) {
                    $extention = end(explode(".", $aFile['FILE']));
                    if ($extention == 'docx' || $extention == 'doc' || $extention == 'html' || $extention == 'php' || $extention == 'jsp' || $extention == 'xlsx' || $extention == 'xls' || $extention == 'js' || $extention == 'css' || $extention == 'txt') {
                        $sEditable = true;
                    } else {
                        $sEditable = false;
                    }
                    $aTheFiles[] = array('name' => $aFile['FILE'],
                                         'type' => "file",
                                         'path' => $sDirectory,
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
            $aData['path'] = rtrim($aData['path'], '/') . '/';
            $sMainDirectory = current(explode("/", $aData['path']));
            if ($sMainDirectory != 'public' && $sMainDirectory != 'templates') {
                throw (new \Exception( 'invalid value specified for `prf_path`. Expecting `templates/` or `public/`'));
            }
            if (strstr($aData['path'],'/')) {
                $sSubDirectory = substr($aData['path'], strpos($aData['path'], "/")+1) ;
            } else {
                $sSubDirectory = '';
            }
            switch ($sMainDirectory) {
                case 'templates':
                    $sDirectory = PATH_DATA_MAILTEMPLATES . $sProcessUID . PATH_SEP . $sSubDirectory . $aData['file_name'];
                    $sCheckDirectory = PATH_DATA_MAILTEMPLATES . $sProcessUID . PATH_SEP . $sSubDirectory;
                    $sEditable = false;
                    break;
                case 'public':
                    $sDirectory = PATH_DATA_PUBLIC . $sProcessUID . PATH_SEP . $sSubDirectory . $aData['file_name'];
                    $sCheckDirectory = PATH_DATA_PUBLIC . $sProcessUID . PATH_SEP . $sSubDirectory;
                    break;
                default:
                    $sDirectory = PATH_DATA_MAILTEMPLATES . $sProcessUID . PATH_SEP . $sSubDirectory . $aData['file_name'];
                    break;
            }
            $extention = end(explode(".", $aData['file_name']));
            if ($extention == 'docx' || $extention == 'doc' || $extention == 'html' || $extention == 'php' || $extention == 'jsp' ||
                $extention == 'xlsx' || $extention == 'xls' || $extention == 'js' || $extention == 'css' || $extention == 'txt') {
                $sEditable = true;
            } else {
                $sEditable = false;
            }
            \G::verifyPath($sCheckDirectory, true);
            if (file_exists(PATH_SEP.$sDirectory)) {
                throw (new \Exception( 'The file: '. $sDirectory . ' exists.'));
            }
            $sPkProcessFiles = \G::generateUniqueID();
            $oProcessFiles = new \ProcessFiles();
            $sDate = date('Y-m-d H:i');
            $oProcessFiles->setPrfUid( $sPkProcessFiles );
            $oProcessFiles->setProUid( $sProcessUID );
            $oProcessFiles->setUsrUid( $userUID );
            $oProcessFiles->setPrfUpdateUsrUid( '' );
            $oProcessFiles->setPrfPath( $sDirectory );
            $oProcessFiles->setPrfType('file');
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
            $path = rtrim($path, '/') . '/';
            $sMainDirectory = current(explode("/", $path));
            if ($sMainDirectory != 'public' && $sMainDirectory != 'templates') {
                throw (new \Exception( 'invalid value specified for `prf_path`. Expecting `templates/` or `public/`'));
            }
            if (strstr($path,'/')) {
                $sSubDirectory = substr($path, strpos($path, "/")+1) ;
            } else {
                $sSubDirectory = '';
            }
            switch ($sMainDirectory) {
                case 'templates':
                    $sDirectory = PATH_DATA_MAILTEMPLATES . $sProcessUID . PATH_SEP . $sSubDirectory . $aData['file_name'];
                    $sEditable = false;
                    break;
                case 'public':
                    $sDirectory = PATH_DATA_PUBLIC . $sProcessUID . PATH_SEP . $sSubDirectory . $aData['file_name'];
                    break;
                default:
                    $sDirectory = PATH_DATA_MAILTEMPLATES . $sProcessUID . PATH_SEP . $aData['file_name'];
                    break;
            }
            $arrayTaskUid = $this->getFileManagerUid($sDirectory);
            if (!$arrayTaskUid) {
                throw (new \Exception( 'invalid value specified for `path`.'));
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
            $sDate = date('Y-m-d H:i');
            $oProcessFiles->setPrfUid( $sPkProcessFiles);
            $oProcessFiles->setProUid( $sProcessUID );
            $oProcessFiles->setUsrUid( $userUID );
            $oProcessFiles->setPrfUpdateUsrUid( '' );
            $oProcessFiles->setPrfPath( $sDirectory );
            $oProcessFiles->setPrfType('file');
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
            $sMainDirectory = current(explode("/", $path));
            if ($sMainDirectory != 'public' && $sMainDirectory != 'templates') {
                throw (new \Exception( 'invalid value specified for `prf_path`. Expecting `templates/` or `public/`'));
            }
            if ($sMainDirectory == 'templates') {
                $sMainDirectory = 'mailTemplates';
            }
            $sfile = end(explode("/",$path));
            $sSubDirectorytemp = substr($path, strpos($path, "/")+1);
            if (strstr($sSubDirectorytemp,'/')) {
                $sSubDirectory = str_replace('/'.$sfile,"",$sSubDirectorytemp);
                $sSubDirectoryCheck = str_replace($sfile,"",$sSubDirectorytemp);
            } else {
                $sSubDirectory = '';
                $sSubDirectoryCheck = '';
            }
            switch ($sMainDirectory) {
                case 'mailTemplates':
                    $sDirectory = PATH_DATA_MAILTEMPLATES . $sProcessUID . PATH_SEP . $sSubDirectoryCheck . $sfile;
                    $sEditable = false;
                    break;
                case 'public':
                    $sDirectory = PATH_DATA_PUBLIC . $sProcessUID . PATH_SEP . $sSubDirectoryCheck . $sfile;
                    break;
                default:
                    $sDirectory = PATH_DATA_MAILTEMPLATES . $sProcessUID . PATH_SEP . $sfile;
                    break;
            }
            $arrayTaskUid = $this->getFileManagerUid($sDirectory);
            if (!$arrayTaskUid){
                throw (new \Exception( 'invalid value specified for `path`.'));
            }
            $oProcessMap = new \processMap(new \DBConnection());
            $oProcessMap->deleteFile($sProcessUID,
                                     $sMainDirectory,
                                     $sSubDirectory,
                                     $sfile);
            $c = new \Criteria("workflow");
            $c->add(\ProcessFilesPeer::PRF_PATH, $sDirectory, \Criteria::EQUAL);
            $rs = \ProcessFilesPeer::doDelete($c);
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
    public function downloadProcessFilesManager($sProcessUID, $path)
    {
        try {
            $sMainDirectory = current(explode("/", $path));
            if ($sMainDirectory != 'public' && $sMainDirectory != 'templates') {
                throw (new \Exception( 'invalid value specified for `prf_path`. Expecting `templates/` or `public/`'));
            }
            if ($sMainDirectory == 'templates') {
                $sMainDirectory = 'mailTemplates';
            }
            $sfile = end(explode("/",$path));
            $sSubDirectorytemp = substr($path, strpos($path, "/")+1);
            if (strstr($sSubDirectorytemp,'/')) {
                $sSubDirectory = str_replace('/'.$sfile,"",$sSubDirectorytemp);
                $sSubDirectoryCheck = str_replace($sfile,"",$sSubDirectorytemp);
            } else {
                $sSubDirectory = '';
                $sSubDirectoryCheck = '';
            }
            switch ($sMainDirectory) {
                case 'mailTemplates':
                    $sDirectory = PATH_DATA_MAILTEMPLATES . $sProcessUID . PATH_SEP . $sSubDirectoryCheck . $sfile;
                    $sEditable = false;
                    break;
                case 'public':
                    $sDirectory = PATH_DATA_PUBLIC . $sProcessUID . PATH_SEP . $sSubDirectoryCheck . $sfile;
                    break;
                default:
                    $sDirectory = PATH_DATA_MAILTEMPLATES . $sProcessUID . PATH_SEP . $sfile;
                    break;
            }
            $arrayTaskUid = $this->getFileManagerUid($sDirectory);
            if (!$arrayTaskUid) {
                throw (new \Exception( 'invalid value specified for `path`.'));
            }
            /*
            This is usefull when you are downloading big files, as it
            will prevent time out of the script :
            */
            set_time_limit(0);
            ini_set('display_errors',true);//Just in case we get some errors, let us know....
            $fp = fopen ($sDirectory, 'w+');//This is the file where we save the information
            $ch = curl_init($sDirectory);//Here is the file we are downloading
            curl_setopt($ch, CURLOPT_TIMEOUT, 50);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
        } catch (Exception $e) {
            throw $e;
        }
    }
}

