<?


function parseurl($str) {
	
// Oleg Andreev (30.06.2003)

// »щем все http://-строки и обводим урлы маркерами (<;;;;>)
$str=preg_replace("{(\W)(http://.*?)([\s\)\(;<>])}si","\\1<;;;;>\\2</;;;;>\\3",$str);

// »щем все www.-строки, без http:// и обводим урлы маркерами (<;;;>)
$str=preg_replace("{(\W)((?<!http://)www\..*?)([\s\)\(\"'\;])}si","\\1<;;;>\\2</;;;>\\3",$str);

// ”дал€ем маркеры внутри уже существующих ссылок
$str=preg_replace("{<a\s.*?</a}sie","preg_replace('{</?;{3,4}>}','',stripslashes('\\0'))",$str);

// «амен€ем маркеры на ссылки
$str=preg_replace("#<;;;;>(.*?)</;;;;>#si",'<a href="\\1">\\1</a>',$str);
$str=preg_replace("#<;;;>(.*?)</;;;>#si",'<a href="http://\\1">\\1</a>',$str);

return $str;
}









function unQuotePlain($str)
{
	$str = preg_replace("/&..quo;/", '"', $str);
	$str=strtr ($str, array("&#8470;"=>'є',"\r"=>'','&mdash;'=> '-','&#132;'=>'"','&#147;'=>'"','&#148;'=>'"','&#151;'=> '-','&hellip;'=> '...','&nbsp;'=> ' '));
	$str=preg_replace("%<br>%si","\n",$str);
	/*
	$str=preg_replace("%<br>%si",'<###>',$str);
	$str=preg_replace('%(<\?.*?\?'.'>)%sie',"str_replace('<###>','<br>','\\1')",$str);
	$str=str_replace('<###>',"\n",$str);
	*/
	$str=stripslashes($str);
//	$str=preg_replace("/\&(\w+);/","&amp;\\1;",$str);
	return $str;
}


function unQuote ($str)
{
	// prevent < ?   ? > & <code></code> transforming
	// needs normal using of < ?   ? >

	if(stristr($str,'<?')){
		$parts=explode("<?",$str);
		$parts[0]=unQuotePlain($parts[0]);
		for($i=1;$i<sizeof($parts);$i++){
			$p2=explode('?'.'>',$parts[$i]);
			$p2[0]=preg_replace("%<br>%si","&lt;br&gt;",$p2[0]);
			$p2[1]=unQuotePlain($p2[1]);
			$parts[$i]=implode('?'.'>',$p2);
		}
		$str=implode('<?',$parts);
	}else{
		$str=unQuotePlain($str);
	}
	$str=preg_replace("/\&(\w+);/","&amp;\\1;",$str);
//	$str=str_replace("<","&lt;",$str);
//	$str=str_replace(">","&gt;",$str);
	return $str;
}












function formatting($str,$spectags=0)
{

// format quotes from Word
$str = str_replace ('Ђ','"', $str);
$str = str_replace ('ї','"', $str);

$str=preg_replace ( "#<(.*?)>#esi", "'<'.str_replace ('\\\"', '<^>','\\1').'>'", $str);
$str=preg_replace ( "#<tt>(.*?)</tt>#esi", "'<tt>'.str_replace ('\\\"', '<^>','\\1').'</tt>'", $str);
$str=preg_replace ( "#<code([^>]*?)>(.*?)</code>#esi", "'<code\\1>'.str_replace ('\\\"', '<^>','\\2').'</code>'", $str);
$str=preg_replace ( "#(<script[^>]*>)(.*?)</script>#esi", "'\\1'.str_replace ('\\\"', '<^>','\\2').'</scr'.'ipt>'", $str);
$str=preg_replace ( "#<\?(.*?)\?".">#esi", "'<?'.str_replace ('\\\"', '<^>','\\1').'?'.'>'", $str);

$str=preg_replace ( "#(\W)\"(.*?)([^\s])\"#si", "\\1Ђ\\2\\3ї", $str);
if(preg_match("#Ђ.*?(\"[^ї]*?)*?ї#si",$str)){
	$str=preg_replace ( "#(\W)\"(.*?)([^\s])\"#si", "\\1Ђ\\2\\3ї", $str);
	while (preg_match ("#Ђ[^ї]*?Ђ#", $str))
//		$str=preg_replace ( "#(Ђ[^ї]*)Ђ([^ї]*)ї#", "\\1&#132;\\2&#147;", $str);
		$str=preg_replace ( "#(<[^>]*)<([^>]*)>#", "\\1&#147;\\2&#148;", $str);
}

$str = str_replace ('Ђ','&laquo;', $str);
$str = str_replace ('ї','&raquo;', $str);
//$str = str_replace ('<','&#147;', $str);
//$str = str_replace ('>','&#148;', $str);

$str = str_replace ('є','&#8470;', $str);
$str = str_replace ('Е','&hellip;', $str);
$str = str_replace ('Щ','&trade;', $str);
$str = str_replace ('Ѓ','&reg;', $str);
$str = str_replace ('©','&copy;', $str);
$str = str_replace ('...','&hellip;', $str);
if(!$spectags)$str = str_replace (' - ','&nbsp;&#151; ', $str);
$str = str_replace ('(c)','&copy;', $str);
$str = str_replace ('(r)','&reg; ', $str);
$str = str_replace ('(tm)','&trade; ', $str);
$str = str_replace ('[tm]','&trade; ', $str);

$str = str_replace ('<^>','"', $str);

return $str;
}





function parseTags($str,$pe=0,$spectags=0){

$str=str_replace('&','&amp;',$str);
$str=str_replace('<','&lt;',$str); // commented by reggie
$str=str_replace('>','&gt;',$str); // commented by reggie

if($spectags)
	$str=preg_replace ( "/(&lt;\?.*?\?&gt;)/es", "str_replace ('&lt;', '<',str_replace ('&gt;', '>','\\1'))", $str);

$str=preg_replace ( "/&lt;script(?<!&gt;)&gt;(.*?)&lt;\/script&gt;/es", "str_replace ('&lt;', '<',str_replace ('&gt;', '>','\\1'))", $str);
if($pe){
	$str=preg_replace ( "/&lt;script(?<!&gt;)&gt;(.*?)&lt;\/script&gt;/es", "str_replace ('&amp;', '&','\\1')", $str);
}else{
	$str=str_replace('&amp;','&',$str);
}
return $str;
}






function parseBrs($str,$spectags=0){

$str=str_replace("\n",'<xxbr>',$str);
$str=str_replace("\r",'',$str);

$str=preg_replace ( "/(<script[^<]+<\/script>)/es", "str_replace ('<xxbr>', \"\\n\",'\\0')", $str);
if($spectags){
	$str=preg_replace ( "/(<\?.*?\?".">)/es", "str_replace ('<xxbr>', \"\\n\",'\\0')", $str);
	$str=stripslashes($str);
}
return str_replace("<xxbr>",'<br>',$str);

}













$phpkeywords=array('\$this','__sleep','__wakeup','and','array','as','bool','break','case','cfunction','char','class','continue','declare','default','do','double','else','elseif','enddeclare','endfor','endforeach','endif','endswitch','endwhile','eval','extends','FALSE','float','for','foreach','function','global','if','int','integer','long','mixed','new','not','NULL','object','old_function','or','parent','php','PHP_OS','PHP_VERSION','real','return','static','static','stdClass','string','switch','TRUE','var','void','while','xor','arsort','asort','compact','count','current','each','end','extract','in_array','array_search','key','krsort','ksort','list','natsort','natcasesort','next','pos','prev','range','reset','rsort','shuffle','sizeof','sort','uasort','uksort','usort','call_user_method_array','call_user_method','class_exists','get_class','get_class_methods','get_class_vars','get_declared_classes','get_object_vars','get_parent_class','is_subclass_of','method_exists','checkdate','date','getdate','gettimeofday','gmdate','gmmktime','gmstrftime','localtime','microtime','mktime','strftime','time','strtotime','chroot','chdir','dir','closedir','getcwd','opendir','readdir','rewinddir','error_log','error_reporting','restore_error_handler','set_error_handler','trigger_error','user_error','basename','chgrp','chmod','chown','clearstatcache','copy','delete','dirname','disk_free_space','diskfreespace','disk_total_space','fclose','feof','fflush','fgetc','fgetcsv','fgets','fgetss','file','file_exists','fileatime','filectime','filegroup','fileinode','filemtime','fileowner','fileperms','filesize','filetype','flock','fopen','fpassthru','fputs','fread','fscanf','fseek','fstat','ftell','ftruncate','fwrite','is_dir','is_executable','is_file','is_link','is_readable','is_writable','is_writeable','is_uploaded_file','link','linkinfo','mkdir','move_uploaded_file','parse_ini_file','pathinfo','pclose','popen','readfile','readlink','rename','rewind','rmdir','stat','lstat','realpath','symlink','tempnam','tmpfile','touch','umask','unlink','call_user_func_array','call_user_func','create_function','func_get_arg','func_get_args','func_num_args','function_exists','get_defined_functions','register_shutdown_function','register_tick_function','unregister_tick_function','header','headers_sent','setcookie','GetImageSize','mail','abs','acos','acosh','asin','asinh','atan','atanh','atan2','base_convert','bindec','ceil','cos','cosh','decbin','dechex','decoct','deg2rad','exp','floor','getrandmax','hexdec','lcg_value','hypot','lcg_value','log','log10','log1p','max','min','mt_rand','mt_srand','mt_getrandmax','number_format','octdec','pi','pow','rad2deg','rand','round','sin','sinh','sqrt','srand','tan','tanh','connection_aborted','connection_status','connection_timeout','constant','define','defined','die','eval','exit','get_browser','highlight_file','highlight_string','ignore_user_abort','iptcparse','leak','pack','show_source','sleep','uniqid','unpack','usleep','checkdnsrr','closelog','debugger_off','debugger_on','define_syslog_variables','fsockopen','gethostbyaddr','gethostbyname','gethostbynamel','getmxrr','getprotobyname','getprotobynumber','getservbyname','getservbyport','ip2long','long2ip','openlog','pfsockopen','socket_get_status','socket_set_blocking','socket_set_timeout','syslog','assert','assert_options','extension_loaded','dl','getenv','get_cfg_var','get_current_user','get_defined_constants','get_extension_funcs','getmygid','get_included_files','get_loaded_extensions','get_magic_quotes_gpc','get_magic_quotes_runtime','getlastmod','getmyinode','getmypid','getmyuid','get_required_files','getrusage','ini_alter','ini_get','ini_get_all','ini_restore','ini_set','phpcredits','phpinfo','phpversion','php_logo_guid','php_sapi_name','php_uname','putenv','set_magic_quotes_runtime','set_time_limit','version_compare','zend_logo_guid','zend_version','escapeshellarg','escapeshellcmd','exec','passthru','system','shell_exec','preg_match','preg_match_all','preg_replace','preg_replace_callback','preg_split','preg_quote','preg_grep','ereg','ereg_replace','eregi','eregi_replace','split','spliti','sql_regcase','addcslashes','addslashes','bin2hex','chop','chr','chunk_split','convert_cyr_string','count_chars','crc32','crypt','echo','explode','get_html_translation_table','get_meta_tags','hebrev','hebrevc','htmlentities','htmlspecialchars','implode','join','levenshtein','localeconv','ltrim','md5','md5_file','metaphone','nl2br','ord','parse_str','print','printf','rtrim','sscanf','setlocale','sprintf','strncasecmp','strcasecmp','strchr','strcmp','strcoll','strcspn','strip_tags','stripcslashes','stripslashes','stristr','strlen','strnatcmp','strnatcasecmp','strncmp','str_pad','strpos','strrchr','str_repeat','strrev','strrpos','strspn','strstr','strtok','strtolower','strtoupper','str_replace','strtr','substr','substr_count','substr_replace','trim','urldecode','urlencode','doubleval','empty','floatval','gettype','get_defined_vars','get_resource_type','import_request_variables','intval','is_array','is_bool','is_double','is_float','is_int','is_integer','is_long','is_null','is_numeric','is_object','is_real','is_resource','is_scalar','is_string','isset','print_r','serialize','settype','strval','unserialize','unset','var_dump','var_export','__FILE__','__LINE__','PHP_VERSION','PHP_OS','TRUE','FALSE','NULL','E_ERROR','E_WARNING','E_PARSE','E_NOTICE','E_ALL');



$askeywords=array('_root','_parent','break','case','continue','default','delete','do','else','false','for','function','if','in','instanceof','int','new','null','return','super','switch','this','true','typeof','var','void','while','with','Array','Button','Date','Math','Number','String','Color','MovieClip','Object','array','button','date','math','string','color','movieclip','object');



$jskeywords=array('abstract','boolean','break','byte','case','catch','char','class','const','continue','default','delete','do','double','else','extends','false','final','finally','float','for','function','goto','if','implements','import','in','instanceof','int','interface','long','native','new','null','package','private','protected','public','return','short','static','super','switch','synchronized','this','throw','throws','transient','true','try','typeof','var','void','while','with','Anchor','anchors','Applet','applets','Area','Array','Button','Checkbox','Date','document','FileUpload','Form','forms','Frame','frames','Hidden','history','Image','images','Link','links','Area','location','Math','MimeType','mimeTypes','navigator','options','Password','Plugin','plugins','Radio','Reset','Select','String','Submit','Text','Textarea','window');



$perlkeywords=array('continue','do','else','elsif','for','foreach','goto','if','last','local','lock','map','my','next','package','redo','require','return','sub','unless','until','use','while','STDIN','STDOUT','STDERR','ARGV','ARGVOUT','ENV','INC','SIG','TRUE','FALSE','__FILE__','__LINE__','__PACKAGE__','__END__','__DATA__','lt','gt','le','ge','eq','ne','cmp','x','not','and','or','xor','q','qq','qx','qw','$','@','%','abs','accept','alarm','atan2','bind','binmode','bless','caller','chdir','chmod','chomp','chop','chown','chr','chroot','close','closedir','connect','cos','crypt','dbmclose','dbmopen','defined','delete','die','dump','each','eof','eval','exec','exists','exit','exp','fcntl','fileno','flock','fork','format','formline','getc','getlogin','getpeername','getpgrp','getppid','getpriority','getpwnam','getgrnam','gethostbyname','getnetbyname','getprotobyname','getpwuid','getgrgid','getservbyname','gethostbyaddr','getnetbyaddr','getprotobynumber','getservbyport','getpwent','getgrent','gethostent','getnetent','getprotoent','getservent','setpwent','setgrent','sethostent','setnetent','setprotoent','setservent','endpwent','endgrent','endhostent','endnetent','endprotoent','endservent','getsockname','getsockopt','glob','gmtime','grep','hex','import','index','int','ioctl','join','keys','kill','lc','lcfirst','length','link','listen','localtime','log','lstat','mkdir','msgctl','msgget','msgsnd','msgrcv','no','oct','open','opendir','ord','pack','pipe','pop','pos','print','printf','prototype','push','quotemeta','rand','read','readdir','readlink','recv','ref','rename','reset','reverse','rewinddir','rindex','rmdir','scalar','seek','seekdir','select','semctl','semget','semop','send','setpgrp','setpriority','setsockopt','shift','shmctl','shmget','shmread','shmwrite','shutdown','sin','sleep','socket','socketpair','sort','splice','split','sprintf','sqrt','srand','stat','study','substr','symlink','syscall','sysopen','sysread','sysseek','system','syswrite','tell','telldir','tie','tied','time','times','truncate','uc','ucfirst','umask','undef','unlink','unpack','untie','unshift','utime','values','vec','wait','waitpid','wantarray','warn','write');



$cppkeywords=array('auto','bool','break','case','catch','char','cerr','cin','class','const','continue','cout','default','delete','do','double','else','enum','explicit','extern','float','for','friend','goto','if','inline','int','long','namespace','new','operator','private','protected','public','register','return','short','signed','sizeof','static','struct','switch','template','this','throw','try','typedef','union','unsigned','virtual','void','volatile','while','__asm','__fastcall','__based','__cdecl','__pascal','__inline','__multiple_inheritance','__single_inheritance','__virtual_inheritance','define','error','include','elif','if','line','else','ifdef','pragma','endif','ifndef','undef','if','else','endif');







function colorize($str,$lang,$tags=array('<font color=#222299>','</font>','<font color=#888888>','</font>','<font color=#007700>','</font>')){
	if($lang!='php' && $lang!='as' && $lang!='js' && $lang!='perl' && $lang!='cpp')return $str;
	$ks=$lang.'keywords';
	global $$ks;
	$ks=$$ks;
	if($lang=='php'){
		$str=str_replace('<?','<^^^><b><?</b></^^^>',$str);
		$str=str_replace('?'.'>','<^^^><b>?'.'></b></^^^>',$str);
		$str=str_replace('&lt;?','<^^^><b>&lt;?</b></^^^>',$str);
		$str=str_replace('?&gt;','<^^^><b>?&gt;</b></^^^>',$str);
		$str=preg_replace("%\\$(?=\w)%si","<^^^>$</^^^>",$str);
	}

		$str=stripslashes($str);
		foreach($ks as $word){
			$str=preg_replace("%(\W)".$word."(\W)%si","\\1<^^^>".$word."</^^^>\\2",$str);
		}
		// strings
		$str=preg_replace('%(?<!\\\)""%si',"<^^_^>",$str); 
		$str=preg_replace("%(?<!\\\)''%si","<^_^>",$str);
		$str=preg_replace('%((?<!\\\)".*?[^\\\]")%si',"<^^>\\1</^^>",$str);
		$str=preg_replace("%((?<!\\\)'.*?[^\\\]')%si","<^>\\1</^>",$str);
		
		// comments
		$str=preg_replace("%(#.*?(\n|\r|<br>))%si","<^^^^>\\1</^^^^>",$str);
		$str=preg_replace("%(//.*?(\n|\r|<br>))%si","<^^^^>\\1</^^^^>",$str);
		$str=preg_replace("%(/\*.*?\*/)%si","<^^^^^>\\1</^^^^^>",$str);
		
		// strings retransform
		$str=str_replace("<^^_^>",'<^^>""</^^>',$str);
		$str=str_replace("<^_^>","<^>''</^>",$str);

		// delete <^+> from: 
		//   "
		$str=(preg_replace("{<^^>(.*?)</^^>}sie","'<^^>'.preg_replace('{</?^+>}si','',stripslashes('\\1')).'</^^>'",$str));
		//   '
		$str=(preg_replace("{<^>(.*?)</^>}sie","'<^>'.preg_replace('{</?^+>}si','',stripslashes('\\1')).'</^>'",$str));

		// delete <^+> from comments
		$str=(preg_replace("{<^^^^^>(.*?)</^^^^^>}sie","'<^^^^^>'.preg_replace('{</?^+>}si','',stripslashes('\\1')).'</^^^^^>'",$str));
		$str=(preg_replace("{<^^^^>(.*?)</^^^^>}sie","'<^^^^>'.preg_replace('{</?^+>}si','',stripslashes('\\1')).'</^^^^>'",$str));
		
		//	print str_replace('<br>',"<br>\n",$str)."<br>";
		
		$str=str_replace("<^>",$tags[2],$str);
		$str=str_replace("</^>",$tags[3],$str);
		$str=str_replace("<^^>",$tags[2],$str);
		$str=str_replace("</^^>",$tags[3],$str);
		$str=str_replace("<^^^>",$tags[0],$str);
		$str=str_replace("</^^^>",$tags[1],$str);
		$str=str_replace("<^^^^>",$tags[4],$str);
		$str=str_replace("</^^^^>",$tags[5],$str);
		$str=str_replace("<^^^^^>",$tags[4],$str);
		$str=str_replace("</^^^^^>",$tags[5],$str);


		return $str;
}

?>