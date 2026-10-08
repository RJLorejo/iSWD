<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        iSWD Email Verification
    </title>

</head>

<body
    style="
        margin: 0;
        padding: 0;
        background: #f8fafc;
        font-family: Arial, Helvetica, sans-serif;
        color: #0f172a;
    "
>

    <div
        style="
            max-width: 600px;
            margin: 0 auto;
            padding: 32px 16px;
        "
    >

        <div
            style="
                background: #ffffff;
                border: 1px solid #e2e8f0;
                border-radius: 16px;
                overflow: hidden;
            "
        >

            <div
                style="
                    background: #0369a1;
                    padding: 24px;
                    text-align: center;
                    color: #ffffff;
                "
            >

                <div
                    style="
                        font-size: 24px;
                        font-weight: 700;
                    "
                >
                    iSWD
                </div>

                <div
                    style="
                        margin-top: 4px;
                        font-size: 12px;
                    "
                >
                    Sagay Water District
                </div>

            </div>


            <div
                style="
                    padding: 32px 24px;
                "
            >

                <h1
                    style="
                        margin: 0;
                        font-size: 22px;
                    "
                >
                    Verify your email address
                </h1>


                <p
                    style="
                        margin-top: 16px;
                        font-size: 14px;
                        line-height: 1.7;
                        color: #475569;
                    "
                >
                    Use the verification code below to confirm your
                    email address for your iSWD Consumer Portal
                    registration.
                </p>


                <div
                    style="
                        margin: 28px 0;
                        padding: 20px;
                        border-radius: 12px;
                        background: #f0f9ff;
                        text-align: center;
                    "
                >

                    <div
                        style="
                            font-size: 12px;
                            font-weight: 700;
                            color: #0369a1;
                            text-transform: uppercase;
                            letter-spacing: 1px;
                        "
                    >
                        Verification Code
                    </div>


                    <div
                        style="
                            margin-top: 10px;
                            font-size: 36px;
                            font-weight: 700;
                            letter-spacing: 8px;
                            color: #0f172a;
                        "
                    >
                        {{ $otp }}
                    </div>

                </div>


                <p
                    style="
                        font-size: 14px;
                        line-height: 1.7;
                        color: #475569;
                    "
                >
                    This code expires in
                    <strong>5 minutes</strong>.
                </p>


                <p
                    style="
                        font-size: 13px;
                        line-height: 1.7;
                        color: #64748b;
                    "
                >
                    If you did not request this code, you can safely
                    ignore this email.
                </p>

            </div>


            <div
                style="
                    border-top: 1px solid #e2e8f0;
                    padding: 18px 24px;
                    text-align: center;
                    font-size: 11px;
                    color: #94a3b8;
                "
            >
                Sagay Water District · iSWD Consumer Portal
            </div>

        </div>

    </div>

</body>

</html>
