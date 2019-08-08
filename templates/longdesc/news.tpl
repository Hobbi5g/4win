{strip}
{if $game.news_count > 0}
	<div class="row">
		<div class="col-md-12 game-news">
			<h2>Game News</h2>
			<p>
				{foreach $game.news as $newsline}<span>{$newsline.nd}</span> {$newsline.comment}<br>{/foreach}
			</p>
		</div>
	</div>
{/if}
{/strip}