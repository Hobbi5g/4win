{*
<script>
    function AMIdownload(ProductName, ProductDownloadLink, ImageUrl, Identifier) {
        var Installer = "http://www.wonderfuldownload.com/download.php?version=1.1.5.26&campid=3385" +
        "&instid[appname]=" +
        escape(ProductName) +
        "&instid[appsetupurl]=" +
        escape(ProductDownloadLink) +
        "&instid[appimageurl]=" +
        escape(ImageUrl) +
        "&prefix=" +
        escape(ProductName) ; window.location.assign(Installer);

        _gaq.push(['_trackEvent', 'Download', 'top-button', escape(Identifier)]);

        return false;
    }
</script>


_gaq.push(['_trackEvent', 'Download', 'top-button', escape(Identifier)]);
*}
{literal}
	<script>
		function AMIdownload2(ProductName, ProductDownloadLink, ImageUrl, Identifier) {
			return AMIdownload(true, '3385', '1.1.5.26', ProductName, ProductDownloadLink, '', ImageUrl, ProductName);
		}
	</script>
{/literal}
