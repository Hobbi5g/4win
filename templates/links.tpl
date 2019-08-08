<div class="block">
<h1>{$linksblocktitle}</h1>
{foreach item=item from=$sitelinks name=loop}

<img src="/links/{$item.button}" alt="" width="88" height="31" class="gamelogo">
<div><p class="sitename"><a href="{$item.url}">{$item.sitename}</a></p>
<p>{$item.sitedesc}</p></div>

{/foreach}
</div>