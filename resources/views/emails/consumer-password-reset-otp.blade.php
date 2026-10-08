<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        iSWD Password Reset Verification Code
    </title>

</head>

<body
    style="
        margin: 0;
        padding: 0;
        background-color: #f1f5f9;
        font-family: Arial, Helvetica, sans-serif;
        color: #0f172a;
    "
>

    <table
        width="100%"
        cellpadding="0"
        cellspacing="0"
        border="0"
        style="background-color: #f1f5f9; padding: 32px 15px;"
    >

        <tr>

            <td align="center">

                <table
                    width="100%"
                    cellpadding="0"
                    cellspacing="0"
                    border="0"
                    style="
                        max-width: 560px;
                        background-color: #ffffff;
                        border-radius: 16px;
                        overflow: hidden;
                        border: 1px solid #e2e8f0;
                    "
                >

                    {{-- Header --}}
                    <tr>

                        <td
                            style="
                                padding: 26px 30px;
                                background-color: #0369a1;
                                color: #ffffff;
                            "
                        >

                            <div
                                style="
                                    font-size: 23px;
                                    font-weight: 700;
                                "
                            >
                                iSWD
                            </div>

                            <div
                                style="
                                    margin-top: 5px;
                                    font-size: 12px;
                                    color: #e0f2fe;
                                "
                            >
                                Sagay Water District Consumer Portal
                            </div>

                        </td>

                    </tr>


                    {{-- Content --}}
                    <tr>

                        <td style="padding: 32px 30px;">

                            <h1
                                style="
                                    margin: 0;
                                    font-size: 22px;
                                    color: #0f172a;
                                "
                            >
                                Reset Your Password
                            </h1>


                            <p
                                style="
                                    margin: 16px 0 0;
                                    font-size: 14px;
                                    line-height: 1.7;
                                    color: #475569;
                                "
                            >
                                We received a request to reset the
                                password for your iSWD Consumer Portal
                                account.
                            </p>


                            <p
                                style="
                                    margin: 16px 0 0;
                                    font-size: 14px;
                                    line-height: 1.7;
                                    color: #475569;
                                "
                            >
                                Enter the verification code below on
                                the password recovery page.
                            </p>


                            {{-- OTP --}}
                            <div
                                style="
                                    margin: 28px 0;
                                    padding: 22px;
                                    text-align: center;
                                    background-color: #f0f9ff;
                                    border: 1px solid #bae6fd;
                                    border-radius: 12px;
                                "
                            >

                                <div
                                    style="
                                        margin-bottom: 8px;
                                        font-size: 12px;
                                        font-weight: 600;
                                        text-transform: uppercase;
                                        letter-spacing: 1px;
                                        color: #0369a1;
                                    "
                                >
                                    Verification Code
                                </div>


                                <div
                                    style="
                                        font-size: 34px;
                                        font-weight: 700;
                                        letter-spacing: 8px;
                                        color: #0c4a6e;
                                    "
                                >
                                    {{ $otp }}
                                </div>

                            </div>


                            <p
                                style="
                                    margin: 0;
                                    font-size: 14px;
                                    line-height: 1.7;
                                    color: #475569;
                                "
                            >
                                This verification code will expire in
                                <strong>5 minutes</strong>.
                            </p>


                            <p
                                style="
                                    margin: 18px 0 0;
                                    font-size: 13px;
                                    line-height: 1.7;
                                    color: #64748b;
                                "
                            >
                                If you did not request a password reset,
                                you can safely ignore this email. Your
                                password will remain unchanged.
                            </p>

                        </td>

                    </tr>


                    {{-- Footer --}}
                    <tr>

                        <td
                            style="
                                padding: 20px 30px;
                                border-top: 1px solid #e2e8f0;
                                background-color: #f8fafc;
                                text-align: center;
                                font-size: 11px;
                                color: #94a3b8;
                            "
                        >

                            &copy; {{ date('Y') }}
                            Sagay Water District
                            &middot;
                            iSWD Consumer Portal

                        </td>

                    </tr>

                </table>

            </td>

        </tr>

    </table>

</body>

</html>