<div id="video">

    {strip}
    <div id="short_decription" style="margin-bottom:2em;">
        <div style="float:left;width: 230px;">
            <img src="/img/{$game.stringid}.jpg" alt="{$game.title}" class="gamelogo" width="220" height="112" />
        </div>


        <div id="game">
            <h1>{$game.title}</h1>
            <div style="font-size:150%; font-style:italic; margin-bottom:1em;">&#147;{$game.shortdesc}&#148;</div>
        </div>
    </div>
    {/strip}

    <h2 class="big">Watch {$game.title} Video</h2>

	<table>
		<tr>
			<td style="width:420px;">
				<object width="384" height="313">
					<iframe width="384" height="313" src="//www.youtube.com/embed/{$game.video01}?autoplay=1" frameborder="0" allowfullscreen></iframe>
				</object>
			</td>
			
			<td>
				<table>
					{foreach from=$three_videos item=video}
					<tr>
						<td><a href="/videos/{$video.stringid}/"><img src="/previews/{$video.video01_snapshot}" style="margin:1px;padding:1px;border:1px solid #FFC7E4;width:130px;float:left;height:96px;" alt="{$video.title} Video" width="130" height="96" title="Watch {$video.title} Game Video" /></a></td>
						<td style="padding-left:5px;">
							<a href="/videos/{$video.stringid}/" class="title" title="">{$video.title} Video</a> - {$video.shortdesc|truncate:120:'...':true}
							<br />
							<img src="/images/play_small.png" alt="" style="margin-right:4px;" /><a href="/videos/{$video.stringid}/">Play</a>
						</td>
					</tr>
					{/foreach}
				</table>
			</td>
		</tr>
	</table>
	
	<p>{$game.longdesc|truncate:600:'...':true} <strong>Read more about <a href="/games/{$stringid}/">{$game.title} Game</a></strong></p>

</div>