<?php

/**
 * @author Daniel Gehn <me@theinad.com>
 * @version 0.3a
 * @copyright 2015 Daniel Gehn
 * @license http://opensource.org/licenses/MIT Licensed under MIT License
 */

require_once 'vendor/provider.php';

class TheiNaDNDRMediathek extends TheiNaDProvider {

    protected $LogPath = '/tmp/ndr-mediathek.log';

    //This function returns download url.
    public function GetDownloadInfo()
    {
        return $this->Download();
    }

    //This function gets the download url
    protected function Download()
    {
        $hits = array();

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

        $DownloadInfo = array();
        $DownloadInfo[DOWNLOAD_URL] = trim($url);

        return $DownloadInfo;
    }

}

return new TheiNaDNDRMediathek();
