{strip}
<div id="news">
	{foreach $allnews as $newsline}<p><em>{$newsline.tday}&nbsp;{$newsline.tmonth}&nbsp;{$newsline.tyear}</em> {$newsline.comment}</p>{/foreach}
</div>

{*{section name=rows loop=$allnews}<p>{* <em>{$allnews[rows].tday}&nbsp;{$allnews[rows].tmonth}&nbsp;{$allnews[rows].tyear}</em> *}{*{$allnews[rows].comment}</p>{$game.userreviews[rows].report}{/section}*}
{*<p><a href="/news/">All news</a></p>*}

{/strip}