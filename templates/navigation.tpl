{*<h4><a href="{$links.forum}" title="{$lang.visit_forum}">{$lang.forum}</a></h4>*}
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-6 col-xs-6">
		{include file="gamespots.tpl"}
	</div>

{if $template == 'links'}{*control type="sitelinks" lang="`$lang.lang`"*}
{else}
	<div class="col-lg-12 col-md-12 col-sm-6 col-xs-6">
		{include file="gameblock.tpl"}
		{*<img src="/images/videos.png" alt="" width="20" height="20" align="top" style="margin-right:0.3em;"/><a href="/videos/" title="Video game trailers">Game Videos</a>*}
	</div>
{/if}
</div>

