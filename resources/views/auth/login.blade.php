<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Garuda-Hub</title>
</head>
<body>

    <h2>Login Garuda-Hub</h2>

    @if ($errors->any())
        <p style="color:red">
            {{ $errors->first() }}
        </p>
    @endif

    <form method="POST" action="{{ route('login.store') }}">

        @csrf

        <label>Email</label>
        <input
            type="email"
            name="email"
            value="{{ old('email') }}"
            required
        >

        <br><br>

        <label>Password</label>
        <input
            type="password"
            name="password"
            required
        >

        <br><br>

        <button type="submit">Login</button>

    </form>

</body>
</html>
