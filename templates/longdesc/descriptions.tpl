{strip}

{foreach $game.main_reviews as $review}
	<div class="row">
		<div class="col-md-12 review">
			{if empty($review.review_html)}
				{$review.report_wiki|markdown_headchanger|markdown}
			{else}
                {$review.review_html}
            {/if}
		</div>
	</div>
{/foreach}

<div id="download" class="row">

	<div class="col-lg-12">
		<h2>Download {$game.title}</h2>
		<div class="download_btn">
			<img src="/images/downloadbig.png" alt="" />
			<a id="download_button" class="download_link" href="{$game.download1link}" title="Download {$game.title}">
				<h2>Download Now</h2>
				<span style="color: black;">This game is freeware</span>
			</a>
		</div>
	</div>

	<div class="col-lg-12" id="system">
		<h3>System Requirements</h3>
		<p class="sys">PC compatible, {$game.requires_html}</p>
		<p class="sys">
			<strong>Systems:</strong>&nbsp;
			{if $game.win311=='Y' or $game.win9x=='Y' or $game.winnt=='Y' or $game.win2k=='Y'}<img src="/images/win9x.gif" alt="Win9x" width="16" height="16" />{/if}
			{if $game.win311=='Y'}Windows&nbsp;3.11{if $game.win9x=='Y' or $game.winnt=='Y' or $game.win2k=='Y'}, {/if}{/if}
			{if $game.win9x=='Y'}Windows&nbsp;9x{if $game.winnt=='Y' or $game.win2k=='Y'}, {/if}{/if}
			{if $game.winnt=='Y'}Windows&nbsp;NT{if $game.win2k=='Y'}, {/if}{/if}
			{if $game.win2k=='Y'}Windows&nbsp;2000{/if}
			{if $game.winxp=='Y' and ($game.win311=='Y' or $game.win9x=='Y' or $game.win9x=='Y' or $game.win2k=='Y')} {/if}
			{if $game.winxp=='Y'}<img src="/images/winxp.gif" alt="WinXP" width="16" height="16" />Windows&nbsp;XP, Vista, Win 7, Win 8, Win 10{/if}.
		</p>

		<p class="sys">
			<strong>Game features:</strong>
				{if $game.netmode1=='Y'}<img src="/images/single.gif" alt="{$game.title} supports single mode" width="16" height="16" />Single game mode{/if}
				{if $game.netmode2=='Y' or $game.netmode3=='Y' or $game.netmode4=='Y'}{if $game.netmode1=='Y'} {/if}<img src="/images/multi.gif" alt="{$game.title} supports multiplayer mode" width="16" height="16">Multiplayer ({if $game.netmode2=='Y'}Hotseat{if $game.netmode3=='Y' or $game.netmode4=='Y'}, {/if}{/if}{if $game.netmode3=='Y'}LAN{if $game.netmode4=='Y'}, {/if}{/if}{if $game.netmode3=='Y'}Internet{/if}){/if}
		</p>

	</div>

</div>


{/strip}