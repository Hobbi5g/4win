{* strip *}
	{* if $lang.en *}{* alert('1');return false; onClick="javascript:urchinTracker ('/downloads/map'); " *}
	{*
		<form action="" method="post">
			<div style="width:95%; background:#eee; padding:10px 0px 10px 5px; border:1px #ccc solid; margin-bottom:10px">
				Subscribe to our maillist
				{if $validemail=='invalid'} <font color="red">(invalid e-mail)</font>{/if}
				{if $validemail=='duplicate'} <font color="red">(this e-mail is already in our database)</font>{/if}
				{if $validemail=='successful'} <font color="blue">(please check your mailbox)</font>{/if}:&nbsp;
				<input type="text" name="ename" style="border-style:solid; border: 1px solid #a0a0a0; margin: 0px 5px 0px 0px;" value="{if $enteredename}{$enteredename}{else}Your Name{/if}">
				<input type="text" name="email" style="width:8em;border-style:solid; border: 1px solid #a0a0a0; margin: 0px 0 0px 0px;" value="{if $enteredemail}{$enteredemail}{else}your@email{/if}">
				<input type="submit" name="submit" value="Subscribe" class="inp" style="font-size:13px; margin: 0px 0px 0px 10px;" onClick="javascript:urchinTracker('/subscribe');">
			</div>
		</form>
	*}
	{* /if *}
{* /strip *}
{*
<div style="width:95%;">
	<div style="background:#eee; padding:0.8em; border:1px #ccc solid; margin:0 0.5em 0.5em 0; float:left; height:100px;">
		{literal}
			<!-- GBN Code Start -->
			<script language="JavaScript">
				aj_pr = Math.floor(Math.random() * 1000000);
				document.write('<iframe width="100" height="100" noresize scrolling=No frameborder=0 marginheight=0 marginwidth=0 src="http://ads.gamesbannernet.com/servlet/ajrotator/19922/0/vh?z=gbn&dim=119&nocache='+aj_pr+'&referer='+escape(document.location.href)+'"></iframe>');
			</script>
			<noscript><a href="http://www.gamesbannernet.com/" target="_blank"><img src="http://www.gamesbannernet.com/img/spacer.gif" alt="Games Banner Network" width="1" height="1" border="0"></a></noscript>
			<!-- GBN Code End -->
		{/literal}
	</div>
	<div style="background:#eee; padding:1.4em 0.6em 0 0.8em; border:1px #ccc solid; margin-bottom:0.5em; float:left; height:100px; width:70%;">
		{literal}
			<script type="text/javascript"><!--
				google_ad_client = "pub-6005396277654882";
				/* 468x60, created 5/19/08, top of the page, near gbn */
				google_ad_slot = "5630057193";
				google_ad_width = 468;
				google_ad_height = 60;
			//-->
			</script>
			<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
		{/literal}
	</div>
</div>
*}
