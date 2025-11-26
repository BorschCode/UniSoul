<b>@lang('about.bot')</b><br>
@lang('about.name',['value'=>config('app.name')])<br>
@lang('about.username',['value'=>'@'.config('bot.username')])<br>
@lang('about.version',['value'=>config('app.version')])<br>

@if(config('bot.source'))
@lang('about.source',['value'=>'<a href="'.config('bot.source').'">'.trans('common.open_url').'</a>'])<br>
@endif

@if(config('bot.changelog'))
@lang('about.changelog',['value'=>'<a href="'.config('bot.changelog').'">'.trans('common.open_url').'</a>'])<br>
@endif
