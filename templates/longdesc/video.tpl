{if $game.video01}
	<div class="row" id="video">
		<div class="col-md-12">
			<h2><img src="/images/screenshot.png" alt="{$game.title} Video" width="20" height="20" align="top" />{$game.title} Video</h2>

			<div style="margin:0.5em 0 1em 2em; width:400px; height: 320px; float: left;">
				<iframe width="384" height="313" src="//www.youtube.com/embed/{$game.video01}" frameborder="0" allowfullscreen></iframe>
			</div>

			<div style="height: 350px;">
				Subscribe <img src="/images/youtube.png" alt="{$game.title} videos" width="16" height="16" align="top" /> <a href="http://www.youtube.com/user/gamefabrique">Gamefabrique game channel</a> on YouTube.
			</div>

		</div>
	</div>
{/if}