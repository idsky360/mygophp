<?php
class Log{
	
	private static $logPath;

	public static function record($logStr,$logFile){
		$logPath = C::getByKey('common.log_path');
		self::$logPath = $logPath ? $logPath : "/tmp/mygo";
		self::write($logStr,$logFile);
	}

	private static function write($logStr,$logFile){
		$logFile = self::$logPath."/".$logFile.".".date("Ymd").".log";
		$isNewFile = !file_exists($logFile);
		$fp = fopen($logFile, 'a');
        if (flock($fp, LOCK_EX)) {
            if ($isNewFile) {
                chmod($logFile, 0666);
            }
            fwrite($fp, $logStr . "\n");
            flock($fp, LOCK_UN);
        }
        fclose($fp);
	}
}

