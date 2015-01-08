<?php

/**
 * @author Daniel Gehn <me@theinad.com>
 * @version 0.1
 * @copyright 2015 Daniel Gehn
 * @license http://opensource.org/licenses/MIT Licensed under MIT License
 */

abstract class TheiNaDProvider {
    protected $Url;
    protected $Username;
    protected $Password;
    protected $HostInfo;

    protected $LogPath = '/tmp/provider.log';
    protected $LogEnabled = false;

    /**
     * Is called by the selfupdate container class
     * @param string $Url           Download Url
     * @param string $Username      Login Username
     * @param string $Password      Login Password
     * @param string $HostInfo      Hoster Info
     * @param boolean $debug        Debug enabled or disabled
     * @return void
     */
    public function init($Url, $Username = '', $Password = '', $HostInfo = '', $debug = false)
    {
        $this->Url = $Url;
        $this->Username = $Username;
        $this->Password = $Password;
        $this->HostInfo = $HostInfo;
        $this->LogEnabled = $debug;

        $this->DebugLog("URL: $Url");
    }

    /**
     * Is called after the download finishes
     * @return void
     */
    public function onDownloaded()
    {
    }

    /**
     * Verifies the Account
     * @param string $ClearCookie
     * @return integer
     */
    public function Verify($ClearCookie = '')
    {
        return USER_IS_PREMIUM;
    }

    /**
     * Returns the Download URI to be used by Download Station
     * @return mixed
     */
    public function GetDownloadInfo()
    {
        $DownloadInfo = array();
        $DownloadInfo[DOWNLOAD_URL] = $this->Url;

        return $DownloadInfo;
    }

    /**
     * Logs debug messages to the logfile, if log is enabled
     * @param string $message Message to be logged
     */
    protected function DebugLog($message)
    {
        if($this->LogEnabled === true)
        {
            file_put_contents($this->LogPath, $message . "\n", FILE_APPEND);
        }
    }

}
