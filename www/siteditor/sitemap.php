<?

	global $conn;


					
	if ($action == 'generatesitemap')
	{
		$handle = fopen ("./../sitemap.xml", "w");
 
		$sSQL = "SELECT stringid FROM games WHERE hidden='N'";
		$games = $conn->getCol($sSQL);

 
  
        fwrite($handle, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"); 
        fwrite($handle, "<urlset xmlns=\"http://www.google.com/schemas/sitemap/0.84\">\n"); 

		foreach($games as $game)
		{

        	fwrite($handle, "<url>\n");

        	fwrite($handle, "<loc>http://".DOMAIN."games/{$game}/</loc>\n");
 
        	fwrite($handle, "</url>\n"); 
			
		}
        fwrite($handle, "</urlset>\n"); 

		fclose($handle);
	}



?>


 <form action="./?report=sitemap" method="POST">
 <input type="hidden" name="action" value="generatesitemap">
    <table>
    <tr>
        <td>Generate Google Sitemap:<td><input type="submit" name="submit" value="           Go!          ">
    </table>
 </form>
