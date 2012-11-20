<?php 

class EmplexerFifoController
{
	
	private $fileDescriptor;
	private static $instance;

	public static function getInstance(){
		if (!isset(self::$instance)){
			hd_print(self::$instance);
			self::$instance = new EmplexerFifoController();
		}
		return self::$instance;
	}

	private function __construct()
	{
		$plugin_dir = dirname(__FILE__);
		if (!$this->isRuning()){
			exec("$plugin_dir/bin/emplexer_fifo_controller.sh '$plugin_dir/bin/' >> /tmp/fifo.txt 2>/dev/null &");	
		}
		
		hd_print(__METHOD__ . ': iniciado fifo' );
	}

	public function __destruct()
	{
		$this->open();
		fwrite($this->fileDescriptor, "quit\n");
		fclose($this->fileDescriptor);
		hd_print(__METHOD__ . ': já fechei fileDescriptor veja '  .$this->fileDescriptor);
	}

	public function isRuning(){
		// $pid = shell_exec("pidof emplexer_fifo_controller.sh");
		// $ret = isset($pid);
		// hd_print(__METHOD__ . ": resultado pid= $pid ret=$ret");
		return file_exists("/tmp/emplexer.fifo");
	}

	private function open(){
		if (is_null($this->fileDescriptor)){
			$this->fileDescriptor = fopen('/tmp/emplexer.fifo', 'r+');
			stream_set_blocking($this->fileDescriptor, true);	
			hd_print(__METHOD__ . 'fileDescriptor : ' . $this->fileDescriptor );
		}		
		
	}
	public function downloadToCache($fileName, $fileUrl){
		
		// fwrite($this->fileDescriptor, "c|$fileName|$fileUrl");
		if (!file_exists("/persistfs/plugins_archive/emplexer/emplexer_default_archive/$fileName")){
			//$this->open();
			// exec("echo 'c|$fileName|$fileUrl' > /tmp/emplexer.fifo");
			//fwrite($this->fileDescriptor, "c|$fileName|$fileUrl\n");
			//hd_print(__METHOD__ . " Escrevi com c|$fileName|$fileUrl" );
			$opts = array(
				CURLOPT_BINARYTRANSFER => true,
				CURLOPT_HEADER =>false,
			);
			$doc = HD::http_get_document($fileUrl, $opts);
			$file = fopen("/persistfs/plugins_archive/emplexer/emplexer_default_archive/$fileName", 'x');
			fwrite($file, $doc);
			fclose($file);
			unset($doc);
			$doc= null;

		}
	}

	public function startPlexNotify($id, $pooling, $url){
		// s|id do arquivo no plex|tempo do pooling|url base do plex (http://192.168.2.9:32400/)"
		$this->open();
		fwrite($this->fileDescriptor, "s|$id|$pooling|$url\n");
		// exec("echo 's|$id|$pooling|$url' > /tmp/emplexer.fifo");
		hd_print(__METHOD__ . " Escrevi com s|$id|$pooling|$url" );
	}
	public function killPlexNotify(){
		$this->open();
		// exec("echo 'kill' > /tmp/emplexer.fifo");	
		fwrite($this->fileDescriptor, "kill\n");
		hd_print(__METHOD__ . " Escrevi com kill" );

	}


}

?>