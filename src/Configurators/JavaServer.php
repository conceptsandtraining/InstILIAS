<?php

namespace CaT\Ilse\Configurators;

/**
 * Configure ILIAS java server part
 * 
 * Create roles
 */
class JavaServer {
	/**
	 * @var \ilSetting
	 */
	protected $gSetting;

	public function __construct($absolute_path, \ilSetting $settings) {
		$this->gSetting = $settings;
	}

	/**
	 * Activate java server
	 *
	 * @param \CaT\Ilse\Config\JavaServer $java_server
	 */
	public function javaServer(\CaT\Ilse\Config\JavaServer $java_server) {
		$this->gSetting->set("rpc_server_host", trim($java_server->host()));
		$this->gSetting->set("rpc_server_port", trim($java_server->port()));

		$this->writeSeverIni($java_server->host(), $java_server->port(), $java_server->indexPath(), $java_server->logFile()
							, $java_server->logLevel(), $java_server->numThreads(), $java_server->maxFileSize(), $java_server->iniPath()
			);
	}

	protected function writeSeverIni($host, $port, $index_path, $log_file, $log_level, $num_threads, $max_file_size, $ini_path) {
		include_once './Services/WebServices/RPC/classes/class.ilRpcIniFileWriter.php';
		$ini = new \ilRpcIniFileWriter();
		$ini->setHost($host);
		$ini->setPort($port);
		$ini->setIndexPath($index_path);
		$ini->setLogPath($log_file);
		$ini->setLogLevel($log_level);
		$ini->setNumThreads($num_threads);
		$ini->setMaxFileSize($max_file_size);

		$ini->write();

		$fh = fopen($ini_path."/ilServer.ini", "w+");
		fwrite($fh, $ini->getIniString());
		fclose($fh);
	}
}