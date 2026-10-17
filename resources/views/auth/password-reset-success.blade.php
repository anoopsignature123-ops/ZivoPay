<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <meta name="referrer" content="no-referrer">
    <title>Password Reset Successful - ZIVO PAY</title>
    <style>
        * {
            box-sizing: border-box
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            background: radial-gradient(circle at top, #064e3b 0, #031a12 42%, #020b08 100%);
            color: #e2e8f0;
            font-family: Arial, sans-serif
        }

        .backdrop {
            position: fixed;
            inset: 0;
            display: grid;
            place-items: center;
            padding: 24px;
            background: rgba(0, 8, 5, .72);
            backdrop-filter: blur(5px)
        }

        .modal {
            width: min(100%, 460px);
            padding: 34px;
            border: 1px solid rgba(16, 185, 129, .45);
            border-radius: 22px;
            background: linear-gradient(145deg, rgba(5, 50, 31, .98), rgba(2, 20, 13, .98));
            box-shadow: 0 24px 80px #0009;
            text-align: center
        }

        .icon {
            width: 68px;
            height: 68px;
            margin: 0 auto 20px;
            display: grid;
            place-items: center;
            border: 1px solid #34d39966;
            border-radius: 50%;
            background: #10b98120;
            color: #6ee7b7;
            font-size: 34px
        }

        .brand {
            margin: 0;
            color: #34d399;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 3px
        }

        .modal h1 {
            margin: 12px 0;
            color: #fff;
            font-size: 26px
        }

        .message {
            margin: 0;
            color: #b4c8be;
            font-size: 15px;
            line-height: 1.7
        }

        .button {
            display: block;
            margin-top: 26px;
            padding: 14px 18px;
            border-radius: 10px;
            background: #10b981;
            color: #002116;
            font-size: 15px;
            font-weight: 800;
            text-decoration: none;
            transition: background .2s
        }

        .button:hover {
            background: #34d399
        }

        .note {
            margin: 16px 0 0;
            color: #82968c;
            font-size: 12px
        }
    </style>
</head>

<body>
    <main class="backdrop" role="dialog" aria-modal="true" aria-labelledby="success-title">
        <section class="modal">
            <div class="icon" aria-hidden="true">✓</div>
            <p class="brand">ZIVO PAY</p>
            <h1 id="success-title">Password reset successful</h1>
            <p class="message">Your password has been reset successfully. You can now log in to your account using your
                new password.</p>
            {{-- <a class="button" href="{{ route('user.login') }}">Go to Login</a> --}}
            <p class="note">Please use your new password the next time you sign in.</p>
        </section>
    </main>
</body>

</html>