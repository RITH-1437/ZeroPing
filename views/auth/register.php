<!DOCTYPE html>
<html>

<head>

    <title>Register</title>

</head>

<body>

<h1>Create Account</h1>

<form method="POST" action="/register">

    <input
        type="text"
        name="first_name"
        placeholder="First Name"
        required
    >

    <br><br>

    <input
        type="text"
        name="last_name"
        placeholder="Last Name"
        required
    >

    <br><br>

    <input
        type="text"
        name="username"
        placeholder="Username"
        required
    >

    <br><br>

    <input
        type="email"
        name="email"
        placeholder="Email"
        required
    >

    <br><br>

    <input
        type="password"
        name="password"
        placeholder="Password"
        required
    >

    <br><br>

    <input
        type="text"
        name="phone"
        placeholder="Phone"
    >

    <br><br>

    <button>

        Register

    </button>

</form>

</body>

</html>