<?
class Routing {
	
	//const PATTERN_MATCH = 1;
	//const PATTERN_INTEGER = 2;
	//const PATTERN_GAMETAG = 3;
	
	// yep, route storage array
	var $route_storage = array();

	//var $defined_vars = array('controller', 'action', 'layout', 'auth');
	var $defined_vars = array('controller', 'action', 'layout');

	// checkers
	function checker_is_integer($pattern) {
		return is_numeric($pattern);
	}

	function checker_is_anything($pattern) {
		return true;
	}


	function checker_is_game_tag($pattern) {
		// TODO: Add gametag check
		//echo '-'.$pattern.'-';
		//print_r($pattern);
		if (is_numeric($pattern)) {
			return false;
		}



		return true;
	}

	function checker_is_string($pattern) {
		return true;
	}


	// controller name generator
	private function get_controller_name($route_name) {
		if (empty($route_name)) {
			throw new Exception('GF Exception::Route name empty.');
		}
		return ucfirst($route_name).'Controller';
	}


	private function get_setter_name($variable_name) {
		if (empty($variable_name)) {
			throw new Exception('GF Exception::Setter name empty.');
		}
		return 'set_'.$variable_name;
	}


	// adds route
	function add_route($route_data) {
		
		$request = trim($route_data['route'], '/');
		$request_array = explode('/', $request);
		
		$class_name = $this->get_controller_name($route_data['controller']);
		if (!(class_exists($class_name) && method_exists($class_name, $route_data['action']))) {

			/*
			 * $route_data
			 * Array
			(
                [route] => /featured-games/:page/
                [controller] => digest
                [action] => featured_games
                [page] => integer
				)
			 */
			throw new Exception("GF Exception::Controller ($class_name) or action ({$route_data['action']}) not exists.");
		}
		
		$result = array();
		foreach($request_array as $route_part) {
			$re = array();
			if ($route_part[0] == ':') {
				$route_part = substr($route_part, 1);
				
				$res['pattern'] = $route_part;
				if (!method_exists($this, 'checker_is_'.$route_data[$route_part])) {
					throw new Exception('GF Exception::Checker not exists: '.$route_part.' for route '.$route_data['route']);
				}
				$res['check'] = $route_data[$route_part];
				
			} else {
				$res['pattern'] = $route_part;
				$res['check'] = 'string_match';
			}
			array_push($result, $res);
		}
		
		foreach ($this->defined_vars as $var) {
			$result[$var] = $route_data[$var];
		}
		
		array_push($this->route_storage, $result);
			
	}
	
	
	function parse_route($url) {
		$request = trim($url, '/');
		$request_array = explode('/', $request);
		//print_r($this->route_storage);
		foreach ($this->route_storage as $variant) {
			
			if (count($request_array) != count($variant) - count($this->defined_vars)) {
				continue;
			}

			//print_r($variant);
			
			$exported_variables = array(); // user defined variables e.g. Array( [game_slug] => 16-tiles-mahjong )
			
			$is_route_valid = true;
			for($i=0; $i<count($request_array);$i++) {
				
				 if ($variant[$i]['check'] == 'string_match') {
						if (strcmp($variant[$i]['pattern'], $request_array[$i]) != 0) {
							$is_route_valid = false;
							//echo $variant[$i]['pattern'].' - '.$request_array[$i]."\n";
						} 
				 	
				} else {
					//print_r($request_array[$i]);
					//if (!call_user_func('self::checker_is_'.$variant[$i]['check'], $request_array[$i] )) { // php 5.3
					if (!call_user_func(array('Routing', 'self::checker_is_'.$variant[$i]['check']), $request_array[$i] )) {
						//print_r ($variant[$i]);
						$is_route_valid = false;
					} else {
						// adding this key-value as possible variables
						$exported_variables[$variant[$i]['pattern']] = $request_array[$i];
						//print_r ($variant[$i]);
					}
				}
				 
			}
			
			//echo $is_route_valid;
			//print_r($exported_variables);
			
			if ($is_route_valid) {
				//print_r($variant);
				//echo $variant['controller'].' - '.$variant['action']."\n";
				
				$controller_class = $this->get_controller_name($variant['controller']);
				$action = $variant['action'];
				$controller = new $controller_class();

				//print_r($exported_variables);
				//var_dump($controller);
				
				// check for setters, define variables
				foreach ($exported_variables as $var=>$value) {
					$setter_name = $this->get_setter_name($var);
					//print_r(get_class_methods($controller));
					if (!method_exists($controller_class, $setter_name)) {
						throw new Exception("GF Exception:: $setter_name setter not exists.");
					} else {
						$controller->$setter_name($value);
					}

				}
				
				
				foreach ($this->defined_vars as $var) {
					$controller->$var = $variant[$var];
				}
				
				
				$controller->$action();
				
				exit;
			} //$is_route_valid
			
		}
		
		
	}
	
}

