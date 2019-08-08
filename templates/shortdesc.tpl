{* called from digest *}
{strip}
{if !empty($game)}

	<li class="game col-md-12{if $lastone} nodivider{/if}">

	{if $game.platform == 'genesis'}

			{if !$game.have_teaser}

				<a href="{$links.games}{$game.stringid}/" title="{$game.title}" class="">
					{*<img src="/images/220x112/{$game.stringid}.png" class="gamelogo" alt="{$game.title}" width="{$game.logo_w}" height="{$game.logo_h}" title="{$game.title}" style="float: left; width:{$game.logo_w}px;height:{$game.logo_h}px; margin-right: 0.75em; margin-bottom:1.2em" />*}
                    <img src="/images/220x112/{$game.stringid}.png" class="gamelogo" alt="{$game.title}" width="{$game.logo_w}" height="{$game.logo_h}" title="{$game.title}" style="" />
				</a>

				<div class="">
					<h2><a href="{$links.games}{$game.stringid}/">{$game.title}</a></h2>

					{*{if ($game.rating == 10 || $game.rating == 9) }<sup class="version" style="background-color: #FF0084; color:white;">Best!</sup>{/if}
					{if ($game.rating == 8 || $game.rating == 7) }<sup class="version" style="background-color: #0084FF; color:white;">Good</sup>{/if}*}

					<p class="by">{if $game.gameprice == 0}{if $lang.en}Freeware game {/if}{/if}{$lang.by} {$game.developer}</p>

					<p>{$game.shortdesc}</p>
					{if $term && $game.snippet01}<p>..{$game.snippet01}..</p>{if $game.snippet02}<p>..{$game.snippet02}..</p>{/if}{/if}

					{*
					<ul class="linksmall">
						<li><img src="/images/download_small.png" alt="Download" width="12" height="12" style="margin-right:0.2em;"><a href=# onClick="return AMIdownload2('{$game.title_nodashes}','{$game.download1link}', 'http://www.games4win.com/img/{$game.stringid}.jpg', '{$game.stringid}');">Download now</a> <span style="color:gray;">{$game.fsize_formatted}</span></li>
						*}
                        {*<li><a href="" title="{$game.title}">Read More</a></li>*}
						{*<li style="display: inline; margin-left:1.5em;">{if $game.gameprice>1}<img src="/images/coins.png" alt="Buy" width="12" height="12" align="top" style="margin-right:0.2em;"><a href="{$game.orderpage}">{$lang.buy|capitalize}</a> <font style="color:gray;">{$game.gameprice_formatted}</font>{/if}{if $game.gameprice==0}{$lang.freeware_game}{if $game.category01 != 'none'} ({$game.category01}){/if}{/if}{if $game.gameprice==-1}Demo{/if}</li>*}
					{*</ul>*}


				</div>

				{if !empty($game.digest_review)}
					<blockquote class="col-md-10">
                    	{*<img src="/images/mystery-man.png" style="float:left;border: 1px solid gray;padding:1px;margin-right:0.5em;" width="50" height="38" />*}
						<b>Editor-in-chief says:</b> <i>{$game.digest_review}</i>
						{*<span style="color:gray;">&nbsp;&#8227;&nbsp;Verdict: {$game.rating}/10</span>*}
					</blockquote>
				{/if}

	
			{else}{* have teaser *}

				{*<a href="{$links.games}{$game.stringid}/" title="{$game.title}" class="grid_3">
					<img src="/images/220x112/{$game.stringid}.png" class="gamelogo" alt="{$game.title}" width="{$game.logo_w}" height="{$game.logo_h}" title="{$game.title}" style="" />
				</a>

				<div class="grid_9">
					<h2><a href="{$links.games}{$game.stringid}/">{$game.title}</a></h2>
					*}

					{*{if ($game.rating == 10 || $game.rating == 9) }<sup class="version" style="background-color: #FF0084; color:white;">Best!</sup>{/if}*}
					{*{if ($game.rating == 8 || $game.rating == 7) }<sup class="version" style="background-color: #0084FF; color:white;">Good</sup>{/if}*}

					{*<p class="by">{if $game.gameprice == 0}{if $lang.en}Freeware game {/if}{/if}{$lang.by} {$game.developer}</p>*}

                    {*
					<ul style="margin:1em 0 1em 0; list-style:none;">
						<li><img src="/images/download_small.png" alt="Download" width="12" height="12" style="margin-right:0.2em;"><a href="{$game.download1link}" onClick="_gaq.push(['_trackEvent', 'Download', 'digest', '{$game.stringid}']);">Download now</a> <font style="color:gray;">{$game.fsize_formatted}</font></li>
						<li>{if $game.gameprice>1}<img src="/images/coins.png" alt="Buy" width="12" height="12" align="top" class="icon12"><a href="{$game.orderpage}">{$lang.buy|capitalize}</a> <font style="color:gray;">{$game.gameprice_formatted}</font>{/if}{if $game.gameprice==0}{$lang.freeware_game}{if $game.category01 != 'none'} ({$game.category01}){/if}{/if}{if $game.gameprice==-1}Demo{/if}</li>
					</ul>
					*}

				{*</div>


				<div class="clear"></div>

				<img src="/images/big/{$game.stringid}.jpg" alt="{$game.title}" width="590" />

				<p>{$game.shortdesc}</p>
				{if $term && $game.snippet01}<p>..{$game.snippet01}..</p>{if $game.snippet02}<p>..{$game.snippet02}..</p>{/if}{/if}
                *}

                
                <a href="/games/{$game.stringid}/">
                    <div class="main_image">
                        <img src="/images/big/{$game.stringid}.jpg" alt="{$game.title}">
                        <div class="desc" style="display: block;">
                            <div class="block" style="opacity: 0.7;">
                                <h2>{$game.title}</h2>
                                <p>{$game.shortdesc}</p>
                            </div>
                        </div>
                    </div>
                </a>

                {if !empty($game.digest_review)}
                    <blockquote class="col-md-10">
                        {*<img src="/images/mystery-man.png" style="float:left;border: 1px solid gray;padding:1px;margin-right:0.5em;" width="50" height="38" />*}
                        <b>Editor-in-chief says:</b> <i>{$game.digest_review}</i>
                    </blockquote>
                {/if}



			{/if}


	{else}{* not genesis game *}

			<a href="{$links.games}{$game.stringid}/" title="{$game.title}">
				<img src="/img/{$game.stringid}.jpg" class="gamelogo" alt="{$game.title}" width="{$game.logo_w}" height="{$game.logo_h}" title="{$game.title}" style="float: left; width:{$game.logo_w}px;height:{$game.logo_h}px; margin-right: 0.75em; margin-bottom:1.2em" />
			</a>

			<div style="margin-left: 110px;display: block;">
				<h2><a href="{$links.games}{$game.stringid}/">{$game.title}</a></h2>

				{*{if ($game.rating == 10 || $game.rating == 9) }<sup class="version" style="background-color: #FF0084; color:white;">Best!</sup>{/if}*}
				{*{if ($game.rating == 8 || $game.rating == 7) }<sup class="version" style="background-color: #0084FF; color:white;">Good</sup>{/if}*}

				<p class="by">{if $game.gameprice == 0}{if $lang.en}Freeware game {/if}{/if}{$lang.by} {$game.developer}</p>
				{if $game.have_teaser}<img src="/images/big/{$game.stringid}.jpg" alt="{$game.title}" width="590" />{/if}

				<p>{$game.shortdesc}</p>
				{if $term && $game.snippet01}<p>..{$game.snippet01}..</p>{if $game.snippet02}<p>..{$game.snippet02}..</p>{/if}{/if}

				{if !empty($game.digest_review)}
				<div style="font:italic 80% Georgia,'Times New Roman',serif; margin:24px 0 24px 26px; padding:12px 0 12px 12px; border-left:1px solid #FF007F; width:80%;background-color:#fafafa;">
					{*<img src="/images/mystery-man.png" style="float:left;border: 1px solid gray;padding:1px;margin-right:0.5em;" width="50" height="38" />*}
					<b>Editor-in-chief says:</b> <i>{$game.digest_review}</i>
					<span style="color:gray;">&nbsp;&#8227;&nbsp;Verdict: {$game.rating}/10</span>
				</div>
				{/if}

				{*
				<ul style="margin:1em 0 1em 0; list-style:none;">
					<li style="display: inline;"><img src="/images/download_small.png" alt="Download" width="12" height="12" style="margin-right:0.2em;"><a href="{$game.download1link}" onClick="_gaq.push(['_trackEvent', 'Download', 'digest', '{$game.stringid}']);">Download now</a> <font style="color:gray;">{$game.fsize_formatted}</font></li>
					<li style="display: inline; margin-left:1.5em;">{if $game.gameprice>1}<img src="/images/coins.png" alt="Buy" width="12" height="12" align="top" style="margin-right:0.2em;"><a href="{$game.orderpage}">{$lang.buy|capitalize}</a> <font style="color:gray;">{$game.gameprice_formatted}</font>{/if}{if $game.gameprice==0}{$lang.freeware_game}{if $game.category01 != 'none'} ({$game.category01}){/if}{/if}{if $game.gameprice==-1}Demo{/if}</li>
				</ul>
		*}

			


			</div>



	{/if}

	</li>


{/if}
{/strip}
