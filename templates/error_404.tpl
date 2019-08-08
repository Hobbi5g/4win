<div class="row mt-1">
	<div class="col-md-12">
		<h1>{$page_title}</h1>
	</div>


	<div class="col-md-12">
		<h2>Try other games</h2>
		<ul>
			<li><a href="/game/mission/">Mission Games</a></li>
			<li><a href="/game/landscape/">Landscape Games</a></li>
			<li><a href="/game/firing/">Firing Games</a></li>
			<li><a href="/game/brick/">Brick Games</a></li>
			<li><a href="/game/helicopter/">Helicopter Games</a></li>
			<li><a href="/game/weapon/">Weapon Games</a></li>
			<li><a href="/game/submarine/">Submarine Games</a></li>
			<li><a href="/game/civilization/">Civilization Games</a></li>
			<li><a href="/game/casino/">Casino Games</a></li>
			<li><a href="/game/combine/">Combine Games</a></li>
			<li><a href="/game/www/">WWW Games</a></li>
		</ul>
	</div>

	{*<div class="col-md-12">
		<h2>Try other games</h2>
	</div>*}

</div>

<div class="row">
	{for $column = 0 to 2}
		<div class="col-md-4">
			<ul>
				{foreach from=$portals item=game}
					{if $game@iteration % 6 == $column}<li><a href="/games/{$game.identifier}/">{$game.title}</a></li>{/if}
				{/foreach}
			</ul>
		</div>
	{/for}
</div>

<div class="row mt-1">
	{for $column = 3 to 5}
		<div class="col-md-4">
			<ul>
				{foreach from=$portals item=game}
					{if $game@iteration % 6 == $column}<li><a href="/games/{$game.identifier}/">{$game.title}</a></li>{/if}
				{/foreach}
			</ul>
		</div>
	{/for}
</div>

