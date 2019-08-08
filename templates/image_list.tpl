{strip}
	{if !empty($game_screenshot_tag)}
		{if count($image_list) == 1}
			<div class="row" style="margin-bottom: 1em;">{*width~730px*}
				<div class="col-md-12"><img src="/screenshots/{$game_screenshot_tag}-{$image_list[0]}.jpg" alt="" class="w-100"></div>
			</div>
		{/if}

		{if count($image_list) == 2}
			<div class="row" style="margin-bottom: 1em;">{*width~360px*}
				<div class="col-md-6"><img src="/screenshots/{$game_screenshot_tag}-{$image_list[0]}.medium.jpg" alt="" class="w-100"></div>
				<div class="col-md-6"><img src="/screenshots/{$game_screenshot_tag}-{$image_list[1]}.medium.jpg" alt="" class="w-100"></div>
			</div>
		{/if}

		{if count($image_list) >= 3}
			<div class="row" style="margin-bottom: 1em;">{*width~240px*}
				<div class="col-md-4"><img src="/screenshots/{$game_screenshot_tag}-{$image_list[0]}.small.jpg" alt="" class="w-100"></div>
				<div class="col-md-4"><img src="/screenshots/{$game_screenshot_tag}-{$image_list[1]}.small.jpg" alt="" class="w-100"></div>
				<div class="col-md-4"><img src="/screenshots/{$game_screenshot_tag}-{$image_list[2]}.small.jpg" alt="" class="w-100"></div>
			</div>
		{/if}
	{/if}
{/strip}