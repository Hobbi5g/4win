<div id="review">
	<table cellspacing="0"><tr><td>
		{if !$isLocal}
		{literal}
			<script type="text/javascript"><!--
				google_ad_client = "pub-6005396277654882";
				google_ad_width = 336;
				google_ad_height = 280;
				google_ad_format = "336x280_as";
				google_ad_type = "text";
				google_ad_channel ="0098401842";
				google_color_border = "FFFFFF";
				google_color_bg = "FFFFFF";
				google_color_link = "000099";
				google_color_url = "000099";
				google_color_text = "000000";
			//--></script>
			<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
		{/literal}
		{/if}
	</td><td width="100%">
		{include file="longdesc/screenshots.tpl"}
	</td></tr></table>
	{strip}
		{include file="longdesc/news.tpl"}
		{include file="longdesc/comments.tpl"}
	{/strip}
</div>