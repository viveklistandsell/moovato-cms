<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Status Ihrer Registrierung</title>
</head>
<body style="margin:0;padding:0;background:#fafaf7;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;color:#202020;">
    <div style="display:none;max-height:0;overflow:hidden;font-size:1px;line-height:1px;color:#fafaf7;">
        Rückmeldung zu Ihrer Moovato-Registrierung.
    </div>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#fafaf7;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="560" cellspacing="0" cellpadding="0" border="0" style="max-width:560px;background:#ffffff;border-radius:8px;border:1px solid #ede4d3;">
                    <tr>
                        <td style="padding:32px;">
                            <h1 style="margin:0 0 16px;font-size:20px;line-height:1.4;color:#202020;">
                                Hallo {{ $firstName }},
                            </h1>
                            <p style="margin:0 0 16px;font-size:14px;line-height:1.6;color:#475569;">
                                vielen Dank für Ihre Registrierung bei Moovato. Leider
                                können wir Ihre Anmeldung im Moment nicht bestätigen.
                            </p>

                            <div style="margin:20px 0;padding:16px;background:#ffede6;border-left:3px solid #b9400d;border-radius:4px;">
                                <p style="margin:0 0 4px;font-size:12px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">
                                    Begründung
                                </p>
                                <p style="margin:0;font-size:14px;line-height:1.6;color:#475569;">
                                    {{ $reason }}
                                </p>
                            </div>

                            <p style="margin:0 0 16px;font-size:14px;line-height:1.6;color:#475569;">
                                Sie können sich gerne erneut mit angepassten Angaben
                                registrieren, wenn Sie die genannten Punkte klären
                                können.
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
