<?php

class MySQLconnector
{
    var $MYSQLhostname='127.0.0.1'; //const
    var $MYSQLusername='g4w2'; 
    var $MYSQLpassword=''; 

    var $MYSQLbasename;
    var $mysql_link;
    var $mysql_data;

    function MySQLconnector()
    {
        $this->MYSQLbasename='g4w2';
        $this->mysql_link=mysql_connect($this->MYSQLhostname, 
                                         $this->MYSQLusername, 
                                         $this->MYSQLpassword) 
                                        or die("Could not connect. Database is not available");

        if (!mysql_select_db($this->MYSQLbasename)) 
            die("Could not select database");

        return 1;
    }

    function BaseSelect($query)
    {
        $this->mysql_data = mysql_query($query)
            or die("Invalid query: " .$query.' '. mysql_error());
			

    }

    function BaseFetch()
    {
        if(mysql_num_rows($this->mysql_data)>0)
        {
            return mysql_fetch_array($this->mysql_data);
        }
    }
    
    function BaseFinish()
    {
        mysql_free_result($this->mysql_data);
    }

    function BaseDisconnect()
    {
        mysql_close($this->mysql_link);
    }

//    function BaseDo($q)
//    {
 //       this->BaseSelect($q);
        //BaseFinish();
        //global $mysql_data;
        //mysql_unbuffered_query($query);
        //    or die("Invalid query: " . mysql_error());
//    }

    function BaseRows()
    {
        return (mysql_num_rows($this->mysql_data));
    }

}
?>