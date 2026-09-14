{{-- <div>
    <!-- Nothing in life is to be feared, it is only to be understood. Now is the time to understand more, so that we may fear less. - Maria Skłodowska-Curie -->
</div> --}}
@extends('mail.layouts.wrapper')

@section('content')
    <!-- Email Body -->
    <tr>
        <td class="card-padding"
            style="
                                    padding: 0 40px 24px 40px;
                                    text-align: left;
                                ">
            <h2
                style="
                                        margin: 0 0 18px 0;
                                        font-family:
                                            &quot;Work Sans&quot;, sans-serif;
                                        font-size: 20px;
                                        font-weight: 600;
                                        color: #1e2a38;
                                        letter-spacing: -0.3px;
                                    ">
                {{ $user ? 'Dear ' . $user?->name : 'Hello Dear' }},
            </h2>


            {{-- ??????????????????????  START YOUR MESSAGE INTRO ??????????????????????  --}}
            <p
                style="
                                        margin: 0 0 16px 0;
                                        font-family:
                                            &quot;Work Sans&quot;, sans-serif;
                                        font-size: 15px;
                                        line-height: 26px;
                                        color: #4a5560;
                                        font-weight: 400;
                                    ">
                I wanted to personally reach out and welcome
                you to VeriScore. We are incredibly excited
                to have you on board.
            </p>

            <p
                style="
                                        margin: 0 0 24px 0;
                                        font-family:
                                            &quot;Work Sans&quot;, sans-serif;
                                        font-size: 15px;
                                        line-height: 26px;
                                        color: #4a5560;
                                        font-weight: 400;
                                    ">
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

            <!-- Highlight Section / Next Steps Header -->
            <h3
                style="
                                        margin: 0 0 12px 0;
                                        font-family:
                                            &quot;Work Sans&quot;, sans-serif;
                                        font-size: 16px;
                                        font-weight: 600;
                                        color: #1e2a38;
                                        line-height: 25px;
                                    ">
                To get the most out of your dashboard today,
                I recommend starting here:
            </h3>
        </td>
    </tr>

    <!-- Step 1 & 2 Box -->
    <tr>
        <td class="card-padding" style="padding: 0 40px 28px 40px">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td class="step-box"
                        style="
                                                background-color: #f8fafc;
                                                border-left: 4px solid #0766ad;
                                                border-radius: 0 12px 12px 0;
                                                padding: 20px;
                                                margin-bottom: 12px;
                                            ">
                        <p
                            style="
                                                    margin: 0 0 4px 0;
                                                    font-family:
                                                        &quot;Work Sans&quot;,
                                                        sans-serif;
                                                    font-size: 14px;
                                                    font-weight: 700;
                                                    color: #0766ad;
                                                    text-transform: uppercase;
                                                    letter-spacing: 0.5px;
                                                ">
                            Step 1
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
                            <strong>Unlock Premium Growth Tools:</strong>
                            Select a flexible plan or
                            purchase a feature bundle to
                            instantly access the advanced
                            diagnostic tools, deep
                            analytics, and premium
                            optimization strategies needed
                            to maximize your score.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td
                        style="
                                                height: 12px;
                                                line-height: 12px;
                                                font-size: 12px;
                                            ">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td class="step-box"
                        style="
                                                background-color: #f8fafc;
                                                border-left: 4px solid #0766ad;
                                                border-radius: 0 12px 12px 0;
                                                padding: 20px;
                                            ">
                        <p
                            style="
                                                    margin: 0 0 4px 0;
                                                    font-family:
                                                        &quot;Work Sans&quot;,
                                                        sans-serif;
                                                    font-size: 14px;
                                                    font-weight: 700;
                                                    color: #0766ad;
                                                    text-transform: uppercase;
                                                    letter-spacing: 0.5px;
                                                ">
                            Step 2
                        </p>
                        <p
                            style='margin: 0;
                                                    font-family:
                                                        "Work Sans",
                                                        sans-serif;
                                                    font-size: 14px;
                                                    line-height: 22px;
                                                    color: #4a5560;
                                                '>
                            <strong>Upload your business documents:</strong>
                            Securely upload your corporate
                            documentation to instantly
                            fast-track our verification
                            process and activate your
                            scoring dashboard.
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- CTA Button Section -->
    <tr>
        <td align="center" class="card-padding" style="padding: 10px 40px 32px 40px">
            <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center"
                        style="
                                                background-color: #0766ad;
                                                border-radius: 8px;
                                            ">
                        <a href="{{ config('app.frontend_url') }}/dashboard" target="_blank" class="cta-button"
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
                                                ">
                            Access Your Dashboard
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>


    {{-- ?????????????????????? END OF MESSAGE CONTENT ??????????????????????  --}}

    <!-- Closing Remarks -->
    <tr>
        <td class="card-padding"
            style="
                                    padding: 0 40px 40px 40px;
                                    text-align: left;
                                ">
            <p
                style="
                                        margin: 0 0 24px 0;
                                        font-family:
                                            &quot;Work Sans&quot;, sans-serif;
                                        font-size: 15px;
                                        line-height: 26px;
                                        color: #4a5560;
                                    ">
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
                                    ">
                To your business success,
            </p>
            <!-- CEO Profile Info Placeholder -->
            <p
                style="
                                        margin: 0;
                                        font-family:
                                            &quot;Work Sans&quot;, sans-serif;
                                        font-size: 15px;
                                        line-height: 24px;
                                        color: #1e2a38;
                                        font-weight: 600;
                                    ">
                Segun Oyenuga<br />
                <span
                    style="
                                            font-size: 13px;
                                            color: #607274;
                                            font-weight: 500;
                                        ">Founder
                    & CEO, VeriScore</span>
            </p>

            <hr
                style="
                                        border: 0;
                                        border-top: 1px solid #e1e8ed;
                                        margin: 40px 0 0 0;
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
                <strong style="color: #1e2a38">Need absolute help setting up?</strong>
                Reach out to us
                <a href="mailto:info@veriscore.app" class="btn-link"
                    style="
                                            color: #0766ad;
                                            text-decoration: none;
                                            font-weight: 600;
                                        ">here</a>.
            </p>
        </td>
    </tr>
@endsection
