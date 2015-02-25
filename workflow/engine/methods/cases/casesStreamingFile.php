<?php
/**
 * casesStreamingFile.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 */

/**
 * File for get Streaming file type audio and video
 * return in header code 206
 *
 * Created by Dev: Ronald Quenta
 * E-mail: ronald.otn@gmail.com
 */

$actionAjax = isset( $_REQUEST['actionAjax'] ) ? $_REQUEST['actionAjax'] : null;

if ($actionAjax == "streaming") {

    $app_uid = isset( $_REQUEST['a'] ) ? $_REQUEST['a'] : null;
    $inp_doc_uid = isset( $_REQUEST['d'] ) ? $_REQUEST['d'] : null;
    $oAppDocument = new \AppDocument();

    if (! isset( $fileData['version'] )) {
        //Load last version of the document
        $docVersion = $oAppDocument->getLastAppDocVersion( $inp_doc_uid );
    } else {
        $docVersion = $fileData['version'];
    }

    $oAppDocument->Fields = $oAppDocument->load( $inp_doc_uid, $docVersion );

    $sAppDocUid  = $oAppDocument->getAppDocUid();
    $iDocVersion = $oAppDocument->getDocVersion();
    $info = pathinfo( $oAppDocument->getAppDocFilename() );
    $ext  = (isset($info['extension'])?$info['extension']:'');

    //$app_uid = \G::getPathFromUID($oAppDocument->Fields['APP_UID']);
    $file = \G::getPathFromFileUID($oAppDocument->Fields['APP_UID'], $sAppDocUid);

    $realPath  = PATH_DOCUMENT .  $app_uid . '/' . $file[0] . $file[1] . '_' . $iDocVersion . '.' . $ext;
    $realPath1 = PATH_DOCUMENT . $app_uid . '/' . $file[0] . $file[1] . '.' . $ext;

    if (file_exists( $realPath )) {
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $realPath);
        finfo_close($finfo);
        if ($ext == "mp3") {
            $mimeType = "audio/mpeg";
        }
        rangeDownload($realPath,$mimeType);
    } elseif (file_exists( $realPath1 )) {
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $realPath1);
        finfo_close($finfo);
        if ($ext == "mp3") {
            $mimeType = "audio/mpeg";
        }
        rangeDownload($realPath1,$mimeType);
    } else {
        header ("HTTP/1.0 404 Not Found");
        return;
    }
    exit(0);
}

exit;

function rangeDownload($location,$mimeType)
{
    if (!file_exists($location))
    {
        header ("HTTP/1.0 404 Not Found");
        return;
    }
    //echo ($mimeType);die;
    $size  = filesize($location);
    $time  = date('r', filemtime($location));

    $fm = @fopen($location, 'rb');
    if (!$fm)
    {
        header ("HTTP/1.0 505 Internal server error");
        return;
    }

    $begin  = 0;
    $end  = $size - 1;

    if (isset($_SERVER['HTTP_RANGE']))
    {
        if (preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches))
        {
            $begin  = intval($matches[1]);
            if (!empty($matches[2]))
            {
                $end  = intval($matches[2]);
            }
        }
    }

    header('HTTP/1.0 206 Partial Content');
    header("Content-Type: $mimeType");
    header('Cache-Control: public, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Accept-Ranges: bytes');
    header('Content-Length:' . (($end - $begin) + 1));
    if (isset($_SERVER['HTTP_RANGE']))
    {
        header("Content-Range: bytes $begin-$end/$size");
    }
    header("Content-Disposition: inline; filename=$location");
    header("Content-Transfer-Encoding: binary");
    header("Last-Modified: $time");

    $cur  = $begin;
    fseek($fm, $begin, 0);

    while(!feof($fm) && $cur <= $end && (connection_status() == 0))
    {
        set_time_limit(0);
        print fread($fm, min(1024 * 16, ($end - $cur) + 1));
        $cur += 1024 * 16;
        flush();
    }
}
