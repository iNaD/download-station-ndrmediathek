<?php

/**
 * Abstract class Selfupdateable
 *
 * Forwards every function call to the dynamically updated provider.
 *
 * @author Daniel Gehn <me@theinad.com>
 * @version 0.1
 * @copyright 2015 Daniel Gehn
 * @license http://opensource.org/licenses/MIT Licensed under MIT License
 */

abstract class TheiNaDSelfupdateable {

    /**
     * Path where the log file is written if logging is enabled
     * @var string
     */
    protected $LogPath = '/tmp/theinad-selfupdate.log';

    /**
     * If set to true, debugging is enabled an log is written
     * @var boolean
     */
    protected $LogEnabled = false;

    /**
     * Holds the instance of the real provider
     * @var null
     */
    protected $provider = null;

    /**
     * Name of the provider
     * @var string
     */
    protected $name = '';

    /**
     * Path where every provider is downloaded and extracted to
     * @var string
     */
    protected $providersPath = '/tmp/providers';

    /**
     * Provider's source
     * @var string
     */
    protected $sourceUrl = 'http://192.168.0.10/download-station/providers';

    /**
     * Catches Download Stations's parameters, loads the provider and initializes it with given parameters.
     * @param string $Url      Download Url
     * @param string $Username Login Username
     * @param string $Password Login Password
     * @param string $HostInfo Hoster Info
     */
    public function __construct($Url, $Username = '', $Password = '', $HostInfo = '') {
        $this->provider = $this->loadProvider();

        $this->DebugLog('Initializing Provider with ' . json_encode(array($Url, $Username, $Password, $HostInfo)));

        $this->provider->init($Url, $Username = '', $Password = '', $HostInfo = '', $this->LogEnabled);
    }

    /**
     * Forwards every function call to the real provider
     * @param  string $name         Name of the function
     * @param  array $arguments     Array of arguments
     * @return mixed                Result of the function call
     */
    public function __call($name, $arguments)
    {
        $this->DebugLog('Calling ' . $name . ' with ' . json_encode($arguments));

        return call_user_func_array(array($this->provider, $name), $arguments);
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

    /**
     * Path of the real provider
     * @return string
     */
    protected function getProvidersPath()
    {
        return $this->providersPath . '/' . $this->name;
    }

    /**
     * builds the provider's filename
     * @param  boolean $path full path
     * @return string
     */
    protected function getProviderFilename($path = false)
    {
        return ( $path === true ? $this->getProvidersPath() . '/' : '' ) . strtolower($this->name) . '.php';
    }

    /**
     * builds the provider's archive name
     * @param  boolean $path full path
     * @return string
     */
    protected function getProviderArchiveFilename($path = false)
    {
        return ( $path === true ? $this->getProvidersPath() . '/' : '' ) . strtolower($this->name) . '.tar.gz';
    }

    /**
     * Try to load the provider
     * @return Object
     */
    protected function loadProvider()
    {
        if(!file_exists($this->getProvidersPath()))
        {
            mkdir($this->getProvidersPath(), 0777, true);
        }

        if(!file_exists($this->getProviderFilename(true)) || $this->checkVersion() === false)
        {
            $this->DebugLog("Provider outdated or doesn't exist");

            $this->getCurrentVersion();
        }

        return include $this->getProviderFilename(true);
    }

    /**
     * Checks the md5 hashes of local and remote file
     * @return boolean true if version is latest, false if not
     */
    protected function checkVersion()
    {
        $md5hash = file_get_contents($this->sourceUrl . '/' . $this->getProviderFilename() . '.md5');

        return $md5hash === md5_file($this->getProviderFilename(true));
    }

    /**
     * Downloads and extracts the real provider
     * @return void
     */
    protected function getCurrentVersion()
    {
        $this->DebugLog('Downloading Provider');

        file_put_contents($this->getProviderArchiveFilename(true), file_get_contents($this->sourceUrl . '/' . $this->getProviderArchiveFilename()));

        $this->DebugLog('Extracting Provider');

        exec('cd ' . $this->getProvidersPath() . ' && tar -zxvf ' . $this->getProviderArchiveFilename());

        unlink($this->getProviderArchiveFilename(true));

        $this->DebugLog('Extracted Provider');
    }

}
?>
