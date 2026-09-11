<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Plan geändert — Moovato</title>
</head>
<body style="margin:0;padding:0;background:#fafaf7;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;color:#202020;">

    <div style="display:none;max-height:0;overflow:hidden;font-size:1px;line-height:1px;color:#fafaf7;">
        Ihr Moovato-Plan wurde von {{ $oldLabel }} auf {{ $newLabel }} geändert.
    </div>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#fafaf7;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="560" cellspacing="0" cellpadding="0" border="0" style="max-width:560px;background:#ffffff;border-radius:8px;border:1px solid #ede4d3;">
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 8px;font-size:11px;font-weight:600;color:#b9400d;text-transform:uppercase;letter-spacing:0.6px;">
                                @if($isDowngrade)Plan gewechselt @else Plan aktiviert @endif
                            </p>
                            <h1 style="margin:0 0 16px;font-size:20px;line-height:1.4;color:#202020;">
                                Hallo {{ $firstName }},
                            </h1>
                            <p style="margin:0 0 16px;font-size:14px;line-height:1.6;color:#475569;">
                                @if($isDowngrade)
                                    Ihr Plan für <strong>{{ $companyName }}</strong> wurde von
                                    <strong>{{ $oldLabel }}</strong> auf <strong>{{ $newLabel }}</strong>
                                    geändert. Die neuen Limits gelten ab sofort — Inhalte über den
                                    Grenzen wurden angepasst.
                                @else
                                    Vielen Dank! Ihr Plan für <strong>{{ $companyName }}</strong> wurde
                                    erfolgreich auf <strong>{{ $newLabel }}</strong> aktualisiert. Die
                                    neuen Funktionen sind bereits freigeschaltet.
                                @endif
                            </p>

                            {{-- Plan summary card --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:16px 0;border:1px solid #ede4d3;border-radius:6px;overflow:hidden;">
                                <tr>
                                    <td style="padding:14px;background:#ffede6;">
                                        <p style="margin:0 0 4px;font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">
                                            Neuer Plan
                                        </p>
                                        <p style="margin:0;font-size:18px;font-weight:700;color:#202020;">
                                            {{ $newLabel }}
                                            @if(! $isFree)
                                                <span style="font-size:13px;font-weight:400;color:#475569;">
                                                    · {{ $currency }}{{ $newPrice }} / {{ $period }}
                                                </span>
                                            @else
                                                <span style="font-size:13px;font-weight:400;color:#475569;">
                                                    · kostenlos
                                                </span>
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin:24px 0;">
                                <tr>
                                    <td style="background:#b9400d;border-radius:6px;">
                                        <a href="{{ $portalUrl }}"
                                           style="display:inline-block;padding:12px 24px;font-size:14px;font-weight:600;color:#ffffff;text-decoration:none;">
                                            Zum Firmenprofil
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 4px;font-size:12px;line-height:1.6;color:#94a3b8;">
                                Sie können Ihren Plan jederzeit über das Dashboard ändern.
                            </p>
                            <p style="margin:0;font-size:11px;line-height:1.5;color:#475569;word-break:break-all;">
                                {{ $portalUrl }}
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
