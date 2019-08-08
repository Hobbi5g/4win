{strip}

{if !empty($h1_tag)}
	<h1>{$h1_tag}</h1>
{/if}


{if $num_of_results > 0}
	<div class="row mb-1">
		<div class="col-12 h2-divider"><span>Viewing games {($paginator_page-1)*$games_per_page+1} to {($paginator_page-1)*$games_per_page+$num_of_results}</span></div>
	</div>
{else}
	<div class="row">
		<div class="col-12">
			No games found
		</div>
	</div>
{/if}

{if empty($hide_paginator)}
	{include file="paginator.tpl"}
{/if}

<ul class="row game-list">
	{foreach $indexgame as $game}
		{include file="gamecard.tpl" game=$game}
		{*
		<li class="col-lg-3 col-md-3 col-sm-4 col-xs-6 game-short">
			<a href="/games/{$game.stringid}/" title="{$game.title}" class="game-logo">
				<img src="/images/220x112/{$game.stringid}.png" class="gamelogo" alt="{$game.title}" width="220" height="112" title="{$game.title}" />
				{$game.title}XX
			</a>
		</li>
		*}
	{/foreach}
</ul>

{if empty($hide_next_page_button)}
	{if $numpages != $paginator_page}
		<div class="row mb-1">
			<div class="col-12"><a href="{$gamelink}{$paginator_page+1}/" rel="next" class="button">Next Page</a></div>
		</div>
	{else}
		<div class="row mb-1">
			<div class="col-12"><a href="{$gamelink}" class="button">Back to first page</a></div>
		</div>
	{/if}
{/if}

{include file="paginator.tpl"}

{if !empty($portal.description)}
	<div class="row">
		<div class="col-12">
			{markdown($portal.description)}
		</div>
	</div>
{/if}


{/strip}
