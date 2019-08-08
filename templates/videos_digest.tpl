<div id="video-content" style="margin-bottom:1em;">
<h1>Game Videos <sup>{$all_videos|@count}</sup></h1>


{foreach from=$all_videos item=video}
<div class="video-decription">
	<table>
		<tr>
			<td>
				<!--<div class="video-thumb">-->
					<a href="/videos/{$video.stringid}/"><img src="/previews/{$video.video01_snapshot}" alt="{$video.title} Video" width="130" height="96" title="Watch {$video.title} Game Video" /></a>
				<!--</div>-->
			</td>

			<td>
				<div class="video-text">
					<h2><img src="/images/play.png" alt="Play {$video.title} Video" width="26" height="26" align="top" /><a href="/videos/{$video.stringid}/">{$video.title} Video</a></h2>
					<p class="by">developed by {$video.developer}</p>
					<p>{$video.shortdesc}</p>
					
					<ul>
						<li><a href="/videos/{$video.stringid}/">Watch video</a></li>
						<li><a href="/games/{$video.stringid}/">Game description</a></li>
					</ul>
					
				</div>
			</td>

	
&nbsp;
</div>

{/foreach}
</div>