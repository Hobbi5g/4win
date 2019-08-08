<?

// database singleton

class DatabaseConnection {
	
	// Содержит экземпляр класса
	private static $instance;
	static $conn;

	// Закрытый конструктор; предотвращает прямой доступ к созданию объекта
	private function __construct() {
			// establish connection
			$this->conn = DB::connect(CONNECTION_STRING);
			$this->conn->setFetchmode(DB_FETCHMODE_ASSOC);
			$this->conn->query('SET NAMES utf8');
	}

	// Метод синглтон
	public static function singleton() {
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}

		return self::$instance;
	}

	// Предотвращает клонирование экземпляра класса
	public function __clone() {
		trigger_error('Clone error', E_USER_ERROR);
	}	
	
	
}

