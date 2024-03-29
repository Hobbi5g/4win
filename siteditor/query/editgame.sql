SELECT games.gameid,
       games.stringid,
       games.userid,
       games.developer,
       games.vendorid,
       games.title,
       games.version,
       games.`category01`,
       games.`category02`,
       games.shortdesc,
       games.longdesc,
       games.page_title,
       games.rating,
       games.`win311`,
       games.`win9x`,
       games.winnt,
       games.`win2k`,
       games.winxp,
       games.other,
       games.requires,
       games.gameprice,
       games.fsize,
       games.homepage,
       games.`download1`,
       games.`download2`,
       games.screenshot,
       games.orderpage,
       games.logo,
       games.counter,
       games.featscale,
       games.regdate,
       games.hidden,
       rates.gameid,
       rates.playability,
       rates.graphics,
       rates.sounds,
       rates.quality,
       rates.idea,
       rates.awards,
       rates.time,
       rates.action,
       rates.`age1`,
       rates.`age2`,
       rates.`age3`,
       rates.`age4`,
       rates.`age5`,
       rates.`age6`,
       rates.cpu,
       rates.video,
       rates.`netmode1`,
       rates.`netmode2`,
       rates.`netmode3`,
       rates.`netmode4`
FROM games,rates
WHERE 
   (
	  (games.gameid = rates.gameid) AND
      (games.gameid = $gameid)
   )