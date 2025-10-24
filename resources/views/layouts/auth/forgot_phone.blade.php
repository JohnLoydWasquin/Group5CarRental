<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Phone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        .card-custom {
            max-width: 400px;
            margin: 80px auto;
            margin-bottom: 300px;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-light mb-3">

    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="card card-custom">
            
            <h3 class="text-center mb-2">Enter Your Phone Number</h3>
            <p class="text-center text-muted mb-4">We will send you a verification code via SMS.</p>

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('forgot_sendOtp') }}">
                @csrf
                
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-telephone text-secondary fs-5"></i>
                        </span>
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="09XXXXXXXXX" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">Send OTP</button>
            </form>
        </div>
    </div>

</body>
</html>
