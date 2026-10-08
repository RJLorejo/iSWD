
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employee Password Reset</title>
</head>
<body style="margin:0;padding:32px 16px;background:#f1f5f9;font-family:Arial,sans-serif;color:#0f172a">

    <div style="max-width:520px;margin:auto;background:white;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden">

        <div style="background:#07569a;padding:28px;text-align:center;color:white">
            <h2 style="margin:0;font-size:24px">iSWD</h2>
            <p style="margin:8px 0 0;font-size:13px">Sagay Water District</p>
        </div>

        <div style="padding:32px;text-align:center">

            <h2 style="font-size:22px;margin:0 0 12px">
                Employee Password Recovery
            </h2>

            <p style="font-size:14px;line-height:24px;color:#475569">
                Use the following verification code to reset your employee account password.
            </p>

            <div style="margin:28px 0;padding:22px;background:#f0f9ff;border:1px solid #bae6fd;border-radius:12px">
                <span style="font-size:36px;letter-spacing:9px;font-weight:bold;color:#0369a1">
                    {{ $otp }}
                </span>
            </div>

            <p style="font-size:14px;color:#475569">
                This code expires in <strong>5 minutes</strong>.
            </p>

            <p style="font-size:13px;line-height:22px;color:#64748b">
                If you did not request this password reset, you can safely ignore this email.
                Never share your verification code with anyone.
            </p>
        </div>

        <div style="padding:18px;background:#f8fafc;text-align:center;font-size:12px;color:#64748b">
            &copy; {{ date('Y') }} Sagay Water District
        </div>
    </div>
</body>
</html>
