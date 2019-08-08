<p class="comments">
	<strong>{$lang.comments_on} {$game.title}:</strong><br /><br />
	{section name=rows loop=$game.comments}
		<img src="/images/smile0{$game.comments[rows].icon}.gif" alt="" />
		{if $game.comments[rows].username != ''}<strong>{$game.comments[rows].username}:</strong> {/if}{$game.comments[rows].report}<br />
	{/section}
</p>

{if $game.comments_total == 0}
	<p class="total">{$lang.no_comments_yet}.</p>
{else}
	<p class="total">{$lang.total_comments_on} {$game.title}: {$game.comments_total}.<br />
	{if $morecomments}
		{$lang.back_to} <a href="./../" title="{$game.title} game">{if $lang.en}{$game.title} {$lang.game_homepage}{/if}{if $lang.ru}{$lang.game_homepage} {$game.title}{/if}</a> ({$lang.hide_comments}).</p>
	{else}
		<a href="{$links.games}{$game.stringid}/comments-on-{$game.stringid}/" rel="nofollow">{$lang.show_all_comments}</a> {$lang.on_game} {$game.title}.</p>
	{/if}
{/if}



{literal}
	<div id="disqus_thread"></div>
	<script type="text/javascript">
		/* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
		var disqus_shortname = 'games4win'; // required: replace example with your forum shortname

		/* * * DON'T EDIT BELOW THIS LINE * * */
		(function() {
			var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
			dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
			(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
		})();
	</script>
	<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
	<a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>
{/literal}


{*
{if $game.forumtopic}
	<p class="total">{$lang.commenting_is_closed}</p>
{/if}
*}
