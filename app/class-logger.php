<?

// logger singleton

class Logger
{
	// Содержит экземпляр класса
	private static $instance;
	var $conn;
	
	// Закрытый конструктор; предотвращает прямой доступ к созданию объекта
	private function __construct() 
	{
		$connection = DatabaseConnection::singleton();
		$this->conn = $connection->conn;
	}

	// Метод синглтон
	public static function singleton() 
	{
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}

		return self::$instance;

	}

	// Предотвращает клонирование экземпляра класса
	public function __clone()
	{
		trigger_error('Clone error', E_USER_ERROR);
	}


	public function log_event($event = 'redirect', $description = '')
	{
		$this->conn->query("INSERT INTO log(created_at, event_type, description) VALUES (NOW(), ?, ?)", array($event, $description));
	}


}
