<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Plan-Änderungsanfrage — Moovato</title>
</head>
<body style="margin:0;padding:0;background:#fafaf7;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;color:#202020;">
    <div style="display:none;max-height:0;overflow:hidden;font-size:1px;line-height:1px;color:#fafaf7;">
        {{ $requesterName }} möchte den Plan von {{ $fromLabel }} auf {{ $toLabel }} ändern.
    </div>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#fafaf7;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="560" cellspacing="0" cellpadding="0" border="0" style="max-width:560px;background:#ffffff;border-radius:8px;border:1px solid #ede4d3;">
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 8px;font-size:11px;font-weight:600;color:#b9400d;text-transform:uppercase;letter-spacing:0.6px;">
                                Plan-Änderungsanfrage
                            </p>
                            <h1 style="margin:0 0 16px;font-size:20px;line-height:1.4;color:#202020;">
                                Neue Anfrage von {{ $companyName }}
                            </h1>
                            <p style="margin:0 0 16px;font-size:14px;line-height:1.6;color:#475569;">
                                <strong>{{ $requesterName }}</strong> ({{ $requesterEmail }})
                                möchte den Plan von <strong>{{ $fromLabel }}</strong> auf
                                <strong>{{ $toLabel }}</strong> ändern.
                            </p>
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 24px;">
                                <tr>
                                    <td style="background:#ffede6;border:1px solid #b9400d;border-radius:6px;padding:10px 14px;">
                                        <span style="font-size:13px;color:#b9400d;font-weight:600;">
                                            {{ $fromLabel }} → {{ $toLabel }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:0 0 24px;font-size:14px;line-height:1.6;color:#475569;">
                                Öffnen Sie die Warteschlange, um die Anfrage zu prüfen und zu
                                genehmigen oder abzulehnen.
                            </p>
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="border-radius:6px;background:#b9400d;">
                                        <a href="{{ $queueUrl }}" style="display:inline-block;padding:12px 24px;font-size:14px;font-weight:600;color:#ffffff;text-decoration:none;">
                                            Anfrage öffnen
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 32px;border-top:1px solid #ede4d3;font-size:12px;color:#94a3b8;">
                            Moovato — automatische Benachrichtigung an das Admin-Team.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
