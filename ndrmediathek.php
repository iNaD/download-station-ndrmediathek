<?php

/**
 * @author Daniel Gehn <me@theinad.com>
 * @version 0.3b
 * @copyright 2015 Daniel Gehn
 * @license http://opensource.org/licenses/MIT Licensed under MIT License
 */

require_once 'provider.php';

class SynoFileHostingNDRMediathek extends TheiNaDProvider {
    protected $LogPath = '/tmp/ndr-mediathek.log';

    //This function gets the download url
    public function GetDownloadInfo() {
        $this->DebugLog("Getting Content of $this->Url");

        $rawXML = $this->curlRequest($this->Url);

        if($rawXML === null) {
            return false;
        }

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
        $DownloadInfo[DOWNLOAD_FILENAME] = $this->safeFilename($filename);

        return $DownloadInfo;
    }

 }
?>
