<?php

/**
 * @author Daniel Gehn <me@theinad.com>
 * @version 0.3a
 * @copyright 2015 Daniel Gehn
 * @license http://opensource.org/licenses/MIT Licensed under MIT License
 */

class SynoFileHostingNDRMediathek {
    private $Url;
    private $Username;
    private $Password;
    private $HostInfo;

    private $LogPath = '/tmp/ndr-mediathek.log';
    private $LogEnabled = true;

    public function __construct($Url, $Username = '', $Password = '', $HostInfo = '') {
        $this->Url = $Url;
        $this->Username = $Username;
        $this->Password = $Password;
        $this->HostInfo = $HostInfo;

        $this->DebugLog("URL: $Url");
    }

    //This function returns download url.
    public function GetDownloadInfo() {
        $ret = FALSE;

        $this->DebugLog("GetDownloadInfo called");

        $ret = $this->Download();

        return $ret;
    }

    public function onDownloaded()
    {
    }

    public function Verify($ClearCookie = '')
    {
        $this->DebugLog("Verifying User");

        return USER_IS_PREMIUM;
    }

    //This function gets the download url
    private function Download() {
        $this->DebugLog("Getting Content of $this->Url");

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $this->Url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, DOWNLOAD_STATION_USER_AGENT);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $rawXML = curl_exec($curl);

        preg_match('#playlist:\s*\[\s*\{(.*?)\}\s*]#is', $rawXML, $matches);

        preg_match_all('#\d:\s*\{\s*src\s*:\s*["|\'](.*?)["|\']#si', $matches[1], $sources);

        $url = '';

        foreach($sources[1] as $source)
        {
            if(strpos($source, "media.ndr.de") !== false)
            {
                $url = $source;
            }
        }

        $this->DebugLog('Best format is ' . $url);

        $url = trim($url);

        $episodeTitle = '';
        $filename = '';
        $pathinfo = pathinfo($url);

        $match = array();

        if(preg_match('#var trackTitle = "(.*?)";#i', $rawXML, $match) == 1)
        {
            $episodeTitle = $match[1];
            $filename .= $episodeTitle;
        }
        else
        {
            $filename .= $pathinfo['basename'];
        }


        if(empty($filename))
        {
            $filename = $pathinfo['basename'];
        }
        else
        {
            $filename .= '.' . $pathinfo['extension'];
        }

        $this->DebugLog('Filename based on episodeTitle "' . $episodeTitle . '" is: "' . $filename . '"');

        $DownloadInfo = array();
        $DownloadInfo[DOWNLOAD_URL] = $url;
        $DownloadInfo[DOWNLOAD_FILENAME] = $filename;

        return $DownloadInfo;
    }

    private function DebugLog($message)
    {
        if($this->LogEnabled === true)
        {
            file_put_contents($this->LogPath, $message . "\n", FILE_APPEND);
        }
    }
}
?>
