{strip}

{if $game.similargames}
    <div class="row" id="similar">
        <div class="col-lg-12 col-md-12">
            <h2>Similar Games</h2>
        </div>
    </div>

    <ul class="row similar game-list">
        {foreach $game.similargames as $sim}
            {include file="gamecard.tpl" game=$sim}
        {/foreach}
    </ul>
{/if}

{/strip}