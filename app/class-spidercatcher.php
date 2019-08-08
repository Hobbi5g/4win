<?

// logger singleton

class SpiderCatcher
{
	// Содержит экземпляр класса
	private static $instance;
	var $conn;

	// Закрытый конструктор; предотвращает прямой доступ к созданию объекта
	private function __construct() {
		$connection = DatabaseConnection::singleton();
		$this->conn = $connection->conn;
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

	public function log_visitor() {

		$client_ip = getRemoteIPAddress();
		$numeric_ip = ip2long($client_ip);

		if ($client_ip == '127.0.0.1') {
			return;
		}

		$request_url = $_SERVER['REQUEST_URI'];
		$user_agent = $_SERVER['HTTP_USER_AGENT'];

		//$this->conn->query("INSERT INTO log(created_at, event_type, description) VALUES (NOW(), ?, ?)", array($event, $description));

		$records_count = $this->conn->getOne("SELECT count(*) FROM log WHERE user_agent = ?", array($user_agent));
		if ($records_count == 0) {
			$this->conn->query("INSERT INTO log(client_ip, numeric_ip, request_url, created_at, user_agent) values (?,?,?, NOW(), ?)",
				array($client_ip, $numeric_ip, $request_url, $user_agent));
		}
	}


/*
	user_agent

	Googlebot-Image/1.0
	Mozilla/5.0 (compatible; AhrefsBot/6.1; +http://ahrefs.com/robot/)
	Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)
	Mozilla/5.0 (compatible; coccocbot-web/1.0; +http://help.coccoc.com/searchengine)
	Mozilla/5.0 (compatible; DotBot/1.1; http://www.opensiteexplorer.org/dotbot, help@moz.com)
	Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)
	Mozilla/5.0 (compatible; MJ12bot/v1.4.8; http://mj12bot.com/)
	Mozilla/5.0 (compatible; SemrushBot/3~bl; +http://www.semrush.com/bot.html)
	Mozilla/5.0 (compatible; Uptimebot/1.0; +http://www.uptime.com/uptimebot)
	Mozilla/5.0 (compatible; YandexBot/3.0; +http://yandex.com/bots)
	Mozilla/5.0 (iPhone; CPU iPhone OS 7_0 like Mac OS X) AppleWebKit/537.51.1 (KHTML, like Gecko) Version/7.0 Mobile/11A465 Safari/9537.53 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)
	Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.96 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)
	Mozilla/5.0 (Linux; Android 7.1.2; CUBOT NOTE S) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.90 Mobile Safari/537.36
	Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/600.2.5 (KHTML, like Gecko) Version/8.0.2 Safari/600.2.5 (Applebot/0.1; +http://www.apple.com/go/applebot)
	Twitterbot/1.0
 */
	public function log_all_bots() {

		$client_ip = getRemoteIPAddress();
		$numeric_ip = ip2long($client_ip);

		if ($client_ip == '127.0.0.1') {
			return;
		}

		$request_url = $_SERVER['REQUEST_URI'];
		$user_agent = $_SERVER['HTTP_USER_AGENT'];

		if (strpos($user_agent, 'bot') !== false) {
			$this->conn->query("INSERT INTO log_bots (client_ip, numeric_ip, request_url, created_at, user_agent) values (?,?,?, NOW(), ?)",
				array($client_ip, $numeric_ip, $request_url, $user_agent));
		}

		//$this->conn->query("INSERT INTO log(created_at, event_type, description) VALUES (NOW(), ?, ?)", array($event, $description));

	}


}
