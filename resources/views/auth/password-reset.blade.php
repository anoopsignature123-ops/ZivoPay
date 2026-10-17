<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <meta name="referrer" content="no-referrer">
    <title>Reset Password - ZIVO PAY</title>
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

        .card {
            width: min(100%, 460px);
            padding: 32px;
            border: 1px solid rgba(16, 185, 129, .35);
            border-radius: 22px;
            background: rgba(4, 39, 24, .94);
            box-shadow: 0 24px 70px #0008
        }

        .brand {
            margin: 0;
            color: #34d399;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 3px;
            text-transform: uppercase
        }

        .card h1 {
            margin: 12px 0 8px;
            color: #fff;
            font-size: 27px
        }

        .intro {
            margin: 0 0 24px;
            color: #a7b9b0;
            font-size: 14px;
            line-height: 1.6
        }

        label {
            display: block;
            margin: 16px 0 7px;
            color: #a7f3d0;
            font-size: 12px;
            font-weight: 700
        }

        .password-wrap {
            position: relative
        }

        .password-wrap input {
            padding-right: 48px
        }

        .password-wrap input {
            width: 100%;
            padding: 13px 48px 13px 14px;
            border: 1px solid rgba(16, 185, 129, .35);
            border-radius: 10px;
            background: #01140c;
            color: #fff;
            font-size: 14px;
            outline: none
        }

        .password-wrap input:focus {
            border-color: #34d399;
            box-shadow: 0 0 0 3px #10b98122
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 9px;
            display: grid;
            width: 32px;
            height: 32px;
            place-items: center;
            transform: translateY(-50%);
            border: 0;
            border-radius: 7px;
            background: transparent;
            color: #8fb5a4;
            cursor: pointer
        }

        .toggle-password:hover,
        .toggle-password:focus-visible {
            background: #10b98120;
            color: #6ee7b7;
            outline: none
        }

        .toggle-password svg {
            width: 19px;
            height: 19px
        }

        .button {
            width: 100%;
            margin-top: 22px;
            padding: 14px;
            border: 0;
            border-radius: 10px;
            background: #10b981;
            color: #00140c;
            font-weight: 800;
            font-size: 14px;
            cursor: pointer
        }

        .button:hover {
            background: #34d399
        }

        .error {
            margin: 12px 0;
            padding: 12px;
            border: 1px solid #fb718566;
            border-radius: 10px;
            background: #7f1d1d33;
            color: #fecdd3;
            font-size: 13px
        }

        .help {
            margin-top: 18px;
            color: #82968c;
            font-size: 12px;
            text-align: center
        }

        .help a {
            color: #6ee7b7
        }

        .invalid {
            padding: 15px;
            border: 1px solid #f59e0b55;
            border-radius: 12px;
            background: #78350f33;
            color: #fde68a;
            font-size: 14px;
            line-height: 1.6
        }
    </style>
</head>

<body>
    <main class="card">
        <p class="brand">ZIVO PAY</p>
        <h1>Reset your password</h1>
        <p class="intro">Choose a new password for your account. For your security, this reset link expires after five
            minutes and can be used only once.</p>

        @if($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        @if($validToken)
            <form method="POST" action="{{ route('password.reset.submit') }}">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <input type="hidden" name="token" value="{{ $token }}">
                <label for="password">New password</label>
                <div class="password-wrap">
                    <input id="password" type="password" name="password" required minlength="8" autocomplete="new-password"
                        placeholder="At least 8 characters">
                    <button class="toggle-password" type="button" data-password-toggle="password" aria-label="Show password"
                        aria-pressed="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                            stroke-linejoin="round" aria-hidden="true">
                            <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </button>
                </div>
                <label for="password_confirmation">Confirm new password</label>
                <div class="password-wrap">
                    <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8"
                        autocomplete="new-password" placeholder="Enter the new password again">
                    <button class="toggle-password" type="button" data-password-toggle="password_confirmation"
                        aria-label="Show password" aria-pressed="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                            stroke-linejoin="round" aria-hidden="true">
                            <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </button>
                </div>
                <button class="button" type="submit">Save new password</button>
            </form>
        @else
            <div class="invalid">This reset link is invalid, expired, or already used. Request a fresh password reset link
                from the app.</div>
        @endif

        <p class="help">If you need help, contact <a href="mailto:support@zivopay.com">support@zivopay.com</a>.</p>
    </main>
    <script>
        document.querySelectorAll('[data-password-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                const input = document.getElementById(button.dataset.passwordToggle);
                const shouldShow = input.type === 'password';
                input.type = shouldShow ? 'text' : 'password';
                button.setAttribute('aria-label', shouldShow ? 'Hide password' : 'Show password');
                button.setAttribute('aria-pressed', String(shouldShow));
            });
        });
    </script>
</body>

</html>