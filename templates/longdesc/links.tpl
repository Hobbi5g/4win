<table><tr><td>
{if $game.gameprice>0}
	<h2><img src="/images/order.png" alt="{$lang.buy|capitalize} {$game.title}" width="20" height="20" align="top" />{$lang.get_full_version} {$game.title}:</h2>
	<p class="link"><a href="{$game.orderpage}" title="{$lang.buy|capitalize} {$game.title}" onClick="_gaq.push(['_trackEvent', 'Order', 'index', '{$game.stringid}']);">{$lang.buy|capitalize} {$game.title}</a></p>
{/if}


{*if !$game.use_tabs*}

<h2><img src="/images/download.png" alt="{$lang.download|capitalize} {$game.title}" width="20" height="20" align="top" />{$lang.download|capitalize} {$game.title}:</h2>
<p class="link"><a id="first_link1" href="{$game.download1link}" title="Download {$game.title}" >{$game.title} {$lang.download}</a>{* ({$game.fsize_formatted}).*}</p>

{*<p class="link"><a id="first_link" href=# title="Download {$game.title}" onClick="return AMIdownload2('{$game.title_nodashes}','{$game.download1link}', 'http://www.games4win.com/img/{$game.stringid}.jpg', '{$game.stringid}');">{$game.title} {$lang.download}</a>{* ({$game.fsize_formatted}).*}{*</p>*}
{*/if*}

{if $game.download2 !=''}<p class="link"><a href="{$game.download2link}" onClick="_gaq.push(['_trackEvent', 'Download', 'index-bottom-02', '{$game.stringid}']);">{$game.title} {$lang.download}</a> link #2.</p>{/if}

</td></tr>
</table>