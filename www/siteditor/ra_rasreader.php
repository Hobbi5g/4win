<?php
/***********************************************************************************************/
/*                                                                                             */
/*  RA_RASReader.php                                                                           */
/*                                                                                             */
/*  Property of Reflexive Entertainment, Inc.                                                  */
/*  Copyright 2004                                                                             */
/*                                                                                             */
/*  This file reads, parses and caches Reflexive Arcade Syndication feeds                      */
/*                                                                                             */
/*  For more information see the README.txt file from the Site Development Kit                 */
/*                                                                                             */
/***********************************************************************************************/

  function enum() {
    $ArgC = func_num_args();
    $ArgV = func_get_args();

    for($Int = 0; $Int < $ArgC; $Int++) define($ArgV[$Int], $Int);
  }

  enum("ParseNoContext", "ParseGameList", "ParseGameData", "ParseDataElement", "ParseIndexList", "ParseIndexElement", "ParseCategoryList", "ParseCategoryElement", "ParseLinks");

  enum("RASParseError", "RASParseWarning");

  class RA_RASReader
  {
    var $GameList;
    var $Index;
    var $LinkList;
    var $CategoryByGame;
    var $CategorySize;

    var $LotteryIndex;

    var $ParseState;
    var $ParseGame;
    var $ParseData;
    var $ParseIndex;
    var $ParseCategory;
    var $ParseLink;

    var $FeedTime;

    function ElementStart($Parser, $Name, $Attrib)
    {
        switch ($this->ParseState)
        {
            case ParseNoContext:
                if ($Name == "GameList")
                {
                    $this->ParseState = ParseGameList;
                }
                elseif ($Name == "Index")
                {
                    $this->ParseState = ParseIndexList;
                }
                elseif ($Name == "Category")
                {
                    $this->ParseState = ParseCategoryList;
                }
                elseif ($Name == "Links")
                {
                    $this->ParseState = ParseLinks;
                }
                break;
            case ParseGameList:
                if ($Name == "GameData")
                {
                    $this->ParseGame = $Attrib["GameID"];
                    $this->ParseState = ParseGameData;
                    $this->CategorySize['All']++;
                }
                break;
            case ParseGameData:
                $this->ParseData = $Name;
                $this->ParseState = ParseDataElement;
                break;
            case ParseDataElement:
                $this->ReadError("Unexpected element ".$Name." in game data", RASParseError);
                break;
            case ParseIndexList:
                $this->ParseIndex = $Name;
                $this->ParseState = ParseIndexElement;
                break;
            case ParseIndexElement:
                if ($Name == "Game")
                {
                    $Rank = $Attrib["Rank"];
                    unset($Attrib["Rank"]);
                    if ($Rank == null || $Attrib["ID"] == null)
                    {
                        $this->ReadError("Missing Rank and/or ID tags on Index ".$this->ParseIndex, RASParseError);
                    }
                    else
                    {
                        $this->Index[$this->ParseIndex][$Rank] = $Attrib;
                    }
                }
                else
                {
                    $this->ReadError("Unexpected element ".$Name." in index ".$this->ParseIndex, RASParseWarning);
                }
                break;
            case ParseCategoryList:
                $this->ParseCategory = $Name;
                $this->ParseState = ParseCategoryElement;
                break;
            case ParseCategoryElement:
                if ($Name == "Game")
                {
                    $ID = $Attrib["ID"];
                    if ($ID == null)
                    {
                        $this->ReadError("Missing ID tag on Category ".$this->ParseCategory, RASParseError);
                    }
                    else
                    {
                        if ($this->CategoryByGame[$ID] == NULL)
                        {
                            $this->CategoryByGame[$ID] = array($this->ParseCategory);
                        }
                        else
                        {
                            array_push($this->CategoryByGame[$ID], $this->ParseCategory);
                        }
                        $this->CategorySize[$this->ParseCategory]++;
                    }
                }
                else
                {
                    $this->ReadError("Unexpected element ".$Name." in index ".$this->ParseIndex, RASParseWarning);
                }
                break;
            case ParseLinks:
                $this->ParseLink = $Name;
                break;
        }
    }

    function ElementEnd($Parser, $Name)
    {
        switch ($this->ParseState)
        {
            case ParseGameList:
                if ($Name == "GameList")
                {
                    $this->ParseState = ParseNoContext;
                }
                break;
            case ParseGameData:
                if ($Name == "GameData")
                {
                    $this->ParseState = ParseGameList;
                }
                break;
            case ParseDataElement:
                if ($Name == $this->ParseData)
                {
                    $this->ParseState = ParseGameData;
                }
                break;
            case ParseIndexList:
                if ($Name == "Index")
                {
                    $this->ParseState = ParseNoContext;
                }
                break;
            case ParseIndexElement:
                if ($Name == $this->ParseIndex)
                {
                    $this->ParseState = ParseIndexList;
                }
                break;
            case ParseCategoryList:
                if ($Name == "Category")
                {
                    $this->ParseState = ParseNoContext;
                }
                break;
            case ParseCategoryElement:
                if ($Name == $this->ParseCategory)
                {
                    $this->ParseState = ParseCategoryList;
                }
                break;
            case ParseLinks:
                if ($Name == $this->ParseLink)
                {
                    $this->ParseLink = NULL;
                }
                elseif ($Name == "Links")
                {
                    $this->ParseState = ParseNoContext;
                }
                else
                {
                    $this->ReadError("Error Parsing Link Element ".$Name, RASParseError);
                }
                break;
        }
    }

    function CharacterData($Parser, $Data)
    {
        switch ($this->ParseState)
        {
            case ParseDataElement:
                if ($this->GameList[$this->ParseGame][$this->ParseData] != NULL)
                {
                    if ($Data == "\n")
                    {
                        $this->GameList[$this->ParseGame][$this->ParseData] = $this->GameList[$this->ParseGame][$this->ParseData]."<br>";
                    }
                    else
                    {
                        $this->GameList[$this->ParseGame][$this->ParseData] = $this->GameList[$this->ParseGame][$this->ParseData].utf8_decode($Data);
                    }
                }
                else
                {
                    $this->GameList[$this->ParseGame][$this->ParseData] = utf8_decode($Data);
                }
                break;
            case ParseLinks:
                if ($this->ParseLink != NULL)
                {
                    $this->LinkList[$this->ParseLink] = $this->LinkList[$this->ParseLink].$Data;
                    break;
                }
            default:
                $this->ReadError("Unexpected character data ".$Data." in state ".$this->ParseState);
                break;
        }
    }

    function ReadError($ErrorMsg, $ErrorLevel)
    {
        if ($ErrorLevel == RASParseError)
        {
            die($ErrorMsg);
        }
    }

    function PostProcessFeed()
    {
        $this->FeedTime = date('Y-m-d H:i:s');

        foreach(array_keys($this->GameList) as $GameKey)
        {
            $CatString = "";
            $Underdog = "";
            $UnderdogCount = 0;
            foreach($this->CategoryByGame[$GameKey] as $Category)
            {
                if ($Underdog == "")
                {
                    $CatString = $Category;
                    $Underdog = $Category;
                    $UnderdogCount = $this->CategorySize[$Category];
                }
                else
                {
                    $CatString = $CatString."/".$Category;
                    if ($UnderdogCount > $this->CategorySize[$Category])
                    {
                        $Underdog = $Category;
                        $UnderdogCount = $this->CategorySize[$Category];
                    }
                }
                $this->GameList[$GameKey]["GamePrimaryCategory"] = $Underdog;
                $this->GameList[$GameKey]["GameCategories"] = $CatString;
            }
        }
    }

    function RA_RASReader($RASFeedURL)
    {
        $Parser = xml_parser_create();
        xml_parser_set_option($Parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parser_set_option($Parser, XML_OPTION_CASE_FOLDING, 0);

        $this->ParseState = ParseNoContext;
        $this->GameList = array();
        $this->Index = array();
        $this->LinkList = array();
        xml_set_object($Parser, $this);
        xml_set_element_handler($Parser, "ElementStart", "ElementEnd");
        xml_set_character_data_handler($Parser, "CharacterData");

        if ($FeedStream = fopen($RASFeedURL, "r")) {
            while ($Data = fread($FeedStream, 4096)) {

                $ParsedChunk = xml_parse($Parser, $Data, feof($FeedStream));
                if (!$ParsedChunk) {
                    $ErrorCode = xml_get_error_code($Parser);
                    $ErrorText = xml_error_string($ErrorCode);
                    $ErrorLine = xml_get_current_line_number($Parser);

                    $OutputText = "Parsing problem at line $ErrorLine: $ErrorText";
                    die($OutputText);
                }
            }
            fclose($FeedStream);
        } else {
            //$this = NULL; //reggie
            return;
        }

        $this->PostProcessFeed();
    }

    function BuildLotterySort()
    {
        if (session_id() != null)
        {
            $Sort = $_SESSION["LotterySort"];
            if ($Sort != NULL)
            {
                $this->LotteryIndex = $Sort;
                return;
            }

            $Seed = $_SESSION["RandomSeed"];
            if ($Seed == NULL)
            {
                $Seed = mt_rand();
                $_SESSION["RandomSeed"] = $Seed;
            }
            mt_srand($Seed);
        }
        else
        {
            $IP = str_replace(".", "", $REMOTE_ADDR);
            $Day = date("Ymd");
            $Seed = $IP + $Day;
            mt_srand($Seed);
        }

        $TotalTickets = 0;
        $this->LotteryIndex = array();

        foreach(array_keys($this->Index["Lottery"]) as $Key)
        {
            if ($this->Index["Lottery"][$Key]["Tickets"] <= 0) $this->Index["Lottery"][$Key]["Tickets"] = 1;
            $TotalTickets += $this->Index["Lottery"][$Key]["Tickets"];
        }

        $numToSort = count($this->Index["Lottery"]);
        for($i = 0; $i < $numToSort; $i++)
        {
            $Selected = mt_rand(0, $TotalTickets - 1);
            $TicketRange = 0;
            foreach(array_keys($this->Index["Lottery"]) as $Key)
            {
                $ElementTickets = $this->Index["Lottery"][$Key]["Tickets"];

                if ($TicketRange <= $Selected && $TicketRange + $ElementTickets > $Selected)
                {
                    $this->LotteryIndex[$i] = array("ID"=>$this->Index["Lottery"][$Key]["ID"]);
                    $TotalTickets -= $ElementTickets;
                    $this->Index["Lottery"][$Key]["Tickets"] = 0;
                    break;
                }
                $TicketRange += $ElementTickets;
            }
        }

        if (session_id() != null)
        {
            $_SESSION["LotterySort"] = $this->LotteryIndex;
        }
    }

    function GetAIDForIndexBySortTypeAndCategory($SortType, $CategoryType, $Index)
    {
        if ($SortType == "Lottery")
        {
            if ($this->LotteryIndex == NULL)
            {
                $this->BuildLotterySort();
            }
            $SortList =& $this->LotteryIndex;
        }
        else
        {
            $SortList =& $this->Index[$SortType];
        }

        if ($CategoryType != NULL && $CategoryType != "All")
        {
            $CatCount = 0;
            for($SortIndex = 0; $SortIndex < count($SortList); $SortIndex++)
            {
                $CatList =& $this->CategoryByGame[$SortList[$SortIndex]["ID"]];
                if (in_array($CategoryType, $CatList))
                {
                    $CatCount++;
                    if ($CatCount == $Index)
                    {
                        return $SortList[$SortIndex]["ID"];
                    }
                }
            }
        }
        else
        {
            return $SortList[$Index - 1]["ID"];
        }
    }

    function GetAIDListForSearch($SearchString, $CategoryType)
    {
        if ($SearchString == "" || $SearchString == NULL) return array();

        $SearchString = strtr($SearchString, '"\\\'', '   ');

        $SearchWords = preg_split("/\s+/", $SearchString, -1, PREG_SPLIT_NO_EMPTY);

        // Search fields is ordered
        $SearchFields = array("GameTitle", "GameCategories", "Developer", "ShortDescription", "MediumDescription", "LongDescription");

        foreach(array_keys($this->GameList) as $GameID)
        {
            if ($CategoryType == "All" || $CategoryType == NULL || in_array($CategoryType, $this->CategoryByGame[$GameID]))
            {
                $GameData =& $this->GameList[$GameID];

                $RankSum = 0;
                $FoundAllWords = true;

                foreach($SearchWords as $SearchWord)
                {
                    $FoundWord = false;

                    foreach(array_keys($SearchFields) as $FieldKey)
                    {
                        if (stristr($GameData[$SearchFields[$FieldKey]], $SearchWord))
                        {
                            $RankSum += $FieldKey;
                            $FoundWord = true;
                            break;
                        }
                        $FindRank++;
                    }

                    if (!$FoundWord)
                    {
                        $FoundAllWords = false;
                        break;
                    }
                }

                if ($FoundAllWords)
                {
                    $Matches[$GameID] = $RankSum;
                }
            }
        }

        if (is_array($Matches))
	    {
            asort($Matches);
            return array_keys($Matches);
        }
        else
        {
            return array();
        }
    }

    function GetNumberOfGamesInCategory($CategoryType)
    {
        if ($CategoryType == NULL || $CategoryType == '')
        {
            return $this->CategorySize["All"];
        }
        else
        {
            return $this->CategorySize[$CategoryType];
        }
    }

    function GetGameData($AID, $DataName)
    {
        return $this->GameList[$AID][$DataName];
    }

    function GetGame($AID)
    {
        return $this->GameList[$AID];
    }

    function GetFormattedLink($Type, $Replacement)
    {
        $String = $this->LinkList[$Type];
        $Output = '';
        while (($BraceStart = strpos($String, '{')) !== false)
        {
            $BraceEnd = strpos($String, '}', $BraceStart);
            $Prefix = substr($String, 0, $BraceStart);
            $Tag = substr($String, $BraceStart + 1, $BraceEnd - $BraceStart - 1);
            $String = substr($String, $BraceEnd + 1);
            $Output = $Output.$Prefix.$Replacement[$Tag];
        }
        return $Output.$String;
    }
  }

  function RA_GetFeed($FeedURL, $ChannelID, $CachePath, $gameid)
  {
      if (!is_writable($CachePath))
      {
          exit("FATAL ERROR: Path for feed caching is not writable");
      }

      $CacheName = $CachePath."/channel".$ChannelID.".ras";
      $GotValidFeed = false;

      if (file_exists($CacheName))
      {
          $File = fopen($CacheName, 'r');
          $Content = fread($File, filesize($CacheName));
          fclose($File);
          $GLOBALS["GlobalReader"] = unserialize($Content);

          $EarliestValidFeedDate = date('Y-m-d H:i:s', time() - 2*60*60);
          if ($GLOBALS["GlobalReader"]->FeedTime != NULL && $GLOBALS["GlobalReader"]->FeedTime >= $EarliestValidFeedDate)
          {
                $GotValidFeed = true;
				//print_r($GLOBALS["GlobalReader"]->GetGame(9));
				return $GLOBALS["GlobalReader"]->GetGame($gameid);
          }
      }

      if (!$GotValidFeed)
      {
          $NewReader = new RA_RASReader($FeedURL."?CID=".$ChannelID);

          if ($NewReader != NULL)
          {
              $GLOBALS["GlobalReader"] =& $NewReader;
              $OutFile = fopen($CacheName, "w");
              fwrite($OutFile, serialize($GLOBALS["GlobalReader"]));
              fclose($OutFile);
          }
      }
	
  }
?>