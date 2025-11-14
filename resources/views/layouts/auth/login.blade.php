<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Autopiloto Car Rental</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .fade { transition: opacity 0.8s ease-in-out; }
  </style>
</head>

<body class="flex min-h-screen bg-gray-100">

  <!-- LEFT SIDE: LOGIN FORM -->
  <div class="flex w-full md:w-1/2 flex-col justify-center items-center p-10 bg-white shadow-lg">
    <div class="w-full max-w-md">
      <h1 class="text-4xl font-bold text-gray-900 text-center mb-2">Welcome Back!</h1>
      <p class="text-gray-500 text-center mb-8">Login to your account to continue</p>

      <!-- LOGIN FORM -->
      <form method="POST" action="{{ route('login') }}" class="space-y-5" id="loginForm">
        @csrf
        
        <!-- EMAIL -->
        <div>
          <label class="block text-gray-700 mb-1 font-semibold">Email</label>
          <input type="email" name="email" id="email" required 
            value="{{ old('email', $rememberedEmail ?? '') }}"
            class="w-full border-gray-300 rounded-lg p-3 border focus:ring-green-500 focus:border-green-500">
          @if($errors->has('email'))
            <p class="text-red-500 text-sm mt-1">{{ $errors->first('email') }}</p>
          @endif
        </div>

        <!-- PASSWORD -->
        <div class="relative">
          <label class="block text-gray-700 mb-1 font-semibold">Password</label>
          <input type="password" name="password" id="password" required
            value="{{ old('password', $rememberedPassword ?? '') }}"
            class="w-full border-gray-300 rounded-lg p-3 border focus:ring-green-500 focus:border-green-500 pr-10">
          <!-- 👁️ Eye Toggle -->
          <button type="button" id="togglePassword" 
            class="absolute right-3 top-9 text-gray-500 hover:text-gray-700">
            <i class="fa-solid fa-eye"></i>
          </button>
        </div>

        <!-- REMEMBER ME + FORGOT PASSWORD -->
        <div class="flex justify-between items-center text-sm">
          <label class="flex items-center gap-2">
            <input type="checkbox" id="rememberMe" name="remember" {{ old('email', $rememberedEmail ?? '') ? 'checked' : '' }}>
            Remember Me
          </label>
          <a href="{{ route('forgot_phone') }}" class="text-green-600 hover:underline">Forgot Password?</a>
        </div>

        <!-- SUBMIT BUTTON -->
        <button type="submit" 
          class="w-full bg-green-600 text-white font-semibold py-3 rounded-lg hover:bg-green-700 transition">
          Login
        </button>

        <!-- GENERAL LOGIN ERROR (if needed) -->
        @if($errors->has('general'))
          <p class="text-red-500 text-sm mt-2">{{ $errors->first('general') }}</p>
        @endif
      </form>

      <!-- REGISTER LINK -->
      <p class="text-center text-gray-600 mt-5">
        Don't have an account?
        <a href="{{ route('register') }}" class="text-green-600 hover:underline">Register here</a>
      </p>
    </div>
  </div>

  <!-- RIGHT SIDE: IMAGE CAROUSEL -->
  <div class="hidden md:flex w-1/2 bg-gray-900 text-white items-center justify-center relative overflow-hidden">
    
    <!-- IMAGE DISPLAY -->
    <div class="relative w-3/4 h-3/4 flex items-center justify-center">
      <img id="showcase-image" 
        src="{{ asset('images/ToyotaInnova.webp') }}" 
        alt="Ad Image" 
        class="rounded-2xl w-full h-auto object-contain shadow-lg fade opacity-100">
    </div>

    <!-- NAV BUTTONS -->
    <button onclick="prevImage()" 
      class="absolute left-6 bg-gray-800 bg-opacity-60 text-white p-3 rounded-full hover:bg-opacity-90 transition">
      <i class="fa-solid fa-chevron-left text-2xl"></i>
    </button>
    <button onclick="nextImage()" 
      class="absolute right-6 bg-gray-800 bg-opacity-60 text-white p-3 rounded-full hover:bg-opacity-90 transition">
      <i class="fa-solid fa-chevron-right text-2xl"></i>
    </button>

    <!-- DOT INDICATORS -->
    <div class="absolute bottom-8 flex space-x-2">
      <span class="dot w-3 h-3 bg-green-500 rounded-full"></span>
      <span class="dot w-3 h-3 bg-gray-400 rounded-full"></span>
      <span class="dot w-3 h-3 bg-gray-400 rounded-full"></span>
      <span class="dot w-3 h-3 bg-gray-400 rounded-full"></span>
      <span class="dot w-3 h-3 bg-gray-400 rounded-full"></span>
    </div>

    <!-- CAPTION -->
    <div class="absolute bottom-20 text-center px-6">
      <h2 class="text-2xl font-semibold mb-1">The all-in-one platform for growing your business</h2>
      <p class="text-gray-300 text-sm">Powering over 10,000 Filipino businesses of all sizes.</p>
    </div>
  </div>

  <!-- JS -->
  <script>
const images = [
  "{{ asset('images/ToyotaInnova.webp') }}",
  "{{ asset('images/Toyota-Vios.webp') }}",
  "{{ asset('images/ToyotaRush.webp') }}",
  "{{ asset('images/ToyotaWigoG.webp') }}",
  "{{ asset('images/MitsubishiMontero.webp') }}"
];

// === Load last viewed image from localStorage ===
let currentIndex = localStorage.getItem('currentImageIndex') 
  ? parseInt(localStorage.getItem('currentImageIndex')) 
  : 0;

const imageElement = document.getElementById("showcase-image");
const dots = document.querySelectorAll(".dot");

// --- Immediately set image and dots to prevent "jump" ---
imageElement.src = images[currentIndex];
dots.forEach((dot, i) => {
  dot.classList.toggle("bg-green-500", i === currentIndex);
  dot.classList.toggle("bg-gray-400", i !== currentIndex);
});

// --- Carousel functions ---
function updateImage() {
  imageElement.style.transition = "opacity 0.4s";
  imageElement.style.opacity = 0;

  setTimeout(() => {
    imageElement.src = images[currentIndex];
    imageElement.style.opacity = 1;
  }, 10);

  dots.forEach((dot, i) => {
    dot.classList.toggle("bg-green-500", i === currentIndex);
    dot.classList.toggle("bg-gray-400", i !== currentIndex);
  });

  localStorage.setItem('currentImageIndex', currentIndex);
}

function nextImage() {
  currentIndex = (currentIndex + 1) % images.length;
  updateImage();
}

function prevImage() {
  currentIndex = (currentIndex - 1 + images.length) % images.length;
  updateImage();
}

// === PASSWORD TOGGLE + VALIDATION ===
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

  document.getElementById('passwordHint').style.display = allRulesPassed(password) ? 'none' : 'block';
}

function updateRule(element, isValid) {
  const icon = element.querySelector('i');
  if (isValid) {
    icon.classList.replace('fa-xmark', 'fa-check');
    icon.classList.replace('text-red-500', 'text-green-500');
    element.classList.add('text-green-600');
  } else {
    icon.classList.replace('fa-check', 'fa-xmark');
    icon.classList.replace('text-green-500', 'text-red-500');
    element.classList.remove('text-green-600');
  }
}

function allRulesPassed(password) {
  return password.length >= 8 && password.length <= 20 &&
        /[A-Z]/.test(password) && /\d/.test(password) &&
        !/\s/.test(password);
}

// --- Attach toggle password event ---
document.getElementById('togglePassword').addEventListener('click', function() {
  togglePassword('password', this);
});
</script>

</body>
</html>
