<!DOCTYPE html>
<html lang="en">
<head>
<title>{$title}</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<meta name="verifyownership" content="569bddc629a48daf3847cf6528ef2da2" />
	{if !empty($mainlongdescription.html_meta_desc)}<meta name="description" content="{$mainlongdescription.html_meta_desc}" />{/if}

{*<meta property="og:title" content="Games4Win" />
<meta property="og:type" content="website" />
<meta property="og:url" content="http://www.games4win.com" />
<meta property="og:image" content="http://www.games4win.com/images/logo_new.png" />
<meta property="fb:admins" content="100001773321372" />
<meta property="fb:page_id" content="153361391386951" />*}


{if !empty($page_metadescription)}
<meta name="description" content="{$page_metadescription}">
{/if}

{*<style type="text/css" media="screen">
<!--
@import url("/styles.css");

{if $template eq 'links' or $template eq 'submit'}@import url("/styles2.css");{/if}
-->
</style>
*}

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
	<link href="/css/styles.css" rel="stylesheet">

	<meta name="google-site-verification" content="PrufmRKhqJ_wck6mgDN1d20vf-GC6TpKzNe9qe3x4PM" />
    {if !empty($game.stringid)}<link rel="canonical" href="https://games4win.com/games/{$game.stringid}/" />{/if}

{*
{literal}
<script language="JavaScript" type="text/javascript">
function la(l)
{
	switch(l)
	{
		case 1:	this.location = '/index-ru/'; break;
	}
	return false;
}
</script>
{/literal}
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
*}
{literal}
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script type="text/javascript" src="/js/jquery.colorbox-min.js"></script>
{/literal}
	{if !empty($is_installcore)}{literal}<script type="text/javascript" src="https://js.games4windownloads.com/dl.min.js"></script>{/literal}{/if}
{*
	<script src="http://www.html-manager.com/scripts/AMIdownload.js"></script>
	<script type="text/javascript" src="/js/jquery-1.5.min.js"></script>
*}

{if not $isLocal}{literal}
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-135177-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>{/literal}{/if}
	{literal}
		<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5cca2a578547683e"></script>
	{/literal}
</head>

<body>
{strip}

<div id="main" class="container">

	{include file="header.tpl"}

	<div class="row">

		<div class="col-md-10 col-lg-push-2 order-2 content">
			{$yield}
			<a href="#top"><img src="/images/top.png" alt="Top" width="60" height="20" /></a>
		</div>{* 170 margin *}


		<div id="navigation" class="col-md-2 order-1">
			{include file="navigation.tpl"}
		</div>

	</div>

	{include file="footer.tpl"}

</div>{* main *}
{/strip}

{if ($template eq 'games' or $template eq 'search') && $stringid == ''}
	{*include file="games_digest.tpl"*}
{else}
	{*include file="main.tpl"*}
{/if}

{literal}
	<script type="text/javascript">
		jQuery(document).ready( function() {
			$("div#screenshots-bottom a").colorbox( {rel:'screenshot'} );
            $("div#screenshots_top a").colorbox( {rel:'screenshot'} );
		});
	</script>
{/literal}

{if ($template eq 'existing_game')}
{literal}
	<script type="text/javascript">
		$(document).ready(function(){
			$(window).scroll(function(){
				var distanceTop = $('#last').offset().top - $(window).height();
				if  ($(window).scrollTop() > distanceTop)
					$('#slidebox').animate({'right':'0px'},400);
				else
					$('#slidebox').stop(true).animate({'right':'-430px'},100);
			});
			$('#slidebox .close').bind('click',function(){
				$(this).parent().remove();
			});
		});

        $(document).ready(function(){

            $('.star_1').hover(function() {
                $('.stars').addClass('rating_2_hover');
            });

            $('.star_2').hover(function() {
                $('.stars').addClass('rating_4_hover');
            });

            $('.star_3').hover(function() {
                $('.stars').addClass('rating_6_hover');
            });

            $('.star_4').hover(function() {
                $('.stars').addClass('rating_8_hover');
            });

            $('.star_5').hover(function() {
                $('.stars').addClass('rating_10_hover');
            });

            $('.star_1, .star_2, .star_3, .star_4, .star_5').mouseleave(function() {
                $('.stars').removeClass('rating_2_hover rating_4_hover rating_6_hover rating_8_hover rating_10_hover');
            }).click(function() {
                jQuery.post('/ajax/vote/', {
                    stars: $(this).attr("class"),
                    game: '{/literal}{$game_slug}{literal}'
                } ).always(function() {

                }).done(function(data) {
                    $('.stars').replaceWith(data);
                });

                return false;
            });

        });


	</script>
{/literal}
{/if}

</body>
</html>{*<!-- pgt:{$pgt}-->*}