<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign In</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link
    href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Serif+Display&display=swap"
    rel="stylesheet" />

</head>

<body
  class="font-roboto bg-[url('../../assets/background.png')] bg-cover bg-center bg-no-repeat text-[#374151] min-h-screen flex items-center justify-center px-4">

  <div class="w-full max-w-md">

    <div class="bg-white border border-stone-200 rounded-2xl px-10 py-10 shadow-sm">

      <!-- Logo + Header inside card -->
      <div class="mb-8 text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl mb-4">
          <img src="../../assets/Province_of_Pangasinan.png" alt="Logo">
        </div>
        <h1 class="text-3xl leading-tight">Welcome back</h1>
        <p class="mt-1.5 text-md text-stone-400 font-light tracking-wide">Sign in to your account to continue</p>
      </div>

      <?php if (isset($_SESSION['login_error'])): ?>
        <div
          class="mb-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg text-sm flex items-center gap-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="12" />
            <line x1="12" y1="16" x2="12.01" y2="16" />
          </svg>
          <p><?php echo htmlspecialchars($_SESSION['login_error']); ?></p>
        </div>
        <?php unset($_SESSION['login_error']); ?>
      <?php endif; ?>

      <form method="POST" action="../../includes/auth_login.php">

        <!-- Email field -->
        <div class="mb-5">
          <label class="block text-xs font-medium uppercase tracking-widest mb-2">Username/Email</label>
          <div class="relative">
            <span class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-stone-400" width="24" height="24"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="lucide lucide-at-sign-icon lucide-at-sign">
                <circle cx="12" cy="12" r="4" />
                <path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-4 8" />
              </svg>
            </span>
            <input type="text" name="username" placeholder="Username or Email"
              class="w-full pl-10 pr-4 py-3 text-sm bg-stone-50 border border-stone-200 rounded-xl outline-none focus:border-[#0033A1] focus:ring-2 focus:ring-[#0033A1]/10 placeholder:text-stone-400 transition"
              required />
          </div>
        </div>

        <!-- Password field -->
        <div class="mb-2">
          <label class="block text-xs font-medium uppercase tracking-widest mb-2">Password</label>
          <div class="relative">
            <span class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-stone-400" width="24" height="24"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="lucide lucide-lock-icon lucide-lock">
                <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
              </svg>
            </span>
            <input id="passwordInput" name="password" type="password" placeholder="••••••••"
              class="w-full pl-10 pr-4 py-3 text-sm bg-stone-50 border border-stone-200 rounded-xl outline-none focus:border-[#0033A1] focus:ring-2 focus:ring-[#0033A1]/10 placeholder:text-stone-400 transition"
              required />
            <!-- Toggle button -->
            <button type="button" onclick="togglePassword()"
              class="absolute inset-y-0 right-3.5 flex items-center text-stone-400 hover:text-stone-700 transition"
              aria-label="Toggle password visibility">
              <!-- Eye icon (shown when password hidden) -->
              <svg id="eyeOpen" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <!-- Eye-slash icon (shown when password visible) -->
              <svg id="eyeClosed" class="w-4 h-4 hidden" fill="none" stroke="currentColor" stroke-width="1.8"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Forgot password -->
        <div class="flex justify-end mb-7">
          <a href="#" class="text-xs text-stone-500 hover:text-[#0033A1] transition underline underline-offset-2">
            Forgot password?
          </a>
        </div>

        <!-- Sign in button -->
        <button type="submit"
          class="w-full flex items-center justify-center gap-3 bg-[#0033A1] hover:bg-[#00247A] active:scale-[0.98] text-white text-sm font-semibold py-3.5 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg">
          Sign In
        </button>

      </form>

      <p class="text-center text-xs text-stone-400 mt-6">
        Province of Pangasinan • Official Portal
      </p>
    </div>

  </div>

  <script>
    function togglePassword() {
      const input = document.getElementById('passwordInput');
      const eyeOpen = document.getElementById('eyeOpen');
      const eyeClosed = document.getElementById('eyeClosed');
      if (input.type === 'password') {
        input.type = 'text';
        eyeOpen.classList.add('hidden');
        eyeClosed.classList.remove('hidden');
      } else {
        input.type = 'password';
        eyeOpen.classList.remove('hidden');
        eyeClosed.classList.add('hidden');
      }
    }
  </script>

</body>

</html>