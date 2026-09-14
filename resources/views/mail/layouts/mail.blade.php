{{-- <div>
    <!-- Simplicity is an acquired taste. - Katharine Gerould -->
</div> --}}

{{-- APP
{
  primary: "#0766AD",
  secondary: "#313131",
} --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        APP has a clear mission: to
        dismantle the transparency barriers that
        keep exceptional African enterprises from
        easily accessing capital, banking
        facilities, and high-value corporate
        partnerships. By translating your dynamic
        business data into a trusted health and
        credit-readiness score, you are taking the
        single most powerful step toward making your
        business universally investable.
    </title>
    <style>
        /* General Body Styles */
        body {
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        /* Container */
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        /* Header */
        .header {
            /* Primary brand color */
            /* background-color: #4caf50;  */
            /* background-color: rgb(5, 35, 62); */
            background-color: #0766AD;
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }

        .header .logo img {
            /* width: 70px;
            height: 70px; */
            width: fit-content;
            height: auto;
            max-width: 100%;
            object-fit: contain;
            margin: 0 auto;
            padding: 18px;
            text-align: center;
        }

        /* .header .logo img{
                width: 100%;
                height: 100%;
                text-align: center;
            } */
        .header h1 {
            margin: 0;
            font-size: 32px;
            font-weight: 700;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 18px;
            opacity: 0.9;
            color: #ffffff;
        }

        /* Main */
        .main {
            padding: 30px 0px 10px;
        }

        /* Content Area */
        .content {
            color: #333333;
            line-height: 1.6;
            font-size: 16px;
            padding: 0 30px;
        }


        .content h2 {
            color: #2c3e50;
            font-size: 24px;
            margin-top: 0;
            margin-bottom: 20px;
        }

        .content p {
            margin-bottom: 15px;
        }

        .content a {
            /* color: #4caf50; */
            color: rgb(5, 35, 62);
            text-decoration: none;
            font-weight: bold;
        }

        /* Call to Action Button */
        .button-container {
            text-align: center;
            padding: 20px 30px;
        }

        .button {
            display: inline-block;
            /* background-color: #4caf50; */
            /* background-color: rgb(5, 35, 62); */
            background-color: #0766AD;
            color: #ffffff;
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
        }

        /* Footer */
        .footer {
            /* background-color: #333333; */
            /* background-color: rgb(5, 35, 62); */
            background-color: #0766AD;
            color: #cccccc;
            text-align: center;
            padding: 20px;
            font-size: 14px;
        }

        .footer p {
            margin: 0;
        }

        .footer a {
            color: #4caf50;
            text-decoration: none;
        }

        /* Responsive Styles */
        @media only screen and (max-width: 620px) {
            .email-container {
                margin: 0 !important;
                border-radius: 0 !important;
            }

            .header,
            .content,
            .footer {
                padding: 20px !important;
            }

            .header h1 {
                font-size: 28px !important;
            }

            .header p {
                font-size: 16px !important;
            }

            .content h2 {
                font-size: 20px !important;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">

        <!-- Header -->
        <div class="header">
            <div class="logo">
                {{-- logo from public path --}}
                <img src="{{ asset('/brands/app-logo.png') }}" alt="APP Brand Logo" />
            </div>
            <p>
                APP helps Nigerian businesses build a trusted, verifiable financial identity so lenders and
                investors can confidently say yes. We translate your fragmented business activities into a clear
                readiness profile that institutions trust.
            </p>
        </div>

        <!-- Main Content -->
        <div class="main">
            <div class="content">

                {{-- Yield content from other page --}}
                @yield('content')


                <!-- signature -->
                <p>
                    Sincerely,<br />
                    APP
                </p>
            </div>
        </div>
        <!-- End of main content -->

        <hr />

        <!-- Outro -->
        <div class="content">
            <p>For more details</p>
            <p>
                visit our website:
                <a href="{{ config('app.frontend_url') ?? 'https://APP.app' }}" target="_blank">
                    {{ config('app.frontend_url') ?? 'https://APP.app' }}
                </a>
            </p>
            <p>
                send us a message:
                <a href="mailto:info@APP.app" target="_blank">info@APP.app</a>
            </p>
        </div>


        <!-- Footer -->
        <div class="footer">
            <p>No. 234 AMAC, Lagos State, Nigeria.</p>
            <p><a href="{{ config('app.frontend_url') ?? 'https://APP.app' }}/#unsubscribe"
                    target="_blank">Unsubscribe</a> |
                <a href="{{ config('app.frontend_url') ?? 'https://APP.app' }}/terms-of-service"
                    target="_blank">Terms of
                    service</a>
            </p>
            <p>&copy; {{ date('Y') }} APP. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
