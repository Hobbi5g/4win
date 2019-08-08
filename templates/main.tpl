{strip}

{if $template eq 'games'}
	{if $stringid != ''}{control type="longdescription" game=$mainlongdescription}{/if}
{/if}

<div id="main" class="container">

	{include file="header.tpl"}

	<div class="row">

		<div class="col-md-10 col-md-push-2 content">

			{if $template eq 'copyright'}
				{include file="copyright.tpl"}
			{/if}

			{if $template eq 'search'}
				{include file="search.tpl"}
			{/if}

			{if $template eq 'index'}
				{include file="main_page.tpl"}
			{/if}

			{if $template eq 'videos'}
				{if $video_page}
					{include file="video.tpl"}
				{else}
					{include file="videos_digest.tpl"}
				{/if}
			{/if}


			{if $template eq 'games' or $template eq 'search'}
				{if $stringid != ''}
					{include file="longdesc.tpl"}
				{else}
					{include file="games_digest.tpl"}
				{/if}
			{/if}

			{if $template eq 'links'}{include file="links.tpl"}{/if}
			{if $template eq 'submit'}{include file="submit.tpl"}{/if}

			{if $template eq 'games' and $stringid != ''}
				{include file="relatednews.tpl"}
			{/if}

			<a href="#top"><img src="/images/top.png" alt="Top" width="60" height="20" /></a>


		</div>{* 170 margin *}


		<div id="navigation" class="col-md-2 col-md-pull-10">
			{include file="navigation.tpl"}
		</div>


	</div>

	{include file="footer.tpl"}


</div>{* main *}

{/strip}