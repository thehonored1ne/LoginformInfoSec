<!-- Show LoginForm Layout -->

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    <!-- Show Contents Here -->

    <!-- The content of the extends-login.blade.php will be injected into the section of this layout -->
    <div class="main-content">
        @yield('content')
    </div>

</body>
</html>