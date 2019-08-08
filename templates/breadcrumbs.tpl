{strip}
	<div class="row">
		{if $topline}
			{if $topline == 'games'}
				<div itemscope itemtype="http://data-vocabulary.org/Breadcrumb" id="breadcrumbs" class="col-md-12">
					<ol>
						<li><a href="https://games4win.com/" title="Games4Win">Games4Win</a>&nbsp;› Games</li>
					</ol>
				</div>
			{/if}

			{if $topline == 'gamedesc'}
				<div id="breadcrumbs" class="col-md-12">
					<ol itemscope itemtype="https://schema.org/BreadcrumbList">

						{*
						<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
							<a itemprop="item" href="https://games4win.com/" title="Games4Win">
								<span itemprop="name">Games4Win</span>
							</a>
							<meta itemprop="position" content="1" />
						</li>
						<li>›</li>
						*}

						<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
							<a itemprop="item" href="https://games4win.com/games/" title="Games">
								<span itemprop="name">Games</span>
							</a>
							<meta itemprop="position" content="2" />
						</li>
						<li>›</li>

						{if !empty($game.category01_url)}
							<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
								<a itemprop="item" href="https://games4win.com/{$game.category01_url}/" title="">
									<span itemprop="name">{$game.category01_link}</span>
								</a>
								<meta itemprop="position" content="3" />
							</li>
						{else}
							<li>{$game.category01_link}</li>
                        {/if}

						<li>›</li>
						<li>{$game.title}</li>
					</ol>
				</div>
			{/if}

			{if $topline == 'search'}
				<div itemscope itemtype="http://data-vocabulary.org/Breadcrumb" id="breadcrumbs" class="col-md-12">
					<a href="https://games4win.com/" title="Games">Games4Win</a>&nbsp;› Search games
				</div>
			{/if}
		{/if}
	</div>
{/strip}


{*

<ol itemscope itemtype="https://schema.org/BreadcrumbList">
	<li itemprop="itemListElement" itemscope
		itemtype="https://schema.org/ListItem">
		<a itemtype="https://schema.org/Thing"
		   itemprop="item" href="https://example.com/books">
			<span itemprop="name">Books</span></a>
		<meta itemprop="position" content="1" />
	</li>
	›
	<li itemprop="itemListElement" itemscope
		itemtype="https://schema.org/ListItem">
		<a itemtype="https://schema.org/Thing"
		   itemprop="item" href="https://example.com/books/sciencefiction">
			<span itemprop="name">Science Fiction</span></a>
		<meta itemprop="position" content="2" />
	</li>
	›
	<li itemprop="itemListElement" itemscope
		itemtype="https://schema.org/ListItem">
		<a itemtype="https://schema.org/Thing"
		   itemprop="item" href="https://example.com/books/sciencefiction/ancillaryjustice">
			<span itemprop="name">Ancillary Justice</span></a>
		<meta itemprop="position" content="3" />
	</li>
</ol>

*}