{if $game.keywordscount > 0}
	<p>
		<b>{$lang.related_games}:</b>
		{section name=rows loop=$game.generatedkeywords}
			<a href="/game{if $lang.ru}-ru{/if}/{$game.generatedkeywords[rows]}/">{$game.generatedkeywords[rows]}</a>{if not $smarty.section.rows.last}, {else}.{/if}
		{/section}
	</p>
{/if}
