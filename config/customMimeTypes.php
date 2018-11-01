<?php

/**
 * This is a partial list of the known types, add the type you want to be recognized, 
 * this affects the whole application globally.
 * 
 * https://www.iana.org/assignments/media-types/media-types.xml
 */
return [
    'dat' => 'text/plain',
    'doc' => ['application/msword', 'text/html'],
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'exe' => ['application/x-msdownload', 'application/x-dosexec'],
    'gif' => 'image/gif',
    'htm' => 'text/html',
    'html' => 'text/html',
    'jpeg' => 'image/jpeg',
    'jpg' => 'image/jpeg',
    'mp3' => 'audio/mpeg',
    'mp4' => 'video/mp4',
    'ppt' => 'application/vnd.ms-office',
    'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'pm' => 'text/plain',
    'pmt' => 'text/plain',
    'pmx' => 'application/xml',
    'po' => 'text/x-po',
    'pdf' => 'application/pdf',
    'png' => 'image/png',
    'php' => 'text/x-php',
    'rar' => 'application/x-rar',
    'txt' => 'text/plain',
    'wmv' => ['video/x-ms-asf', 'video/x-ms-wmv'],
    'xls' => ['application/vnd.ms-excel', 'text/plain'],
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'zip' => 'application/zip',
];
