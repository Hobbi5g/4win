{strip}
	{if !empty($game.screenshots)}
		<div class="row" id="screenshots_top">
			{for $c = 0 to min(4, count($game.screenshots)) - 1}
				{assign var=screenshot value=$game.screenshots[$c]}
				<div class="col-md-3">
					<a href="/up/{$screenshot.name}" title="{$screenshot.title}" rel="screenshot"><img src="/up/thumbs/{$screenshot.thumb}" alt="{$screenshot.title}" title="{$screenshot.title}" class="ss"></a>
				</div>
			{/for}
		</div>
	{/if}
{/strip}