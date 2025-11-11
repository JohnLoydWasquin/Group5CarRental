<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>RideNow | Login</title>

  {{-- Bootstrap CDN --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    body {
      background: url('{{ asset("images/bg.jpg") }}') center center / cover no-repeat;
      min-height: 100vh;
    }
    .overlay {
      background: rgba(0,0,0,0.45);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
    }
    .card-login {
      width: 100%;
      max-width: 480px;
      border-radius: 12px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.3);
      background: #ffffff;
    }
    .form-control:focus {
      box-shadow: none;
      border-color: #0d6efd;
    }
    @media (max-width: 540px) {
      .overlay { padding: 1rem; }
      .card-login { padding: 1rem; }
    }
  </style>
</head>
<body>
  <div class="overlay">
    <div class="card card-login p-4">
      <div class="px-3 py-2">
        <h3 class="text-center mb-1">Welcome Back</h3>
        <p class="text-center text-muted small mb-4">Login to your account</p>

        {{-- Error Message --}}
        @if(session('error'))
          <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
          @csrf

          {{-- Email --}}
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email', $rememberedEmail) }}" class="form-control @error('email') is-invalid @enderror" placeholder="name@example.com" required>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- Password --}}
          <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group">
                <input type="password" id="password" name="password" value="{{ old('password', $rememberedPassword) }}" class="form-control @error('password') is-invalid @enderror" placeholder="Enter your password" required>
                <span class="input-group-text" onclick="togglePassword('password', this)" style="cursor: pointer;">
                    <i class="fa-solid fa-eye"></i>
                </span>
            </div>
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- Remember Me & Forgot Password --}}
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <input type="checkbox" name="remember" {{ request()->cookie('remember_email') ? 'checked' : '' }}> <small>Remember Me</small>
            </div>
            <a href="{{ route('forgot_phone') }}" class="text-decoration-none small">Forgot Password?</a>
          </div>

          <button class="btn btn-primary w-100 mt-2" type="submit">Login</button>

          <p class="text-center small text-muted mt-3 mb-0">
            Don't have an account? <a href="{{ route('register') }}">Sign up</a>
          </p>
        </form>
      </div>
    </div>
  </div>

  <script>
    function togglePassword(fieldId, element) {
      const input = document.getElementById(fieldId);
      const icon = element.querySelector('i');

      if (input.type === "password") {
          input.type = "text";
          icon.classList.remove('fa-eye');
          icon.classList.add('fa-eye-slash');
      } else {
          input.type = "password";
          icon.classList.remove('fa-eye-slash');
          icon.classList.add('fa-eye');
      }
    }
  </script>
</body>
</html>
