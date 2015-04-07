<?php

namespace Veer\Components;

class remoteDownload
{
    protected $remoteLinkDb = "REMOTE_DOWNLOAD_LINK";

    protected $remoteLink;
    
    protected $remoteFile;
    
    public function __construct()
    {
        $this->remoteLink = db_parameter($this->remoteLinkDb);

        if(!empty($this->remoteLink)) {
            $this->remoteFile = app('router')->current()->lnk;

            $this->increment();

            app('veer')->forceEarlyResponse = true;

            app('veer')->earlyResponseContainer = redirect( $this->remoteLink . "/" . $this->remoteFile );
        }
    }

    protected function increment()
    {
        $remoteDownloadsCount = \File::exists(storage_path().'/app/remoteFiles.json') ?
            (array)json_decode(\File::get(storage_path().'/app/remoteFiles.json')) : array();

        $counted = isset($remoteDownloadsCount[$this->remoteLink . '/' . $this->remoteFile]) ?
            $remoteDownloadsCount[$this->remoteLink . '/' . $this->remoteFile] : 0;

        $counted++;

        $remoteDownloadsCount[$this->remoteLink . '/' . $this->remoteFile] = $counted;

        \File::put(storage_path().'/app/remoteFiles.json', json_encode($remoteDownloadsCount));
    }

}
