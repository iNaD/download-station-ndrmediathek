<?php

/**
 * @author Daniel Gehn <me@theinad.com>
 * @version 0.3a
 * @copyright 2015 Daniel Gehn
 * @license http://opensource.org/licenses/MIT Licensed under MIT License
 */

require_once 'vendor/selfupdateable.php';

class SynoFileHostingNDRMediathek extends TheiNaDSelfupdateable {

    protected $LogPath = '/tmp/ndr-mediathek.log';
    protected $LogEnabled = false;

    protected $name = 'ndrmediathek';

}
?>
