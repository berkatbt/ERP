<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ERP Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
</head>

<body class="min-h-screen flex items-center justify-center bg-black">

<!-- BACKGROUND -->
<div class="fixed inset-0">
    <img src="/img/wallpaper_login.png" class="w-full h-full object-cover" alt="">
    <div class="absolute inset-0 bg-black/50"></div>
</div>

<!-- CARD -->
<div class="relative z-10 w-full px-4">

    <div class="max-w-md mx-auto 
                bg-white/10 backdrop-blur-xl 
                border border-white/20
                rounded-2xl shadow-2xl 
                p-8">

        <!-- TITLE -->
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-white">ERP Login</h2>
            <p class="text-gray-300 text-sm mt-1">
                Please login to your account
            </p>
        </div>

        <!-- FORM -->
        <form method="POST" action="/login" class="space-y-5">
            @csrf

            <!-- EMAIL -->
            <div>
                <label class="text-sm text-gray-300">Email</label>
                <input type="email" name="email"
                    class="w-full mt-2 px-4 py-3 rounded-lg
                           text-white !bg-transparent
                           placeholder-gray-400
                           border border-white/30
                           focus:outline-none focus:ring-2 focus:ring-white/70
                           transition duration-300"
                    placeholder="Enter your email"
                    required>
            </div>

            <!-- PASSWORD -->
            <div>
                <label class="text-sm text-gray-300">Password</label>
                <input type="password" name="password"
                    class="w-full px-4 py-3 rounded-lg
                           text-white !bg-transparent
                           placeholder-gray-400
                           border border-white/30
                           focus:outline-none focus:ring-2 focus:ring-white/70
                           transition duration-300"
                    placeholder="********"
                    required>
            </div>

            <!-- BUTTON -->
            <button type="submit"
                class="w-full py-3 rounded-lg font-semibold text-white
                       bg-gradient-to-r to--600 border-1 border-white/20
                       hover:opacity-90 transition duration-300">
                Login
            </button>

        </form>

    </div>

</div>

</body>
</html>