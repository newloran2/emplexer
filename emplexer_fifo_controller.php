<?php 

class EmplexerFifoController
{
	
	private $fileDescriptor;
	private static $instance;

	public static function getInstance(){
		hd_print(__METHOD__);
		if (!isset(self::$instance)){
			hd_print(self::$instance);
			self::$instance = new EmplexerFifoController();
		}
		return self::$instance;
	}

	private function __construct()
	{
		hd_print(__METHOD__);
		$plugin_dir = dirname(__FILE__);
		if (!$this->isRuning()){
			exec("$plugin_dir/bin/emplexer_fifo_controller.sh '$plugin_dir/bin/' >> /D/dune_plugin_logs/fifo.txt 2>/dev/null &");	
		}
		
//		hd_print(__METHOD__ . ': iniciado fifo' );
	}

	public function __destruct()
	{
		hd_print(__METHOD__);
		$this->open();
		fwrite($this->fileDescriptor, "quit\n");
		fclose($this->fileDescriptor);
//		hd_print(__METHOD__ . ': já fechei fileDescriptor veja '  .$this->fileDescriptor);
	}

	public function isRuning(){
		hd_print(__METHOD__);
		// $pid = shell_exec("pidof emplexer_fifo_controller.sh");
		// $ret = isset($pid);
		// hd_print(__METHOD__ . ": resultado pid= $pid ret=$ret");
		return file_exists("/tmp/emplexer.fifo");
	}

	private function open(){
		hd_print(__METHOD__);
		if (is_null($this->fileDescriptor)){
			$this->fileDescriptor = fopen('/tmp/emplexer.fifo', 'r+');
			stream_set_blocking($this->fileDescriptor, true);	
//			hd_print(__METHOD__ . 'fileDescriptor : ' . $this->fileDescriptor );
		}		
		
	}

	public function downloadToCache($fileName, $fileUrl){
		hd_print(__METHOD__);
		// fwrite($this->fileDescriptor, "c|$fileName|$fileUrl");
		$file_path = "/persistfs/plugins_archive/emplexer/emplexer_default_archive/$fileName";
		// $file_path = "/D/emplexer/emplexer_default_archive/$fileName";
		if (!file_exists($file_path)){
			//$this->open();
			// exec("echo 'c|$fileName|$fileUrl' > /tmp/emplexer.fifo");
			//fwrite($this->fileDescriptor, "c|$fileName|$fileUrl\n");
			//hd_print(__METHOD__ . " Escrevi com c|$fileName|$fileUrl" );
			$opts = array(
				CURLOPT_BINARYTRANSFER => true,
				CURLOPT_HEADER =>false,
			);
			$doc = HD::http_get_document($fileUrl, $opts);
			$file = fopen($file_path, 'x');
			fwrite($file, $doc);
			fclose($file);
			unset($doc);
			$doc= null;

		}
	}

	public function startPlexNotify($id, $pooling, $url, $timeToMark=DEFAULT_TIME_TO_MARK){
		hd_print(__METHOD__);
		// s|id do arquivo no plex|tempo do pooling|url base do plex (http://192.168.2.9:32400/)"
		$this->open();
		fwrite($this->fileDescriptor, "s|$id|$pooling|$url|$timeToMark\n");
		// exec("echo 's|$id|$pooling|$url' > /tmp/emplexer.fifo");
//		hd_print(__METHOD__ . " Escrevi com s|$id|$pooling|$url" );
	}
	public function killPlexNotify(){
		hd_print(__METHOD__);
		$this->open();
		// exec("echo 'kill' > /tmp/emplexer.fifo");	
		fwrite($this->fileDescriptor, "kill\n");
//		hd_print(__METHOD__ . " Escrevi com kill" );
	}

	public function startDefaultPlayBack($url,  $startPosition, $plexFileId,  $timeToMark=DEFAULT_TIME_TO_MARK, $basePlexURL, $pooling=5)
	{
		hd_print(__METHOD__);
		$command = "p|". urlencode($url) . "|$startPosition|$plexFileId|$pooling|$basePlexURL|$timeToMark\n";
		$this->open();
		hd_print("iniciando startDefaultPlayBack com paramentros $command");
		//exec("/usr/bin/wget \"http://192.168.2.7/cgi-bin/do?cmd=start_file_playback&media_url=nfs%3A%2F%2F192.168.2.9%3A%2Fvolume1%2FAnimes%2FFairy+Tail%2Fs01%2FPUNCH_Fairy_Tail_-_10_HD.mkv\"");
		// HD::http_get_document("http://192.168.2.7/cgi-bin/do?cmd=start_file_playback&media_url=nfs%3A%2F%2F192.168.2.9%3A%2Fvolume1%2FAnimes%2FFairy+Tail%2Fs01%2FPUNCH_Fairy_Tail_-_10_HD.mkv");
		fwrite($this->fileDescriptor, $command);
	}


}

?>