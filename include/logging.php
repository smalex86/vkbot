<?php

#
#	функции для логгирования информации
#
#	автор: Смирнов Александр
#	версия: 1.2
#

// определяет ведется ли логирование или нет
$loggingStatus = 4; // статус логирования: 4 - запись всего, 3 - отладка, 2 - запись ошибок и предупреждений, 1 - запись только ошибок, 0 - логгирование не ведется
$loggingFileDefault = 'bot.log';

/**
 * Функция логгирования.
 * Ведет запись логов в файл, указанный в параметре $logFileName
 * @param int $msgStatus 0 - необходимая инфа, 1 - error, 2 - warning, 3 - debug, 4.. - другое
 * @param string $msg строка сообщения для записи в лог-файл
 * @param string $logFileName имя лог-файла, указывается только имя файла без пути, лог-файлы хранятся в корневой папке logs
 * @return boolean
 */
function setLogMsg($msgStatus, $msg, $logFileName = '') {
	global $loggingStatus;
	// проверка на необходимость записи данного сообщения
	// определяется текущим статусом логгирования и статусом сообщения
	if ($loggingStatus < $msgStatus) return false;
	// если запись нужно двигаемся дальше
	if (!$logFileName) { global $loggingFileDefault; $logFileName = $loggingFileDefault; }	
	// определяем письменное значение статуса сообщения
	switch ($msgStatus) {
		case 0:
			$msgStatusWord = ' IMPORTANT INFO ';
			break;
		case 1:
			$msgStatusWord = ' ERROR ';
			break;
		case 2:
			$msgStatusWord = ' WARNING ';
			break;
		case 3:
			$msgStatusWord = ' DEBUG INFO ';
			break;
		default:
			$msgStatusWord = ' UNKNOWN INFO ';
	}	
	// записываем сообщение в файл
	return file_put_contents(dirname(dirname(__FILE__)) . '/logs/' . $logFileName,  sprintf('%s :: %s :: %s'.PHP_EOL, date("Y-m-d H-i-s"), $msgStatusWord, $msg), FILE_APPEND);
};

// setLogMsg(2, __FILE__ . ' : ' . __LINE__ . ' -- access denied. ', 'lib.log');
// setLogMsg(0, __FILE__ . ' : ' . __LINE__ . ' -- query = ' . $query, 'lib.log');
// setLogMsg(0, __FILE__ . ' : ' . __LINE__ . ' -- affectedRows = ' . $affectedRows, 'lib.log');
// setLogMsg(1, __FILE__ . ' : ' . __LINE__ . ' -- addError = ' . $this->db->error, 'lib.log');


?>