{*
	<p class="develop">Another games from {$game.title} developers</p>
	{section name=rows loop=$game.samedeveloper_titles}
		<img src="/images/arrow03.gif" alt="" width="7" height="7" align="middle">
		<a href="{$links.games}{$game.samedeveloper_titles[rows].stringid}/" class=a_gamename>
		{$game.samedeveloper_titles[rows].title}&nbsp;{$game.samedeveloper_titles[rows].version}</a>
		{$game.samedeveloper_titles[rows].shortdesc}
		<br />
	{/section}
*}
