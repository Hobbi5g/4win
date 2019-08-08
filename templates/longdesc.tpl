{literal}
	<script language="JavaScript" type="text/javascript">
		function openscreen(scfile) { window.open(scfile, '_blank','Toolbar=0, Location=0, Directories=0, Status=0, Menubar=0, Scrollbars=0, Resizable=0, Copyhistory=1, Width=660, Height=500'); }
	</script>
{/literal}

{strip}

<div itemscope itemtype="http://schema.org/VideoGame">
	<meta itemprop="applicationCategory" content="Game">
	<meta itemprop="operatingSystem" content="Windows">

	{if $game.tab_name!='download' || $game.tab_name!='screenshots'}
		{* free game *}
		{include file="longdesc/title.tpl"}

		{include file="longdesc/screenshots_top.tpl"}
		{include file="longdesc/descriptions.tpl"}

		<div id="last"></div>

		{include file="longdesc/video.tpl"}
		{include file="longdesc/forum-and-reviews.tpl"}

		{include file="longdesc/screenshots.tpl"}
		{include file="longdesc/similar_games.tpl"}
		{include file="longdesc/news.tpl"}

		{include file="longdesc/ratings.tpl"}

	{/if}

    {if $game.category01 != 'none' && $game.nextgames}
		<div class="row">
			<div class="col-md-12">
				<h2>More Games</h2>
			</div>
		</div>

		<ul class="row game-list">
			{foreach $game.nextgames as $nextgame}
				{include file="gamecard.tpl" game=$nextgame}
			{/foreach}
		</ul>

    {/if}



    {if !empty($game.nextgame)}
	<div id="slidebox">
		<a href="javascript:void(0);" class="close">X</a>
		<i>Next game:</i>
		{if $game.nextgame.platform == 'genesis' || $game.nextgame.platform == 'saturn' || $game.nextgame.platform == 'xbox'}
			<a href="/games/{$game.nextgame.stringid}/" class="text"><img src="/images/220x112/{$game.nextgame.stringid}.png" width="220" height="112" alt="{$game.nextgame.title}" /></a>
		{else}
			<a href="/games/{$game.nextgame.stringid}/" class="text"><img src="/img/{$game.nextgame.stringid}.jpg" width="{$game.nextgame.logo_w}" height="{$game.nextgame.logo_h}" alt="{$game.nextgame.title}" /></a>
		{/if}
		<p>Download <a href="/games/{$game.nextgame.stringid}/">{$game.nextgame.title}</a></p>
	</div>
	{/if}

</div>
{/strip}
