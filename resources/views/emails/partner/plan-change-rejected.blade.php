<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Plan-Änderung abgelehnt — Moovato</title>
</head>
<body style="margin:0;padding:0;background:#fafaf7;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;color:#202020;">
    <div style="display:none;max-height:0;overflow:hidden;font-size:1px;line-height:1px;color:#fafaf7;">
        Ihre Anfrage {{ $fromLabel }} → {{ $toLabel }} wurde abgelehnt.
    </div>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#fafaf7;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="560" cellspacing="0" cellpadding="0" border="0" style="max-width:560px;background:#ffffff;border-radius:8px;border:1px solid #ede4d3;">
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 8px;font-size:11px;font-weight:600;color:#b9400d;text-transform:uppercase;letter-spacing:0.6px;">
                                Plan-Anfrage abgelehnt
                            </p>
                            <h1 style="margin:0 0 16px;font-size:20px;line-height:1.4;color:#202020;">
                                Hallo {{ $firstName }},
                            </h1>
                            <p style="margin:0 0 16px;font-size:14px;line-height:1.6;color:#475569;">
                                Ihre Anfrage für <strong>{{ $companyName }}</strong>, den Plan von
                                <strong>{{ $fromLabel }}</strong> auf <strong>{{ $toLabel }}</strong>
                                zu ändern, wurde von unserem Team geprüft und leider abgelehnt.
                            </p>
                            @if($adminNote !== '')
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 20px;">
                                    <tr>
                                        <td style="background:#fafaf7;border:1px solid #ede4d3;border-radius:6px;padding:12px 14px;">
                                            <p style="margin:0 0 4px;font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">
                                                Notiz vom Team
                                            </p>
                                            <p style="margin:0;font-size:13px;line-height:1.6;color:#475569;">
                                                {{ $adminNote }}
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            @endif
                            <p style="margin:0 0 24px;font-size:14px;line-height:1.6;color:#475569;">
                                Ihr aktueller Plan bleibt unverändert. Sie können jederzeit eine
                                neue Anfrage stellen oder uns bei Rückfragen kontaktieren.
                            </p>
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="border-radius:6px;background:#b9400d;">
                                        <a href="{{ $plansUrl }}" style="display:inline-block;padding:12px 24px;font-size:14px;font-weight:600;color:#ffffff;text-decoration:none;">
                                            Pläne ansehen
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 32px;border-top:1px solid #ede4d3;font-size:12px;color:#94a3b8;">
                            Moovato — Sie erhalten diese E-Mail, weil eine Anfrage von Ihrem
                            Konto ausging.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
