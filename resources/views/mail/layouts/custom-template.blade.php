{{--
<div>
    <!-- Nothing in life is to be feared, it is only to be understood. Now is the time to understand more, so that we may fear less. - Maria Skłodowska-Curie -->
</div>
--}}

<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>
            VeriScore has a clear mission: to dismantle the transparency
            barriers that keep exceptional African enterprises from easily
            accessing capital, banking facilities, and high-value corporate
            partnerships. By translating your dynamic business data into a
            trusted health and credit-readiness score, you are taking the single
            most powerful step toward making your business universally
            investable.
        </title>
        <!-- Import Work Sans Font -->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700;800&display=swap"
            rel="stylesheet"
        />

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

            .cta-button:hover {
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
                    padding: 32px 20px 24px 20px !important;
                }

                .banner-padding {
                    padding: 16px 20px !important;
                }

                .step-box {
                    padding: 16px !important;
                }

                .footer-padding {
                    padding: 32px 20px !important;
                }
            }
        </style>
    </head>

    <body>
        <!-- Wrapper Table -->
        <table
            border="0"
            cellpadding="0"
            cellspacing="0"
            width="100%"
            class="wrapper-padding"
            style="background-color: #f4f7f6; padding: 48px 16px"
        >
            <tr>
                <td align="center" valign="top">
                    <!-- Main Card Container -->
                    <table
                        border="0"
                        cellpadding="0"
                        cellspacing="0"
                        width="100%"
                        style="
                            max-width: 560px;
                            background-color: #ffffff;
                            border-radius: 16px;
                            overflow: hidden;
                            box-shadow: 0 10px 25px rgba(7, 102, 173, 0.05);
                            border: 1px solid #e1e8ed;
                        "
                    >
                        <!-- Branding Header -->
                        <tr>
                            <td
                                align="center"
                                class="header-padding"
                                style="padding: 28px"
                            >
                                <img
                                    style="width: 200px"
                                    src="{{ asset('/brands/app-logo.png') }}"
                                    alt="VeriScore Brand Logo"
                                />
                            </td>
                        </tr>

                        <!-- Email Body -->
                        <tr>
                            <td
                                class="card-padding"
                                style="
                                    padding: 0 40px 24px 40px;
                                    text-align: left;
                                "
                            >
                                <p
                                    style="
                                        margin: 0 0 24px 0;
                                        font-family:
                                            &quot;Work Sans&quot;, sans-serif;
                                        font-size: 15px;
                                        line-height: 26px;
                                        color: #4a5560;
                                        font-weight: 400;
                                    "
                                >
                                    We built VeriScore with a clear mission: to
                                    dismantle the transparency barriers that
                                    keep exceptional African enterprises from
                                    easily accessing capital, banking
                                    facilities, and high-value corporate
                                    partnerships. By translating your dynamic
                                    business data into a trusted health and
                                    credit-readiness score, you are taking the
                                    single most powerful step toward making your
                                    business universally investable.
                                </p>

                                <h2
                                    style="
                                        margin: 0 0 18px 0;
                                        font-family:
                                            &quot;Work Sans&quot;, sans-serif;
                                        font-size: 20px;
                                        font-weight: 600;
                                        color: #1e2a38;
                                        letter-spacing: -0.3px;
                                        padding: 44px 40px 28px 40px;
                                    "
                                >
                                    {{ $user ? 'Dear ' . $user?->name : 'Hello
                                    Dear' }},
                                </h2>
                            </td>
                        </tr>

                        {{-- ?????????????????????? START YOUR MESSAGE INTRO
                        ?????????????????????? --}}

                        <!-- Main Message -->
                        <tr>
                            <td
                                class="card-padding"
                                style="
                                    padding: 0 40px 40px 40px;
                                    text-align: left;
                                "
                            >
                                <p
                                    style="
                                        margin: 0 0 24px 0;
                                        font-family:
                                            &quot;Work Sans&quot;, sans-serif;
                                        font-size: 15px;
                                        line-height: 26px;
                                        color: #4a5560;
                                    "
                                >
                                    Our entire team is here to support your
                                    growth. If you ever have feedback,
                                    questions, or just want to share your
                                    business goals, please don't hesitate to hit
                                    reply to this email. We read every single
                                    message.
                                </p>

                                <hr
                                    style="
                                        border: 0;
                                        border-top: 1px solid #e1e8ed;
                                        margin: 40px 0 0 0;
                                    "
                                />
                            </td>
                        </tr>

                        <!-- CTA Button Section -->
                        <tr>
                            <td
                                align="center"
                                class="card-padding"
                                style="padding: 10px 40px 32px 40px"
                            >
                                <table
                                    border="0"
                                    cellpadding="0"
                                    cellspacing="0"
                                >
                                    <tr>
                                        <td
                                            align="center"
                                            style="
                                                background-color: #0766ad;
                                                border-radius: 8px;
                                            "
                                        >
                                            <a
                                                href="{{ config('app.frontend_url') }}/dashboard"
                                                target="_blank"
                                                class="cta-button"
                                                style="
                                                    display: inline-block;
                                                    font-family:
                                                        &quot;Work Sans&quot;,
                                                        sans-serif;
                                                    font-size: 15px;
                                                    font-weight: 600;
                                                    color: #ffffff;
                                                    text-decoration: none;
                                                    padding: 14px 32px;
                                                    border-radius: 8px;
                                                    transition: background-color
                                                        0.2s ease;
                                                "
                                            >
                                                Access Your Dashboard
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        {{-- Yield content from other page --}}
                        @yield('content') {{-- ?????????????????????? END OF
                        MESSAGE CONTENT ?????????????????????? --}}

                        <!-- Closing Remarks -->
                        <tr>
                            <td
                                class="card-padding"
                                style="
                                    padding: 0 40px 40px 40px;
                                    text-align: left;
                                "
                            >
                                <p
                                    style="
                                        margin: 0 0 24px 0;
                                        font-family:
                                            &quot;Work Sans&quot;, sans-serif;
                                        font-size: 15px;
                                        line-height: 26px;
                                        color: #4a5560;
                                    "
                                >
                                    Our entire team is here to support your
                                    growth. If you ever have feedback,
                                    questions, or just want to share your
                                    business goals, please don't hesitate to hit
                                    reply to this email. We read every single
                                    message.
                                </p>
                                <p
                                    style="
                                        margin: 0 0 4px 0;
                                        font-family:
                                            &quot;Work Sans&quot;, sans-serif;
                                        font-size: 15px;
                                        line-height: 24px;
                                        color: #4a5560;
                                        font-style: italic;
                                    "
                                >
                                    To your business success,
                                </p>
                                <!-- Team Info Placeholder -->
                                <p
                                    style="
                                        margin: 0;
                                        font-family:
                                            &quot;Work Sans&quot;, sans-serif;
                                        font-size: 15px;
                                        line-height: 24px;
                                        color: #1e2a38;
                                        font-weight: 600;
                                    "
                                >
                                    Management Team<br />
                                    <span
                                        style="
                                            font-size: 13px;
                                            color: #607274;
                                            font-weight: 500;
                                        "
                                        >VeriScore</span
                                    >
                                </p>

                                <hr
                                    style="
                                        border: 0;
                                        border-top: 1px solid #e1e8ed;
                                        margin: 40px 0 0 0;
                                    "
                                />
                            </td>
                        </tr>

                        <!-- Contact & Meta Links -->
                        <tr>
                            <td
                                class="card-padding"
                                style="
                                    padding: 0 40px 36px 40px;
                                    text-align: left;
                                "
                            >
                                <p
                                    style="
                                        margin: 0;
                                        font-family:
                                            &quot;Work Sans&quot;, sans-serif;
                                        font-size: 13px;
                                        line-height: 20px;
                                        color: #607274;
                                    "
                                >
                                    <strong style="color: #1e2a38"
                                        >Need absolute help setting up?</strong
                                    >
                                    Reach out to us
                                    <a
                                        href="mailto:info@veriscore.app"
                                        class="btn-link"
                                        style="
                                            color: #0766ad;
                                            text-decoration: none;
                                            font-weight: 600;
                                        "
                                        >here</a
                                    >.
                                </p>
                            </td>
                        </tr>

                        <!-- Footer Details -->
                        <tr>
                            <td
                                class="footer-padding"
                                style="
                                    padding: 36px 40px;
                                    background-color: #f8fafc;
                                    border-top: 1px solid #e1e8ed;
                                    text-align: center;
                                "
                            >
                                <p
                                    style="
                                        margin: 0 0 10px 0;
                                        font-family:
                                            &quot;Work Sans&quot;, sans-serif;
                                        font-size: 12px;
                                        line-height: 18px;
                                        color: #8a99a8;
                                    "
                                >
                                    No. 234 AMAC, Lagos State, Nigeria.
                                </p>
                                <p
                                    style="
                                        margin: 0 0 20px 0;
                                        font-family:
                                            &quot;Work Sans&quot;, sans-serif;
                                        font-size: 12px;
                                        color: #8a99a8;
                                    "
                                >
                                    <a
                                        href="{{ config('app.frontend_url') }}/privacy-policy"
                                        style="
                                            color: #8a99a8;
                                            text-decoration: underline;
                                        "
                                        >Privacy Policy</a
                                    >
                                    &nbsp;&bull;&nbsp;
                                    <a
                                        href="{{ config('app.frontend_url') }}/terms-of-service"
                                        style="
                                            color: #8a99a8;
                                            text-decoration: underline;
                                        "
                                        >Terms of Service</a
                                    >
                                </p>
                                <p
                                    style="
                                        margin: 0;
                                        font-family:
                                            &quot;Work Sans&quot;, sans-serif;
                                        font-size: 12px;
                                        color: #b2c0cc;
                                        font-weight: 500;
                                    "
                                >
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
