{if !empty($platform.release_year)}
{if intval($platform.release_year) >= 1991 && intval($platform.release_year) <= 2019}
&nbsp;(<a href="/year/{$platform.release_year}/">{$platform.release_year}</a>)
{else}
&nbsp;({$platform.release_year})
{/if}
{/if}