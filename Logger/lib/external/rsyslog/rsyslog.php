<?php
/**
 * Copyright 2012 Diego Zanella
 * This file is part of Logger Plugin for Vanilla Forums.
 *
 * Plugin is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or (at your
 * option) any later version.
 * Plugin is distributed in the hope that it will be
 * useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General
 * Public License for more details.
 * You should have received a copy of the GNU General Public License along with
 * Logger Plugin. If not, see http://opensource.org/licenses/GPL-2.0.
 *
 * @package Logger Plugin
 * @author Diego Zanella <diego@pathtoenlightenment.net>
 * @copyright Copyright (c) 2011 Diego Zanella (http://dev.pathtoenlightenment.net)
 * @license http://opensource.org/licenses/GPL-2.0 GPL 2.0
*/

require('syslogfacility.php');
require('syslogseverity.php');
require('rsyslogmessage.php');

/**
 * Sends SysLog messages to a Remote Server.
 */
class RSyslog {
	// @var Syslog destination server.
  private $LogServer;
	// @var Port to use for communication. Standard syslog port is 514.
  private $Port = 514;
	// @var Timeout of the UDP connection, in seconds.
  private $Timeout;

	/**
	 * Class constructor.
	 *
	 * @param LogServer The Name or IP Address of the remote Log Server. It can be
	 * indicated in format <server>[:<port>].
	 * @param Timeout The timeout for the UDP connection, in seconds.
	 */
  public function __construct($LogServer, $Timeout = 1) {
		$this->SetLogServer($LogServer);
		$this->SetTimeout($Timeout);
  }

	/**
	 * Setter for LogServer property.
	 */
  function SetLogServer($LogServer) {
		if(empty($LogServer)) {
			return;
		}

		// LogServer can be in format <server>:<port>
		$LogServerParts = explode(':', $LogServer);

		$this->LogServer = $LogServerParts[0];
		$this->SetPort($LogServerParts[1]);
  }

	/**
	 * Setter for Port property.
	 */
  function SetPort($Port) {
    if(((int)$Port > 0) && ((int)$Port < 65536)) {
			$this->Port = (int)$Port;
    }
  }

	/**
	 * Setter for Timeout property.
	 */
  function SetTimeout($Timeout) {
    if((int)$Timeout > 0) {
			$this->Timeout = (int)$Timeout;
    }
  }

	/**
	 * Sends a Message to the remote Log Server.
	 *
	 * @param Message An instance of RSyslogMessage class.
	 * @param LogServer The Server to which the message will be sent. If omitted,
	 * the one specified when the class was instantiated will be used instead. It
	 * can be indicated as <server>[:<port>].
	 * @param Timeout Timeout for the UDP Connection, in seconds. If omitted,
	 * the one specified when the class was instantiated will be used instead.
	 * @return True if the message was sent correctly. If not, an array containing
	 * an Error Code and an Error Message.
	 */
  function Send(RSyslogMessage $Message, $LogServer = null, $Timeout = null) {
    $this->SetLogServer($LogServer);
		$this->SetTimeout($Timeout);

    $Socket = fsockopen(sprintf('udp://%s', $this->LogServer), $this->Port, $ErrorNumber, $ErrorMessage);
    if ($Socket) {
			foreach($Message->GetMessageChunks() as $MessageChunk) {
		    fwrite($Socket, $MessageChunk);
			}
	    fclose($Socket);
	    return true;
    }
    else {
			return array($ErrorNumber, $ErrorMessage);
    }
  }
}

$Rsyslog = new RSyslog('logs.papertrailapp.com:22426');
$Msg = new RSyslogMessage('I love my honey bunny', 1, 5, time());
$Rsyslog->Send($Msg);
$Rsyslog->Send($Msg);
$Rsyslog->Send($Msg);
$Rsyslog->Send($Msg);
$Rsyslog->Send($Msg);
$Rsyslog->Send($Msg);
