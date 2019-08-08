<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
function smarty_function_paginator($params, &$smarty)
{
		$elements_total = 10;
		$elements_per_page = 10;
		$current_page = 1;
		$url = "/members/%d/";
		
		//print_r($params);
		if ($params['elements_total']) { $elements_total = intval($params['elements_total']); }
		if ($params['elements_per_page']) { $elements_per_page = intval($params['elements_per_page']); }
		if ($params['current_page']) { $current_page = intval($params['current_page']); }
		if ($params['url']) { $url = $params['url']; }
		

		$num_of_pages = ceil($elements_total/$elements_per_page);

		if ($num_of_pages == 1) {
			return '';
		}
		
		if ($current_page <= 0 || $current_page > $num_of_pages) {
			// TODO: Сюда не должны попадать, проверять надо в контроллере
			//$smarty->trigger_error("paginator: page range error");
			//return;
		}


		$lag = 8;
							//              | - currpage
		$data = array(); 	// 11100000000011100000111
		for ($i = 1; $i <= $num_of_pages; $i++) {
			$data[$i] = 0;
			//if ($i == 1 || $i == 2 || $i == 3 || $i == $num_of_pages || $i == $num_of_pages-1 || $i == $num_of_pages-2 || $i == $current_page-1 || $i == $current_page || $i == $current_page+1 ) {
			if (($i >= 1 && $i <= $lag) || ($i >= $num_of_pages-$lag + 1 && $i <= $num_of_pages) || ( abs($i - $current_page) < $lag / 2  )) {
				$data[$i] = 1;
			}
		}

		
		$_ret = '';
		$_ret .= '<div class="pagination-links" id="member-dir-pag">';
		
		if ($current_page != 1) {
			if ($current_page != 2) {
				$_ret .= '<a class="prev page-numbers" href="' . sprintf($url, $current_page - 1) . '">&larr;</a>';
			} else {
				// грязный хак с str_replace
				$_ret .= '<a class="prev page-numbers" href="' . str_replace("/%d/", "/", $url) . '">&larr;</a>';
			}
		}
		
		for ($i = 1; $i <= $num_of_pages; $i++) {
			if ($data[$i] == 1) {
				if ($i == $current_page) {
					$_ret .= '<span class="page-numbers current">'.$i.'</span>';
				} else {
					// сократим ссылку на страницу 1
					if ($i == 1) {
						// грязный хак с str_replace
						$_ret .= '<a class="page-numbers" href="' . str_replace("/%d/", "/", $url) . '">' . $i . '</a>';
					} else {
						$_ret .= '<a class="page-numbers" href="' . sprintf($url, $i) . '">' . $i . '</a>';
					}
				}
			} else {
				if ( ($i != 1 && $data[$i] == 0 && $data[$i-1] == 1)  || ($i != 1 && $data[$i] == 1 && $data[$i-1] == 0) )
				$_ret .= '...';
			}
		}
		//$_ret .= '<span class="page-numbers current">'.$i.'</span>';
		//$_ret .= '<a class="page-numbers" href="/members/?upage=2">2</a>';
		if ($current_page != $num_of_pages) {
			$_ret .= '<a class="next page-numbers" href="'.sprintf($url, $current_page+1).'">&rarr;</a>';
		}
		
		$_ret .= '</div>';
		
		return $_ret;

}

