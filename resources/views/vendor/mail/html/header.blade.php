@props(['url'])
<tr>
<td class="header" style="padding: 36px 0 20px 0; text-align: center;">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
    @if(isset($message) && file_exists(public_path('images/logo.png')))
        <img src="{{ $message->embed(public_path('images/logo.png')) }}" 
             alt="{{ config('app.name', 'VexaHost') }}" 
             height="32"
             style="height: 32px; max-height: 32px; width: auto; border: 0; display: block; margin: 0 auto; outline: none; text-decoration: none;">
    @else
        <span style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 20px; font-weight: 800; letter-spacing: -0.5px; color: #000000; text-transform: uppercase;">
            VEXAHOST
        </span>
    @endif
</a>
</td>
</tr>
