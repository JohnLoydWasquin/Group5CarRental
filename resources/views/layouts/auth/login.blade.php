<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Autopiloto Car Rental</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="icon" type="image/jpeg" href="{{ asset('images/favicon.jpg') }}">
  <style>
    .fade { transition: opacity 0.8s ease-in-out; }
  </style>
</head>

<body class="flex min-h-screen bg-gray-100">

  <div class="flex w-full md:w-1/2 flex-col justify-center items-center p-10 bg-white shadow-lg">
    <div class="w-full max-w-md">
      <h1 class="text-4xl font-bold text-gray-900 text-center mb-2">Welcome Back!</h1>
      <p class="text-gray-500 text-center mb-8">Login to your account to continue</p>

      <form method="POST" action="{{ route('login') }}" class="space-y-5" id="loginForm">
        @csrf
        
        <div>
          <label class="block text-gray-700 mb-1 font-semibold">Email</label>
          <input type="email" name="email" id="email" required 
            value="{{ old('email', $rememberedEmail ?? '') }}"
            class="w-full border-gray-300 rounded-lg p-3 border focus:ring-green-500 focus:border-green-500">
          @if($errors->has('email'))
            <p class="text-red-500 text-sm mt-1">{{ $errors->first('email') }}</p>
          @endif
        </div>

        <div class="relative">
          <label class="block text-gray-700 mb-1 font-semibold">Password</label>
          <input type="password" name="password" id="password" required
            value="{{ old('password', $rememberedPassword ?? '') }}"
            class="w-full border-gray-300 rounded-lg p-3 border focus:ring-green-500 focus:border-green-500 pr-10">
          <button type="button" id="togglePassword" 
            class="absolute right-3 top-9 text-gray-500 hover:text-gray-700">
            <i class="fa-solid fa-eye"></i>
          </button>
        </div>

        <div class="flex justify-between items-center text-sm">
          <label class="flex items-center gap-2">
            <input type="checkbox" id="rememberMe" name="remember" {{ old('email', $rememberedEmail ?? '') ? 'checked' : '' }}>
            Remember Me
          </label>
          <a href="{{ route('forgot_phone') }}" class="text-green-600 hover:underline">Forgot Password?</a>
        </div>

        <button type="submit" 
          class="w-full bg-green-600 text-white font-semibold py-3 rounded-lg hover:bg-green-700 transition">
          Login
        </button>

        @if($errors->has('general'))
          <p class="text-red-500 text-sm mt-2">{{ $errors->first('general') }}</p>
        @endif
      </form>

      <p class="text-center text-gray-600 mt-5">
        Don't have an account?
        <a href="{{ route('register') }}" class="text-green-600 hover:underline">Register here</a>
      </p>
    </div>
  </div>

  <div class="hidden md:flex w-1/2 bg-gray-900 text-white items-center justify-center relative overflow-hidden">

    <img id="showcase-image"
      src="{{ asset('images/slide1.png') }}" class="rounded-2xl max-w-[350px] max-h-[350px] object-contain shadow-lg fade opacity-100">
    <p id="imageDescription" class="absolute bottom-14 w-full text-center text-gray-300 text-lg leading-tight"></p>

    <button onclick="prevImage()" 
      class="absolute left-6 bg-gray-800 bg-opacity-60 text-white p-3 rounded-full hover:bg-opacity-90 transition">
      <i class="fa-solid fa-chevron-left text-2xl"></i>
    </button>

    <button onclick="nextImage()" 
      class="absolute right-6 bg-gray-800 bg-opacity-60 text-white p-3 rounded-full hover:bg-opacity-90 transition">
      <i class="fa-solid fa-chevron-right text-2xl"></i>
    </button>

    <div class="absolute bottom-8 flex space-x-2">
      <span class="dot w-3 h-3 bg-green-500 rounded-full"></span>
      <span class="dot w-3 h-3 bg-gray-400 rounded-full"></span>
      <span class="dot w-3 h-3 bg-gray-400 rounded-full"></span>
      <span class="dot w-3 h-3 bg-gray-400 rounded-full"></span>
      <span class="dot w-3 h-3 bg-gray-400 rounded-full"></span>
    </div>
  </div>
  <script src="{{ asset('js/auth.js') }}"></script>
</body>
</html>
