{*Блок новостей перенесен в longdesc*}
{*
{if $game.news_count > 0}
<h3>{$game.title} news</h3>
{section name=rows loop=$game.news}<div>{$game.news[rows].comment}</div>{/section}
{else}

<h3>{$game.title} news</h3>
<div>No {$game.title} related news found.</div>
{/if}
*}