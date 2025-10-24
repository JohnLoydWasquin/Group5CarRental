<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-custom {
            max-width: 400px;
            margin: 80px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 22px;
            margin: 0 5px;
        }
    </style>
</head>
<body>
    <div class="card card-custom">
        <a href="javascript:history.back()" class="text-decoration-none text-primary mb-3 d-inline-flex align-items-center">
                <i class="bi bi-chevron-left me-1"></i> Back
        </a>
        <h3 class="text-center mb-3">Enter Verification Code</h3>
        <p class="text-center text-muted mb-4">We sent a 6-digit code to your phone</p>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('verify_otp') }}">
            @csrf
            <div class="d-flex justify-content-center mb-3">
                <input type="text" maxlength="1" class="form-control otp-input" name="otp[]" required>
                <input type="text" maxlength="1" class="form-control otp-input" name="otp[]" required>
                <input type="text" maxlength="1" class="form-control otp-input" name="otp[]" required>
                <input type="text" maxlength="1" class="form-control otp-input" name="otp[]" required>
                <input type="text" maxlength="1" class="form-control otp-input" name="otp[]" required>
                <input type="text" maxlength="1" class="form-control otp-input" name="otp[]" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Verify</button>
        </form>
    </div>

    <script>
        // Auto move to next input
        const inputs = document.querySelectorAll('.otp-input');
        inputs.forEach((input, index) => {
            input.addEventListener('input', () => {
                if (input.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });
            input.addEventListener('keydown', (e) => {
                if (e.key === "Backspace" && index > 0 && !input.value) {
                    inputs[index - 1].focus();
                }
            });
        });
    </script>
</body>
</html>
