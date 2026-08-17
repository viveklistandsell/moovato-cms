<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Willkommen bei Moovato</title>
</head>
<body style="margin:0;padding:0;background:#fafaf7;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;color:#202020;">
    <div style="display:none;max-height:0;overflow:hidden;font-size:1px;line-height:1px;color:#fafaf7;">
        Ihre Partner-Registrierung wurde freigegeben. Passwort jetzt einrichten.
    </div>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#fafaf7;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="560" cellspacing="0" cellpadding="0" border="0" style="max-width:560px;background:#ffffff;border-radius:8px;border:1px solid #ede4d3;">
                    <tr>
                        <td style="padding:32px;">
                            <h1 style="margin:0 0 16px;font-size:20px;line-height:1.4;color:#202020;">
                                Willkommen bei Moovato, {{ $firstName }}!
                            </h1>
                            <p style="margin:0 0 16px;font-size:14px;line-height:1.6;color:#475569;">
                                Ihre Partner-Registrierung wurde geprüft und freigegeben.
                                Bitte richten Sie jetzt Ihr Passwort ein, um sich anzumelden
                                und Ihr Firmenprofil zu pflegen.
                            </p>

                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin:24px 0;">
                                <tr>
                                    <td style="background:#b9400d;border-radius:6px;">
                                        <a href="{{ $setPasswordUrl }}"
                                           style="display:inline-block;padding:12px 24px;font-size:14px;font-weight:600;color:#ffffff;text-decoration:none;">
                                            Passwort einrichten
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 16px;font-size:12px;line-height:1.6;color:#94a3b8;">
                                Der Link ist 7 Tage gültig. Wenn Sie ihn nicht selbst
                                angefordert haben, können Sie diese E-Mail ignorieren.
                            </p>

                            <p style="margin:0 0 4px;font-size:12px;line-height:1.6;color:#94a3b8;">
                                Falls der Button nicht funktioniert, kopieren Sie
                                folgenden Link in Ihren Browser:
                            </p>
                            <p style="margin:0;font-size:11px;line-height:1.5;color:#475569;word-break:break-all;">
                                {{ $setPasswordUrl }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 32px;border-top:1px solid #ede4d3;font-size:11px;color:#94a3b8;">
                            Moovato — Ihr Umzugspartner in Berlin
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
