<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        .card-custom {
            max-width: 450px;
            margin: 80px auto;
            padding: 30px;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .password-wrapper {
            position: relative;
        }
        .password-wrapper input {
            padding-right: 45px; /* Space for the eye icon */
        }
        .password-toggle {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 18px;
            color: #666;
        }
        .btn-primary-custom {
            background: #0056ff;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 500;
        }
        .btn-primary-custom:hover {
            background: #0044cc;
        }
    </style>
</head>
<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="card card-custom">

            <!-- Back Button -->
            <a href="javascript:history.back()" class="text-decoration-none text-primary mb-3 d-inline-flex align-items-center">
                <i class="bi bi-chevron-left me-1"></i> Back
            </a>

            <!-- Title -->
            <h3 class="fw-bold mb-1">Create New Password</h3>
            <p class="text-muted mb-4" style="font-size: 14px;">Set a strong password for your account</p>

            <!-- Reset Password Form -->
            <form method="POST" action="{{ route('reset_password') }}">
                @csrf

                <!-- New Password -->
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" class="form-control" name="password" required placeholder="Enter your password">
                        <i class="bi bi-eye password-toggle" onclick="togglePassword('password', this)"></i>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="mb-4">
                    <label class="form-label">Confirm Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password_confirmation" class="form-control" name="password_confirmation" required placeholder="Confirm your password">
                        <i class="bi bi-eye password-toggle" onclick="togglePassword('password_confirmation', this)"></i>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary-custom w-100">Set Password</button>
            </form>

        </div>
    </div>

    <script>
        function togglePassword(fieldId, icon) {
            const field = document.getElementById(fieldId);
            if (field.type === "password") {
                field.type = "text";
                icon.classList.replace("bi-eye", "bi-eye-slash");
            } else {
                (field.type = "password");
                icon.classList.replace("bi-eye-slash", "bi-eye");
            }
        }
    </script>

</body>
</html>
