<!-- Show Home Layout -->

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    <!-- Show Contents Here -->

    <div class="main-content">
        @yield('content')
    </div>

</body>
</html>