{strip}
	{if $game.have_screenshots}
		<div class="row" id="screenshots">
			<div class="col-12">
				<h2>{$game.title} Screenshots and Media</h2>
			</div>
		</div>

		{foreach $game.all_game_data as $game_data}

			{if !empty($game_data.screenshots)}
				<div class="row">
					<div class="col-12">
						<h3>{$game_data.platform|vendor_humanname} Screenshots</h3>
					</div>
				</div>

				<div itemscope itemtype="http://schema.org/ImageGallery" class="row" id="screenshots-bottom">
					{foreach $game_data.screenshots as $screenshot}
						<figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject" class="col-md-3 col-xs-6 figure">
							<a itemprop="contentUrl" href="/up/{$screenshot.name}" title="{$screenshot.title}" rel="screenshot">
								<img itemprop="thumbnail" src="/up/thumbs/{$screenshot.thumb}" alt="{$screenshot.title}" title="{$screenshot.title}" class="ss">
							</a>
						</figure>
					{/foreach}
				</div>
			{/if}

		{/foreach}

    {/if}
{/strip}