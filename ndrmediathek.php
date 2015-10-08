<?php

/**
 * @author Daniel Gehn <me@theinad.com>
 * @version 0.3c
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

        preg_match('#itemprop="contentUrl" content="(.*?)"#is', $rawXML, $sources);

        $url = '';

        $source = $sources[1];

        if(strpos($source, "media.ndr.de") !== false)
        {
            $url = $source;
        }

        $this->DebugLog('Best format is ' . $url);

        $url = trim($url);

        $episodeTitle = '';
        $filename = '';
        $pathinfo = pathinfo($url);

        $match = array();

        if(preg_match('#itemprop="name" content="(.*?)"#is', $rawXML, $match) == 1)
        {
            $episodeTitle = str_replace("Video: ", "", $match[1]);
            $filename .= $episodeTitle;
        }

        if($filename == "" && preg_match('#itemprop="headline">(.*?)<\/#is', $rawXML, $headline) == 1)
        {
            $episodeTitle = $headline[1];

            if(preg_match('#itemprop="startDate" content="(.*?)">#is', $rawXML, $startDate) == 1)
            {
                $episodeTitle .= " vom " . date("d.m.Y", strtotime($startDate[1]));
            }

            $filename .= $episodeTitle;
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
