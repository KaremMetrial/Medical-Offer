<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ __('message.payment_success') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&family=Noto+Kufi+Arabic:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --success-color: #00BFA5;
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
            overflow-x: hidden;
        }

        .container {
            width: 100%;
            max-width: 400px;
            padding: 24px;
            text-align: center;
        }

        .success-card {
            background: var(--card-bg);
            border-radius: 32px;
            padding: 40px 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }

        .icon-wrapper {
            width: 88px;
            height: 88px;
            background: rgba(0, 191, 165, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            animation: scaleIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .icon-wrapper svg {
            width: 48px;
            height: 48px;
            color: var(--success-color);
        }

        h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 8px;
            color: var(--text-main);
        }

        p.subtitle {
            font-size: 14px;
            color: var(--text-secondary);
            margin: 0 0 32px;
        }

        .receipt-container {
            background: #F1F5F9;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 32px;
            text-align: left;
        }

        [dir="rtl"] .receipt-container {
            text-align: right;
        }

        .receipt-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .receipt-row:last-child {
            margin-bottom: 0;
            padding-top: 12px;
            border-top: 1px dashed #CBD5E1;
            font-weight: 700;
            color: var(--primary-color);
        }

        .receipt-label {
            color: var(--text-secondary);
        }

        .receipt-value {
            color: var(--text-main);
            font-weight: 600;
        }

        .btn-primary {
            display: block;
            width: 100%;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            padding: 18px;
            border-radius: 16px;
            font-weight: 700;
            font-size: 16px;
            transition: transform 0.2s;
            box-shadow: 0 8px 24px rgba(0, 138, 184, 0.2);
        }

        .btn-primary:active {
            transform: scale(0.98);
        }

        .footer-text {
            margin-top: 24px;
            font-size: 12px;
            color: var(--text-secondary);
        }

        @keyframes scaleIn {
            from { transform: scale(0); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        /* Auto-redirect loading bar */
        .loading-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 4px;
            background: var(--success-color);
            width: 0%;
            transition: width 3s linear;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-card">
            <div class="icon-wrapper">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            
            <h1>{{ __('message.payment_success_title') }}</h1>
            <p class="subtitle">{{ __('message.payment_success_desc') }}</p>

            <div class="receipt-container">
                <div class="receipt-row">
                    <span class="receipt-label">{{ __('message.transaction_id') }}</span>
                    <span class="receipt-value">#{{ $payment_id }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">{{ __('message.date') }}</span>
                    <span class="receipt-value">{{ now()->format('Y-m-d H:i') }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">{{ __('message.amount_paid') }}</span>
                    <span class="receipt-value">{{ $amount }} {{ $currency }}</span>
                </div>
            </div>

            <a href="medicaloffer://payment-result?status=success&id={{ $payment_id }}" class="btn-primary" id="returnBtn">
                {{ __('message.return_to_app') }}
            </a>

            <div class="loading-bar" id="loadBar"></div>
        </div>

        <p class="footer-text">{{ __('message.automatic_redirect_notice') }}</p>
    </div>

    <script>
        // Start loading bar for visual cue
        setTimeout(() => {
            document.getElementById('loadBar').style.width = '100%';
        }, 100);

        // Auto-redirect to App after 3 seconds
        setTimeout(() => {
            window.location.href = "medicaloffer://payment-result?status=success&id={{ $payment_id }}";
        }, 3200);
    </script>
</body>
</html>
