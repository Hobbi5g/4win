{strip}
{assign var="is_main_page" value=true}

<h2>Game Updates</h2>
{include file="news.tpl"}

<h2>Top Notch Games</h2>

<ul class="row game-list">
	{foreach $index_games as $index_game}
		{include file="shortdesc2.tpl" game=$index_game}
	{/foreach}
</ul>


<h2>Caterogies</h2>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
		<ul>
			<li><a href="/game/mission/">Mission Games</a></li>
			<li><a href="/game/landscape/">Landscape Games</a></li>
			<li><a href="/game/firing/">Firing Games</a></li>
			<li><a href="/game/brick/">Brick Games</a></li>
			<li><a href="/game/helicopter/">Helicopter Games</a></li>
			<li><a href="/game/weapon/">Weapon Games</a></li>
		</ul>
	</div>

	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
		<ul>
			<li><a href="/game/submarine/">Submarine Games</a></li>
			<li><a href="/game/civilization/">Civilization Games</a></li>
			<li><a href="/game/casino/">Casino Games</a></li>
			<li><a href="/game/combine/">Combine Games</a></li>
			<li><a href="/game/www/">WWW Games</a></li>
		</ul>
	</div>

</div>

{/strip}
