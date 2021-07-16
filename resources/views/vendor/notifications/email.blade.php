@php
    $email_template = \App\Models\EmailTemplate::where('blade_name', 'verify_email')->first();

    $email_template_texts = [];

    if ($email_template) {
       $email_template_texts = \App\Models\EmailTemplateText::where('email_template_id', $email_template->id)->get()->pluck('value', 'key');
    }

@endphp

@component('mail::message')
@if(isset($email_template_texts['greeting']))
    # {{ $email_template_texts['greeting'] }}
@else
    # {{ 'greeting' }}
@endif

@if(isset($email_template_texts['intro_texts']))
    {{ $email_template_texts['intro_texts'] }}
@else
    {{ 'intro_texts' }}
@endif
<br/>
@if(isset($email_template_texts['action_text']))
    <?php
    switch ($level) {
        case 'success':
        case 'error':
            $color = $level;
            break;
        default:
            $color = 'primary';
    }
    ?>
    @component('mail::button', ['url' => $actionUrl, 'color' => $color])
    {{ $email_template_texts['action_text'] }}
    @endcomponent
@endisset


@if(isset($email_template_texts['outro_texts']))
    {{ $email_template_texts['outro_texts'] }}
@else
    {{ 'outro_texts' }}
@endif

@if(isset($email_template_texts['regards']))
    {{ $email_template_texts['regards'] }}
@else
    {{ 'regards' }}
@endif<br>
@if(isset($email_template_texts['app_name']))
    {{ $email_template_texts['app_name'] }}
@else
    {{ 'app_name' }}
@endif

@slot('subcopy')

@if(isset($email_template_texts['footer_texts']))
    {{ $email_template_texts['footer_texts'] }}
@else
    {{ 'footer_texts' }}
@endif

<span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
@endslot
@endcomponent
