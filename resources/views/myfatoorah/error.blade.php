<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ __('message.payment_failed') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&family=Noto+Kufi+Arabic:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --error-color: #EF4444;
            --primary-color: #008AB8;
            --bg-color: #F8FAFC;
            --text-main: #1E293B;
            --text-secondary: #64748B;
            --card-bg: #FFFFFF;
        }

        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Outfit', 'Noto Kufi Arabic', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 400px;
            padding: 24px;
            text-align: center;
        }

        .error-card {
            background: var(--card-bg);
            border-radius: 32px;
            padding: 40px 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
        }

        .icon-wrapper {
            width: 88px;
            height: 88px;
            background: rgba(239, 68, 68, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            animation: bounceIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .icon-wrapper svg {
            width: 48px;
            height: 48px;
            color: var(--error-color);
        }

        h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 12px;
            color: var(--text-main);
        }

        .error-message {
            background: #FEF2F2;
            color: #991B1B;
            border-radius: 16px;
            padding: 16px;
            font-size: 14px;
            margin-bottom: 32px;
            border: 1px solid #FEE2E2;
            line-height: 1.5;
        }

        .action-btns {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
            padding: 18px;
            border-radius: 16px;
            font-weight: 700;
            font-size: 16px;
            text-decoration: none;
            transition: transform 0.2s;
            box-shadow: 0 8px 24px rgba(0, 138, 184, 0.2);
        }

        .btn-secondary {
            background: #F1F5F9;
            color: var(--text-main);
            padding: 16px;
            border-radius: 16px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: background 0.2s;
        }

        .btn-primary:active, .btn-secondary:active {
            transform: scale(0.98);
        }

        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-card">
            <div class="icon-wrapper">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </div>
            
            <h1>{{ __('message.payment_failed_title') }}</h1>
            
            <div class="error-message">
                {{ $message ?? __('message.generic_payment_error') }}
            </div>

            <div class="action-btns">
                <a href="medicaloffer://payment-result?status=failure&message={{ urlencode($message) }}" class="btn-primary">
                    {{ __('message.return_to_app') }}
                </a>
                <a href="javascript:window.location.reload();" class="btn-secondary">
                    {{ __('message.retry_payment') }}
                </a>
            </div>
        </div>
    </div>

    <script>
        // Auto-redirect to App after 5 seconds if no action taken
        setTimeout(() => {
            window.location.href = "medicaloffer://payment-result?status=failure&message={{ urlencode($message) }}";
        }, 8000);
    </script>
</body>
</html>
