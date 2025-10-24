<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>RideNow | Register</title>

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
    .card-register {
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
    .brand-title {
      font-weight: 700;
      letter-spacing: .3px;
    }
    @media (max-width: 540px) {
      .overlay { padding: 1rem; }
      .card-register { padding: 1rem; }
    }
  </style>
</head>
<body>
  <div class="overlay">
    <div class="card card-register p-4">
      <div class="px-3 py-2">
        <h3 class="brand-title text-center mb-1">Create an account</h3>
        <p class="text-center text-muted small mb-4">Enter your information to get started</p>

        {{-- success message --}}
        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('register') }}">
          @csrf

          {{-- Name --}}
          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- Email --}}
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- Phone --}}
          <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror" required>
            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- Address --}}
          <div class="mb-3">
            <label class="form-label">Address</label>
            <input type="text" name="address" value="{{ old('address') }}" class="form-control @error('address') is-invalid @enderror" required>
            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group">
                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                <span class="input-group-text" onclick="togglePassword('password', this)" style="cursor: pointer;">
                <i class="fa-solid fa-eye"></i>
                </span>
            </div>
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <div class="input-group">
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                <span class="input-group-text" onclick="togglePassword('password_confirmation', this)" style="cursor: pointer;">
                <i class="fa-solid fa-eye"></i>
                </span>
            </div>
            </div>
          <button class="btn btn-primary w-100 mt-2" type="submit">Create Account</button>

          <p class="text-center small text-muted mt-3 mb-0">
            Already have an account? <a href="{{ route('login') }}">Sign in</a>
          </p>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
