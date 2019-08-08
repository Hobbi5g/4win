{strip}
<div class="row" id="navigation">

	<div class="col-md-2 col-sm-2 col-xs-2">
		<a href="/" title="Games4Win - Main Page"><img src="/images/games4win.png" alt="Games4Win" class="logo" /></a>
	</div>

	<div class="col-md-2 col-sm-3 col-xs-6">
		<ul class="topmenu">
			<li class="first"><a href="/freeware-games/"><strong>Freeware Games</strong></a></li>
			<li><a href="/sega-games/"><strong>Sega Games</strong></a></li>
			<li class="first"><a href="/games/"><strong>All Games</strong></a></li>
			<li><a href="/arcade-games/" title="Arcade Games"><strong>Arcade Games</strong></a></li>
		</ul>
	</div>

	<div class="col-md-2 col-sm-3 col-xs-6">
		<ul class="topmenu">
			<li><a href="/board/">Board</a></li>
			<li><a href="/arkanoid-games/">Arkanoid</a></li>
			<li><a href="/adventure-games/">Adventure</a></li>
			<li><a href="/rpg/">RPG</a></li>
			<li><a href="/chess-games/">Chess</a></li>
		</ul>
	</div>

	<div class="col-md-2 col-sm-3 col-xs-4">
		<ul class="topmenu">
			<li><a href="/puzzle-games/">Puzzle</a></li>
			<li><a href="/shooter-games/">Shooters</a></li>
			<li><a href="/strategy-games/">Strategy</a></li>
			<li><a href="/pacman/" title="Pacman">Pacman</a> and <a href="/game/digger/" title="Digger">Digger</a></li>
			<li><a href="/game/zuma/" title="Zuma download">Zuma</a></li>
		</ul>
	</div>

	<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 search">
		{*
		<form method="post" id="searchform" action="{$links.search}">
			<input type="hidden" name="searchsubmit" value="yes" />
			<fieldset>
				<input name="srequest" type="text" onfocus="if(this.value=='Search') this.value='';" onblur="if(this.value=='') this.value='Search';" value="{if empty($srequest)}Search{else}{$srequest}{/if}" />
				<button type="submit" name="search"></button>
			</fieldset>
		</form>*}

		<form method="get" action="/" class="" id="searchform" style="height:60px;">
			<fieldset>
				<input type="text" name="q" value="{$search_query_for_form}" />
				{*<input type="submit" class="" style="height: 28px" value="Search" />*}
				<button type="submit" name="search"></button>
			</fieldset>
		</form>

        {if !$isLocal}
			<iframe src="https://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.games4win.com%2F&amp;layout=box_count&amp;show_faces=true&amp;width=150&amp;action=like&amp;colorscheme=light&amp;height=65" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:150px; height:65px;" allowTransparency="true"></iframe>
        {/if}

	</div>

</div>
{/strip}

{*
	<select name="catlist02" onchange="la(this.selectedIndex);">
		<option value="english"{if $lang.en} selected{/if}>English</option>
		<option value="russian"{if $lang.ru} selected{/if}>Pycckuu</option>
	</select>
*}
