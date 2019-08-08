{include file="breadcrumbs.tpl"}

{strip}

<div id="short_description" class="row">
	<div class="col-md-12">
		<h1>{$game.title}</h1>
	</div>

	<div class="col-lg-3 col-md-4 col-sm-12 col-xs-12 toc">

		<img src="/images/220x112/{$game.stringid}.png" alt="{$game.title}" class="gamelogo" />

		<p>Contents:</p>
		<ul>
			<li><a href="#reviews">Game Reviews</a></li>
			<li><a href="#download">Download</a>
				<ul>
					<li><a href="#system">System Requirements</a></li>
				</ul>
			</li>

			{if $have_screenshots}<li><a href="#screenshots">Screenshots</a></li>{/if}
			{if $game.video01}<li><a href="#video">Video</a></li>{/if}

			{if $game.similargames}<li><a href="#similar">Similar Games</a></li>{/if}

		</ul>

	</div>


	<div class="game col-lg-9 col-md-8 col-xs-8">

		<div class="download_btn">
			<img src="/images/downloadbig.png" alt="" />
			{*<a id="download_button" href="{$game.download1link}" title="Download {$game.title}" onClick="_gaq.push(['_trackEvent', 'Download', 'index-bottom-01', '{$game.stringid}']);">*}
			{*<a id="first_link1" href="{$game.download1link}" title="Download {$game.title}" >{$game.title} {$lang.download}</a>*}

			<a id="download_button" class="download_link" href="{$game.download1link}" title="Download {$game.title}">
				<h2>Download Now</h2>
				<span style="color: black;">This game is freeware</span>
			</a>
		</div>

		{*
		<table>
			{if !empty($game.developer)}<tr><td>Developer:</td><td>{$game.developer}</td></tr>{/if}
			<tr>
				<td>Genre:</td><td>{$game.category01|category_to_humanname}</td>
			</tr>
			<tr><td>Originally on:</td><td>{$game.platform|vendor_humanname}</td></tr>
			<tr><td>Runs on:</td><td><a href="#system">PC, Windows</a></td></tr>
			<tr>
				<td>Rating:</td>
				<td><img src="/img/{$game.stringid}-rating.png" alt="{$game.title} Rating" width="350" height="70" /></td>
			</tr>
			<tr>
				<td>
				{if !empty($game.homepage_direct) && ($game.platform == 'genesis' || strpos($game.homepage_direct, 'gamefabrique') > 0 || strpos($game.homepage_direct, 'getabandonware') > 0)}
					<li><a href="{$game.homepage_direct}" onClick="_gaq.push(['_trackEvent', 'Homepage', 'index-top', '{$game.stringid}']);">{$game.title} Homepage</a>{if strpos($game.homepage_direct, 'gamefabrique') > 0}<br />Visit and get 10000+ free games{/if}</li>
				{/if}
				</td>
			</tr>
		</table>
*}

		<ul>
			{if !empty($game.developer)}<li>Developer: {$game.developer}</li>{/if}
			<li>Genre: <span itemprop="genre">{$game.category01|category_to_humanname}</span></li>

			<li>
				Originally on:&#32;
				{foreach $game.platforms as $platform}
                    {$platform.platform_name|vendor_humanname}{include file="longdesc/release_year.tpl"}{if !$platform@last}, {/if}
				{/foreach}
			</li>

			{if !empty($game.also_known_as)}
				<li>Also known as:&#32;
					{foreach $game.also_known_as as $aka}
						{$aka}{if !$aka@last}, {/if}
					{/foreach}
				</li>
			{/if}

			<li>Runs on <a href="#system">PC, Windows</a></li>
			<li>Editor Rating:<br/>
				<img src="/img/{$game.stringid}-rating.png" alt="{$game.title} Rating" width="350" height="70" />
			</li>
			{if !empty($game.homepage_direct) && ($game.platform == 'genesis' || strpos($game.homepage_direct, 'gamefabrique') > 0 || strpos($game.homepage_direct, 'getabandonware') > 0)}
				<li><a href="{$game.homepage_direct}" onClick="_gaq.push(['_trackEvent', 'Homepage', 'index-top', '{$game.stringid}']);">{$game.title} Homepage</a>{if strpos($game.homepage_direct, 'gamefabrique') > 0}<br />Visit and get 10000+ free games{/if}</li>
			{/if}

			{if $user_rating.rating_count >= 1}
				<li itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
					User Rating:&nbsp;
					<span itemprop="ratingValue">{$user_rating.average_rating}</span>/<span itemprop="bestRating">10</span> - <span itemprop="ratingCount">{$user_rating.rating_count}</span> {ngettext msgid1="vote" msgid2="votes" n=$user_rating.rating_count}
				</li>
			{/if}

			<li>
				{if empty($user_rating.this_user_rating)}
					Rate this game:<br/>
					<div class="rating rating_10 d-inline-block stars">
						<ul>
							<li class="star_1"><a href=""></a></li><li class="star_2"><a href=""></a></li><li class="star_3"><a href=""></a></li><li class="star_4"><a href=""></a></li><li class="star_5"><a href=""></a></li>
						</ul>
					</div>
				{else}
					Your rating:&nbsp;
					{$user_rating.this_user_rating} {ngettext msgid1="star" msgid2="stars" n=$user_rating.this_user_rating}
				{/if}
			</li>
		</ul>

		<div class="addthis_inline_share_toolbox mb-2"></div>

	</div>


</div>

{/strip}