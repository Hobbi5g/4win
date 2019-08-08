       rates.`netmode4`,
       longreports.gameid,
       longreports.report,
       longreports.checked,
       members.author,
       members.userid,
       members.login
FROM games
   INNER JOIN members ON (members.userid = $gameid)
   INNER JOIN longreports ON (longreports.gameid = $gameid)
   INNER JOIN rates ON (rates.gameid = $gameid)
WHERE 
   ((games.gameid = $gameid) and  (longreports.checked = 'Y')  )