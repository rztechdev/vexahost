<tr>
<td>
<table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation" style="margin: 0 auto; padding: 24px 0 36px 0; text-align: center;">
<tr>
<td class="content-cell" align="center" style="color: #94a3b8; font-size: 12px; line-height: 1.6; text-align: center; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
<p style="margin: 0 0 8px 0; color: #64748b; font-size: 13px;">
    Butuh bantuan? Kunjungi <a href="{{ rtrim(config('app.url', 'https://vexahostcloud.my.id'), '/') }}/dashboard/support" style="color: #4f46e5; text-decoration: none; font-weight: 500;">Pusat Bantuan</a> atau hubungi tim teknis kami.
</p>
<p style="margin: 0 0 12px 0; color: #94a3b8; font-size: 12px;">
    {{ Illuminate\Mail\Markdown::parse($slot) }}
</p>
<p style="margin: 0; color: #cbd5e1; font-size: 11px;">
    Email ini dikirim secara otomatis terkait akun Anda di VexaHost Cloud Indonesia.
</p>
</td>
</tr>
</table>
</td>
</tr>
