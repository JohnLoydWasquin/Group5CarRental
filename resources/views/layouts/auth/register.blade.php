<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register | Autopiloto Car Rental</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    .fade { transition: opacity 0.8s ease-in-out; }
    .password-hint {
      display: none;
      background: #f9f9f9;
      border-radius: 0.5rem;
      padding: 1rem;
      margin-top: 0.5rem;
      border: 1px solid #ddd;
    }
  </style>
</head>

<body class="flex min-h-screen bg-gray-100">

  <div class="flex w-full md:w-1/2 flex-col justify-center items-center p-10 bg-white shadow-lg">
    <div class="w-full max-w-md">
      <h1 class="text-4xl font-bold text-gray-900 text-center mb-2">Create an Account</h1>
      <p class="text-gray-500 text-center mb-8">Enter your information to get started</p>

      @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4 text-center">
          {{ session('success') }}
        </div>
      @endif

      <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
          <label class="block text-gray-700 mb-1 font-semibold">Full Name</label>
          <input type="text" name="name" value="{{ old('name') }}" required
            class="w-full border-gray-300 rounded-lg p-3 border focus:ring-green-500 focus:border-green-500">
          @error('name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-gray-700 mb-1 font-semibold">Email</label>
          <input type="email" name="email" value="{{ old('email') }}" required
            class="w-full border-gray-300 rounded-lg p-3 border focus:ring-green-500 focus:border-green-500">
          @error('email') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-gray-700 mb-1 font-semibold">Phone</label>
          <input type="text" name="phone" value="{{ old('phone') }}" required
            class="w-full border-gray-300 rounded-lg p-3 border focus:ring-green-500 focus:border-green-500">
          @error('phone') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-gray-700 mb-1 font-semibold">Address</label>
          <input type="text" name="address" value="{{ old('address') }}" required
            class="w-full border-gray-300 rounded-lg p-3 border focus:ring-green-500 focus:border-green-500">
          @error('address') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="relative">
          <label class="block text-gray-700 mb-1 font-semibold">Password</label>
          <input type="password" id="password" name="password" required
            class="w-full border-gray-300 rounded-lg p-3 border focus:ring-green-500 focus:border-green-500 pr-10"
            onfocus="showPasswordHint()" onblur="hidePasswordHint()" oninput="validatePassword(this.value)">
          <button type="button" onclick="togglePassword('password', this)"
            class="absolute right-3 top-9 text-gray-500 hover:text-gray-700">
            <i class="fa-solid fa-eye"></i>
          </button>
          @error('password') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror

          <div id="passwordHint" class="password-hint shadow">
            <p class="mb-2 font-semibold">Password must include:</p>
            <ul class="text-sm space-y-1">
              <li id="lenRule"><i class="fa-solid fa-xmark text-red-500 mr-2"></i>8-20 <strong>characters</strong></li>
              <li id="upperRule"><i class="fa-solid fa-xmark text-red-500 mr-2"></i>At least one <strong>capital letter</strong></li>
              <li id="numRule"><i class="fa-solid fa-xmark text-red-500 mr-2"></i>At least one <strong>number</strong></li>
              <li id="spaceRule"><i class="fa-solid fa-xmark text-red-500 mr-2"></i><strong>No spaces</strong></li>
            </ul>
          </div>
        </div>

        <div class="relative">
          <label class="block text-gray-700 mb-1 font-semibold">Confirm Password</label>
          <input type="password" id="password_confirmation" name="password_confirmation" required
            class="w-full border-gray-300 rounded-lg p-3 border focus:ring-green-500 focus:border-green-500 pr-10">
          <button type="button" onclick="togglePassword('password_confirmation', this)"
            class="absolute right-3 top-9 text-gray-500 hover:text-gray-700">
            <i class="fa-solid fa-eye"></i>
          </button>
        </div>

        <button type="submit"
          class="w-full bg-green-600 text-white font-semibold py-3 rounded-lg hover:bg-green-700 transition">
          Create Account
        </button>

        <p class="text-center text-gray-600 mt-5">
          Already have an account?
          <a href="{{ route('login') }}" class="text-green-600 hover:underline">Sign in</a>
        </p>
      </form>
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
