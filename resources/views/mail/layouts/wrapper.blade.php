<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>You've been invited to join APP</title>
    <!-- Import Work Sans Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght=400;500;600;700;800&display=swap"
        rel="stylesheet">

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
            font-family: 'Work Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        /* Interactive element transitions */
        .btn-link:hover {
            text-decoration: underline !important;
            color: #054d82 !important;
        }

        .btn-primary:hover {
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

            .btn-mobile {
                width: 100% !important;
                display: block !important;
                text-align: center !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
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
        style="background-color: #f4f7f6; padding: 48px 16px;">
        <tr>
            <td align="center" valign="top">

                <!-- Main Card Container -->
                <table border="0" cellpadding="0" cellspacing="0" width="100%"
                    style="max-width: 560px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(7, 102, 173, 0.05); border: 1px solid #e1e8ed;">

                    <!-- Top Intro Banner -->
                    <tr>
                        <td class="banner-padding"
                            style="background-color: #0766AD; padding: 20px 40px; text-align: center;">
                            <p
                                style="margin: 0; font-family: 'Work Sans', sans-serif; font-size: 13px; line-height: 18px; color: #ffffff; font-weight: 400; letter-spacing: 0.2px;">
                                {{-- You've been invited to collaborate on APP—the business health and credit-readiness
                                platform trusted across Africa. --}}
                                APP helps Nigerian businesses build a trusted, verifiable financial identity so
                                lenders and investors can confidently say yes. We translate your fragmented business
                                activities into a clear readiness profile that institutions trust.
                            </p>
                        </td>
                    </tr>


                    <!-- Branding Header -->
                    <tr>
                        <td align="center" class="header-padding" style="padding: 44px 40px 28px 20px">
                            <img style="width: 200px;" src="{{ asset('/brands/app-logo.png') }}"
                                alt="APP Brand Logo" />
                        </td>
                    </tr>

                    {{-- START --}}
                    @yield('content')
                    {{-- END --}}

                    <!-- Contact & Meta Links -->
                    <tr>
                        <td class="card-padding" style="padding: 0 40px 36px 40px; text-align: left;">
                            <p
                                style="margin: 0; font-family: 'Work Sans', sans-serif; font-size: 13px; line-height: 20px; color: #607274;">
                                <strong style="color: #1E2A38;">Didn't expect this?</strong> If you weren't expecting an
                                invitation from this organization, you can safely ignore this email. If you need help,
                                please contact our <a href="mailto:info@APP.app" class="btn-link"
                                    style="color: #0766AD; text-decoration: none; font-weight: 600;">support team</a>.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer Details -->
                    <tr>
                        <td class="footer-padding"
                            style="padding: 36px 40px; background-color: #f8fafc; border-top: 1px solid #E1E8ED; text-align: center;">
                            <p
                                style="margin: 0 0 10px 0; font-family: 'Work Sans', sans-serif; font-size: 12px; line-height: 18px; color: #8A99A8;">
                                No. 234 AMAC, Lagos State, Nigeria.
                            </p>
                            <p
                                style="margin: 0 0 20px 0; font-family: 'Work Sans', sans-serif; font-size: 12px; color: #8A99A8;">
                                <a href="http://APP.app/report"
                                    style="color: #8A99A8; text-decoration: underline;">Privacy
                                    Policy</a>
                                &nbsp;&bull;&nbsp; <a href="https://APP-staging.vercel.app/terms-of-service"
                                    style="color: #8A99A8; text-decoration: underline;">Terms of Service</a>
                            </p>
                            <p
                                style="margin: 0; font-family: 'Work Sans', sans-serif; font-size: 12px; color: #B2C0CC; font-weight: 500;">
                                &copy; 2026 APP. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            <td>
        <tr>
    </table>
    <!-- /Wrapper Table -->

</body>

</html>
