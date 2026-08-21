<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Plan aktualisiert — Moovato</title>
</head>
<body style="margin:0;padding:0;background:#fafaf7;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;color:#202020;">

    <div style="display:none;max-height:0;overflow:hidden;font-size:1px;line-height:1px;color:#fafaf7;">
        Ihr {{ $planLabel }}-Plan wurde von unserem Team aktualisiert.
    </div>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#fafaf7;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="560" cellspacing="0" cellpadding="0" border="0" style="max-width:560px;background:#ffffff;border-radius:8px;border:1px solid #ede4d3;">
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 8px;font-size:11px;font-weight:600;color:#b9400d;text-transform:uppercase;letter-spacing:0.6px;">
                                Plan aktualisiert
                            </p>
                            <h1 style="margin:0 0 16px;font-size:20px;line-height:1.4;color:#202020;">
                                Hallo {{ $firstName }},
                            </h1>
                            <p style="margin:0 0 16px;font-size:14px;line-height:1.6;color:#475569;">
                                Wir haben den <strong>{{ $planLabel }}</strong>-Plan, auf dem
                                <strong>{{ $companyName }}</strong> läuft, aktualisiert. Folgende
                                Änderungen gelten ab sofort:
                            </p>

                            {{-- Diff table — one row per changed field. --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 20px;border:1px solid #ede4d3;border-radius:6px;overflow:hidden;">
                                @foreach($changes as $change)
                                    <tr>
                                        @if (! $loop->last)
                                            <td style="padding:12px 14px;border-bottom:1px solid #ede4d3;">
                                        @else
                                            <td style="padding:12px 14px;">
                                        @endif
                                            <p style="margin:0 0 4px;font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">
                                                {{ $change['label'] }}
                                            </p>
                                            <p style="margin:0;font-size:13px;color:#475569;">
                                                <span style="color:#94a3b8;text-decoration:line-through;">{{ $change['from'] }}</span>
                                                <span style="margin:0 8px;color:#b9400d;">→</span>
                                                <strong style="color:#202020;">{{ $change['to'] }}</strong>
                                            </p>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>

                            <p style="margin:0 0 20px;font-size:13px;line-height:1.6;color:#475569;">
                                Ihre bestehenden Inhalte wurden NICHT automatisch angepasst. Falls
                                Sie durch reduzierte Limits jetzt über den erlaubten Werten liegen,
                                sehen Sie einen Hinweis im Portal und können den Plan bei Bedarf
                                wechseln.
                            </p>

                            <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="border-radius:6px;background:#b9400d;">
                                        <a href="{{ $portalUrl }}" style="display:inline-block;padding:12px 24px;font-size:14px;font-weight:600;color:#ffffff;text-decoration:none;">
                                            Zum Firmenprofil
                                        </a>
                                    </td>
                                    <td style="padding-left:8px;">
                                        <a href="{{ $plansUrl }}" style="display:inline-block;padding:12px 20px;font-size:14px;font-weight:600;color:#b9400d;text-decoration:none;border:1px solid #b9400d;border-radius:6px;">
                                            Plan verwalten
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 32px;border-top:1px solid #ede4d3;font-size:11px;color:#94a3b8;">
                            Moovato — Sie erhalten diese E-Mail, weil Ihr Unternehmen aktuell auf
                            diesem Plan läuft.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
