<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>RideNow | Register</title>

  {{-- Bootstrap CDN --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="{{ asset('css/register.css') }}" rel="stylesheet">
</head>
<body>
  <div class="overlay">
    <div class="card card-register p-4">
      <div class="px-3 py-2">
        <h3 class="brand-title text-center mb-1">Create an account</h3>
        <p class="text-center text-muted small mb-4">Enter your information to get started</p>

        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('register') }}">
          @csrf

          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror" required>
            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Address</label>
            <input type="text" name="address" value="{{ old('address') }}" class="form-control @error('address') is-invalid @enderror" required>
            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3 position-relative">
  <label class="form-label">Password</label>
  <div class="input-group">
    <input type="password" id="password" name="password"
           class="form-control @error('password') is-invalid @enderror"
           required
           onfocus="showPasswordHint()"
           onblur="hidePasswordHint()"
           oninput="validatePassword(this.value)">
    <span class="input-group-text" onclick="togglePassword('password', this)" style="cursor: pointer;">
      <i class="fa-solid fa-eye"></i>
    </span>
  </div>

  @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror

  <!-- Password hint box -->
  <div id="passwordHint" class="password-hint shadow">
    <p class="mb-2 fw-semibold">Password must include:</p>
    <ul class="list-unstyled small mb-0">
      <li id="lenRule"><i class="fa-solid fa-xmark text-danger me-2"></i>8-20 <strong>characters</strong></li>
      <li id="upperRule"><i class="fa-solid fa-xmark text-danger me-2"></i>At least one <strong>capital letter</strong></li>
      <li id="numRule"><i class="fa-solid fa-xmark text-danger me-2"></i>At least one <strong>number</strong></li>
      <li id="spaceRule"><i class="fa-solid fa-xmark text-danger me-2"></i><strong>No spaces</strong></li>
    </ul>
  </div>
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
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = "password";
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
  }

  function showPasswordHint() {
    document.getElementById('passwordHint').style.display = 'block';
  }

  function hidePasswordHint() {
    const hint = document.getElementById('passwordHint');
    const password = document.getElementById('password').value;
    if (password === '' || allRulesPassed(password)) {
      hint.style.display = 'none';
    }
  }

  function validatePassword(password) {
    const lenRule = document.getElementById('lenRule');
    const upperRule = document.getElementById('upperRule');
    const numRule = document.getElementById('numRule');
    const spaceRule = document.getElementById('spaceRule');

    updateRule(lenRule, password.length >= 8 && password.length <= 20);
    updateRule(upperRule, /[A-Z]/.test(password));
    updateRule(numRule, /\d/.test(password));
    updateRule(spaceRule, !/\s/.test(password));

    if (allRulesPassed(password)) {
      document.getElementById('passwordHint').style.display = 'none';
    } else {
      document.getElementById('passwordHint').style.display = 'block';
    }
  }

  function updateRule(element, isValid) {
    const icon = element.querySelector('i');
    if (isValid) {
      icon.classList.replace('fa-xmark', 'fa-check');
      icon.classList.replace('text-danger', 'text-success');
      element.classList.add('text-success');
      element.classList.remove('text-danger');
    } else {
      icon.classList.replace('fa-check', 'fa-xmark');
      icon.classList.replace('text-success', 'text-danger');
      element.classList.remove('text-success');
      element.classList.add('text-danger');
    }
  }

  function allRulesPassed(password) {
    return password.length >= 8 && password.length <= 20 &&
           /[A-Z]/.test(password) && /\d/.test(password) &&
           !/\s/.test(password);
  }
</script>
</body>
</html>
