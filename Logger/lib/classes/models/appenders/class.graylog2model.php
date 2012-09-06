<?php if (!defined('APPLICATION')) exit();
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

// Load GELF Libraries
require(LOGGER_PLUGIN_EXTERNAL_PATH . '/Graylog2-gelf-php/GELFMessage.php');
require(LOGGER_PLUGIN_EXTERNAL_PATH . '/Graylog2-gelf-php/GELFMessagePublisher.php');

/**
 * Graylog2 Log Model
 *
 * @package LoggerPlugin
 */
class Graylog2Model extends Gdn_Model {
	/// @var GELFMessagePublisher
	protected $GELFMessagePublisher;

	/**
	 * @var array An associative array mapping Log4php Log levels to the
	 * equivalents used by Graylog2. Note that Graylog2 uses Syslog level coding,
	 * which has more levels than the ones provided by Log4php. For this reason,
	 * not all Log levels match, and some had to be ignored.
	 */
	protected $LogLevelMap = array(
		LoggerLevel::FATAL => GELFMessage::EMERGENCY,
		// Log4php has less Log levels. To err on the safe side, Fatal has been
		// translated to Emergency, while Alert and Critical have been ignored.
    // => GELFMessage::ALERT,
    // => GELFMessage::CRITICAL = 2,
		LoggerLevel::ERROR => GELFMessage::ERROR,
		LoggerLevel::WARN => GELFMessage::WARNING,
		LoggerLevel::INFO => GELFMessage::NOTICE,
		LoggerLevel::DEBUG => GELFMessage::INFO,
		LoggerLevel::TRACE => GELFMessage::DEBUG,
	);

	const GRAYLOG2_DEFAULT_PORT = 12201;
	const GRAYLOG2_DEFAULT_CHUNK_SIZE = 1420;


	/**
	 * Set Validation Rules that apply when saving a new row in Cron Jobs History.
	 *
	 * @return void
	 */
	protected function _SetGraylog2ValidationRules() {
		//$this->Validation = &new Gdn_Validation();

		// Set additional Validation Rules here. Please note that formal validation
		// is done automatically by base Model Class, by retrieving Schema
		// Information.
	}

	protected function GetPublisher() {
		if(empty($this->GELFMessagePublisher)) {
			// Instantiate the Message Publisher that will be used to communicate with
			// Graylog2 Server
			$this->GELFMessagePublisher = new GELFMessagePublisher($this->HostName,
																														 $this->Port,
																														 $this->ChunkSize);
		}

		return $this->GELFMessagePublisher;
	}

	/**
	 * Defines the related database table name. Table name must be passed as a
	 * parameter.
	 *
	 * @param string HostName The name or IP Address of Graylog2 Server.
	 * @param int Port The Port to use to communicate with Graylog2 Server.
	 * @param int ChunkSize The chunk size to use to communicate with Graylog2 Server.
	 */
	public function __construct($HostName, $Port, $ChunkSize) {
		parent::__construct();

		$this->HostName = $HostName;
		$this->Port = $Port;
		$this->ChunkSize = $ChunkSize;

		$this->_SetGraylog2ValidationRules();
	}

	/**
	 * Translates a Log4php Log Level into the correspondant Graylog2 level.
	 *
	 * @param LogLevel The Log4php Log Level to be translated.
	 * @return A Graylog2 Log Level.
	 */
	protected function GetGraylLog2Level($LogLevel) {
		return $this->LogLevelMap[$LogLevel];
	}

	/**
	 * Builds a GELF Message that will be sent to a Graylog2 Server.
	 *
	 * @param LogFields An associative array of fields describing a Log Entry.
	 * @return A GELF Message instance.
	 */
	protected function BuildGELFMessage(array &$LogFields) {
		$Message = new GELFMessage();

		$Message->setAdditional('LoggerName', $LogFields['LoggerName']);
		// TODO Remove call to GetGraylLog2Level and use Level->getSysLogEquivalent() in Controller class
		$Message->setLevel($this->GetGraylLog2Level($LogFields['Level']));
		$Message->setShortMessage($LogFields['Message']);
		$Message->setAdditional('Thread', $LogFields['Thread']);
		$Message->setAdditional('ClassName', $LogFields['ClassName']);
		$Message->setAdditional('MethodName', $LogFields['MethodName']);
		$Message->setFile($LogFields['FileName']);
		$Message->setLine($LogFields['LineNumber']);
		$Message->setTimestamp($LogFields['TimeStamp']);

		$Message->setFullMessage($LogFields['Exception']);

		// This value is not produced, nor managed by Log4php, but Graylog2 can
		// accept it, therefore it's passed to the server as an additional detail.
		$Message->setHost(gethostname());

		return $Message;
	}

	/**
	 * Sends a GELF Message to a Graylog2 Server.
	 *
	 * @param Message The GELF Message to be sent.
	 * @return True if message was sent correctly, False otherwise.
	 */
	protected function PublishMessage(GELFMessage $Message) {
		return $this->GetPublisher()->publish($Message);
	}

	/**
	 * Save a Log Entry to GrayLog2 Server.
	 *
   * @param array LogFields An associative array of Log Entry fields.
   * @return True if the message was sent correctly, False otherwise.
	 */
	public function Save(&$LogFields) {
		// Validate posted data
		//if(!$this->Validate($LogFields)) {
		//	return false;
		//}

		$Message = $this->BuildGELFMessage($LogFields);

		try {
			return $this->PublishMessage($Message);
		}
		catch(Exception $e) {
			// TODO Find a graceful way to handle and display any exception. Logging it could be complicated, since the log will trigger this method again, leading to an infinite recursion.
			return false;
		}
	}
}
