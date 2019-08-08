{strip}
{if $game.forumtopic}
{* WORKS
	<h2><img src="/images/discuss.png" alt="{$game.title} forum" width="20" height="20" align="top">{$lang.discuss_it} <a href="/forum/viewtopic.php?id={$game.forumtopic}">{if $lang.en}{$game.title} {$lang.game_forum}{/if}{if $lang.ru}{$lang.game_forum} {$game.title}{/if}</a> ({$game.forumposts} {if $game.forumposts == 1}{$lang.post}{else}{$lang.posts}{/if})</h2>
*}
{/if}

{if $game.userreviews_total > 0}

	<div class="row">
		<div class="col-12">
			<h2>Game Reviews</h2>
		</div>

		{foreach $game.userreviews as $review}
			<div class="col-md-12 review-bq">
				{$review.report_wiki|markdown_headchanger|markdown}
			</div>
		{/foreach}

	</div>

{/if}


{/strip}