<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Unblock Your VeriScore Dashboard</title>
    <!-- Import Work Sans Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />

    <style>
        body,
        table,
        td,
        a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        table {
            border-collapse: collapse !important;
        }

        body {
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            background-color: #f4f7f6;
            font-family:
                "Work Sans",
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                Roboto,
                Helvetica,
                Arial,
                sans-serif;
        }

        /* Interactive element transitions */
        .btn-link:hover {
            text-decoration: underline !important;
            color: #054d82 !important;
        }

        .cta-btn:hover {
            background-color: #054d82 !important;
        }

        /* Media Queries for Perfect Mobile Scaling */
        @media screen and (max-width: 480px) {
            .wrapper-padding {
                padding: 24px 12px !important;
            }

            .card-padding {
                padding-left: 20px !important;
                padding-right: 20px !important;
            }

            .header-padding {
                padding: 32px 20px 20px 20px !important;
            }

            .banner-padding {
                padding: 16px 20px !important;
            }

            .cta-btn {
                padding: 14px 28px !important;
                font-size: 15px !important;
            }

            .footer-padding {
                padding: 32px 20px !important;
            }
        }
    </style>
</head>

<body>
    <!-- Wrapper Table -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="wrapper-padding"
        style="background-color: #f4f7f6; padding: 48px 16px">
        <tr>
            <td align="center" valign="top">
                <!-- Main Card Container -->
                <table border="0" cellpadding="0" cellspacing="0" width="100%"
                    style="
                            max-width: 560px;
                            background-color: #ffffff;
                            border-radius: 16px;
                            overflow: hidden;
                            box-shadow: 0 10px 25px rgba(7, 102, 173, 0.05);
                            border: 1px solid #e1e8ed;
                        ">
                    <!-- Top Intro Banner -->
                    <tr>
                        <td class="banner-padding"
                            style="
                                    background-color: #0766ad;
                                    padding: 20px 40px;
                                    text-align: center;
                                ">
                            <p
                                style="
                                        margin: 0;
                                        font-family:
                                            &quot;Work Sans&quot;, sans-serif;
                                        font-size: 13px;
                                        line-height: 18px;
                                        color: #ffffff;
                                        font-weight: 400;
                                        letter-spacing: 0.2px;
                                    ">
                                VeriScore turns your business data into
                                clear health and credit-readiness scores
                                trusted by banks, partners, and investors
                                across Africa.
                            </p>
                        </td>
                    </tr>

                    <!-- Branding Header -->
                    <tr>
                        <td align="center" class="header-padding" style="padding: 44px 40px 28px 40px">
                            <img style="width: 200px;" src="{{ asset('/brands/app-logo.png') }}" alt="VeriScore Logo" />
                        </td>
                    </tr>

                    <!-- Email Body -->
                    <tr>
                        <td class="card-padding"
                            style="
                                    padding: 0 40px 24px 40px;
                                    text-align: left;
                                ">
                            <h2
                                style="
                                        margin: 0 0 16px 0;
                                        font-family:
                                            &quot;Work Sans&quot;, sans-serif;
                                        font-size: 20px;
                                        font-weight: 600;
                                        color: #1e2a38;
                                        letter-spacing: -0.3px;
                                    ">
                                {{ $user?->name ? 'Dear ' . $user?->name : 'Hello Dear!' }},
                            </h2>
                            <p
                                style="
                                        margin: 0;
                                        font-family:
                                            &quot;Work Sans&quot;, sans-serif;
                                        font-size: 15px;
                                        line-height: 26px;
                                        color: #4a5560;
                                        font-weight: 400;
                                    ">
                                Thank you for taking the first step toward
                                unlocking your business's financial
                                credibility by creating an account on
                                VeriScore! To get full access to your
                                dashboard and claim your Capital Passport,
                                we just need to finalize two quick details
                                on your profile.
                            </p>
                        </td>
                    </tr>

                    <!-- Step 1 -->
                    <tr>
                        <td class="card-padding"
                            style="
                                    padding: 0 40px 20px 40px;
                                    text-align: left;
                                ">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="
                                        background-color: #f8fafc;
                                        border: 1px solid #e1e8ed;
                                        border-radius: 12px;
                                    ">
                                <tr>
                                    <td style="padding: 20px 24px">
                                        <p
                                            style="
                                                    margin: 0 0 6px 0;
                                                    font-family:
                                                        &quot;Work Sans&quot;,
                                                        sans-serif;
                                                    font-size: 15px;
                                                    font-weight: 700;
                                                    color: #1e2a38;
                                                ">
                                            Step 1 &nbsp;&middot;&nbsp;
                                            Verify your email address
                                        </p>
                                        <p
                                            style="
                                                    margin: 0;
                                                    font-family:
                                                        &quot;Work Sans&quot;,
                                                        sans-serif;
                                                    font-size: 14px;
                                                    line-height: 22px;
                                                    color: #4a5560;
                                                ">
                                            This ensures your account
                                            remains secure and that you
                                            never miss critical updates
                                            regarding your score.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- CTA Button -->
                    <tr>
                        <td align="center" class="card-padding" style="padding: 4px 40px 28px 40px">
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center"
                                        style="
                                                border-radius: 8px;
                                                background-color: #0766ad;
                                            ">
                                        <a href="{{ config('app.frontend_url') }}/verifyOtp?email={{ $user?->email ?? '' }}&otp={{ $otp ?? '' }}&date={{ now() }}"
                                            target="_blank" class="cta-btn"
                                            style="
                                                    display: inline-block;
                                                    padding: 14px 36px;
                                                    font-family:
                                                        &quot;Work Sans&quot;,
                                                        sans-serif;
                                                    font-size: 16px;
                                                    font-weight: 600;
                                                    color: #ffffff; 
                                                    text-decoration: none;
                                                    border-radius: 8px;
                                                ">
                                            Verify My Email Address
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Step 2 -->
                    <tr>
                        <td class="card-padding"
                            style="
                                    padding: 0 40px 28px 40px;
                                    text-align: left;
                                ">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="
                                        background-color: #f8fafc;
                                        border: 1px solid #e1e8ed;
                                        border-radius: 12px;
                                    ">
                                <tr>
                                    <td style="padding: 20px 24px">
                                        <p
                                            style="
                                                    margin: 0 0 6px 0;
                                                    font-family:
                                                        &quot;Work Sans&quot;,
                                                        sans-serif;
                                                    font-size: 15px;
                                                    font-weight: 700;
                                                    color: #1e2a38;
                                                ">
                                            Step 2 &nbsp;&middot;&nbsp; Add
                                            your phone number
                                        </p>
                                        <p
                                            style="
                                                    margin: 0;
                                                    font-family:
                                                        &quot;Work Sans&quot;,
                                                        sans-serif;
                                                    font-size: 14px;
                                                    line-height: 22px;
                                                    color: #4a5560;
                                                ">
                                            To ensure reliable account
                                            security and give you a direct
                                            line for important account
                                            follow-ups, please ensure your
                                            mobile number is updated in your
                                            profile settings.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Wrap up -->
                    <tr>
                        <td class="card-padding"
                            style="
                                    padding: 0 40px 36px 40px;
                                    text-align: left;
                                ">
                            <p
                                style="
                                        margin: 0 0 16px 0;
                                        font-family:
                                            &quot;Work Sans&quot;, sans-serif;
                                        font-size: 14px;
                                        line-height: 24px;
                                        color: #607274;
                                    ">
                                According to recent industry data,
                                businesses with verified, structured
                                profiles are the ones positioned to win
                                institutional trust and scale. It takes less
                                than two minutes to complete these steps and
                                put your data to work for you.
                            </p>
                            <p
                                style="
                                        margin: 0 0 4px 0;
                                        font-family:
                                            &quot;Work Sans&quot;, sans-serif;
                                        font-size: 15px;
                                        line-height: 24px;
                                        color: #4a5560;
                                    ">
                                If you didn't create this account or need
                                any help with the registration process,
                                simply reply directly to this email, our
                                support team is ready to assist.
                            </p>
                            <p
                                style="
                                        margin: 20px 0 0px 0;
                                        font-family:
                                            &quot;Work Sans&quot;, sans-serif;
                                        font-size: 15px;
                                        line-height: 24px;
                                        color: #4a5560;
                                        font-weight: 500;
                                    ">
                                Best regards,<br />
                                <span style="color: #1e2a38; font-weight: 600">The VeriScore Team</span>
                            </p>

                            <hr
                                style="
                                        border: 0;
                                        border-top: 1px solid #e1e8ed;
                                        margin: 32px 0 0 0;
                                    " />
                        </td>
                    </tr>

                    <!-- Contact & Meta Links -->
                    <tr>
                        <td class="card-padding"
                            style="
                                    padding: 0 40px 36px 40px;
                                    text-align: left;
                                ">
                            <p
                                style="
                                        margin: 0;
                                        font-family:
                                            &quot;Work Sans&quot;, sans-serif;
                                        font-size: 13px;
                                        line-height: 20px;
                                        color: #607274;
                                    ">
                                <strong style="color: #1e2a38">Need assistance?</strong>
                                Visit our
                                <a href="https://veriscore.app" target="_blank" class="btn-link"
                                    style="
                                            color: #0766ad;
                                            text-decoration: none;
                                            font-weight: 600;
                                        ">website</a>
                                or connect directly with our
                                <a href="mailto:info@veriscore.app" class="btn-link"
                                    style="
                                            color: #0766ad;
                                            text-decoration: none;
                                            font-weight: 600;
                                        ">support
                                    team</a>.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer Details -->
                    <tr>
                        <td class="footer-padding"
                            style="
                                    padding: 36px 40px;
                                    background-color: #f8fafc;
                                    border-top: 1px solid #e1e8ed;
                                    text-align: center;
                                ">
                            <p
                                style="
                                        margin: 0 0 10px 0;
                                        font-family:
                                            &quot;Work Sans&quot;, sans-serif;
                                        font-size: 12px;
                                        line-height: 18px;
                                        color: #8a99a8;
                                    ">
                                No. 234 AMAC, Lagos State, Nigeria.
                            </p>
                            <p
                                style="
                                        margin: 0 0 20px 0;
                                        font-family:
                                            &quot;Work Sans&quot;, sans-serif;
                                        font-size: 12px;
                                        color: #8a99a8;
                                    ">
                                <a href="https://veriscore.app/privacy-policy"
                                    style="
                                            color: #8a99a8;
                                            text-decoration: underline;
                                        ">Privacy
                                    Policy</a>
                                &nbsp;&bull;&nbsp;
                                <a href="https://veriscore.app/terms-of-service"
                                    style="
                                            color: #8a99a8;
                                            text-decoration: underline;
                                        ">Terms
                                    of Service</a>
                            </p>
                            <p
                                style="
                                        margin: 0;
                                        font-family:
                                            &quot;Work Sans&quot;, sans-serif;
                                        font-size: 12px;
                                        color: #b2c0cc;
                                        font-weight: 500;
                                    ">
                                &copy; 2026 VeriScore. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
                <!-- /Main Card Container -->
            </td>
        </tr>
    </table>
    <!-- /Wrapper Table -->
</body>

</html>
