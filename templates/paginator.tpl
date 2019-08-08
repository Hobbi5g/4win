{*strip*}
{*	{if $numpages > 0}
		<div class="nav">{if $term}{$term} game {/if}pages&nbsp;
			{section name=rows loop=$numpages}
				{if $smarty.section.rows.index+1 != $currpage}
					<a href="{$gamelink}{if not $smarty.section.rows.first}{$smarty.section.rows.index+1}/{/if}">{$smarty.section.rows.index+1}</a> 
				{else}
					<span class="selected">{$smarty.section.rows.index+1}</span>
				{/if}
			{/section}
		</div>
	{/if}
	*}
{*/strip*}


{if $numpages > 0}
	<div class="row mb-1">
		<div class="col-md-12">
			<div class="nav-pagination">{if $term}{$term} game {/if}pages&nbsp;

				{for $page=0 to $numpages-1}
					{if $page+1 != $paginator_page}
						<a href="{$gamelink}{if $page != 0}{$page+1}/{/if}">{$page+1}</a>
					{else}
						<span class="selected">{$page+1}</span>
					{/if}
				{/for}

			</div>
		</div>
	</div>
{/if}


{*
{if $numpages > 0}
	<div class="row">
		<div class="col-md-12">
			<div class="nav-pagination">{if $term}{$term} game {/if}pages&nbsp;

				{section name=rows loop=$numpages}
					{if $smarty.section.rows.index+1 != $currpage}
						<a href="{$gamelink}{if not $smarty.section.rows.first}{$smarty.section.rows.index+1}/{/if}">{$smarty.section.rows.index+1}</a>
					{else}
						<span class="selected">{$smarty.section.rows.index+1}</span>
					{/if}
				{/section}

			</div>
		</div>
	</div>
{/if}
*}