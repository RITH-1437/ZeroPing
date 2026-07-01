<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .error-container {
            text-align: center;
            background: #ffffff;
            padding: 50px 70px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            max-width: 90%;
        }

        h1 {
            font-size: 120px;
            margin: 0;
            color: #ff6b6b;
            line-height: 1;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        h2 {
            font-size: 28px;
            margin: 15px 0;
            font-weight: 600;
        }

        p {
            margin-bottom: 30px;
            color: #6c757d;
            font-size: 16px;
            line-height: 1.5;
        }

        .home-button {
            display: inline-block;
            padding: 12px 25px;
            background-color: #4facfe;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .home-button:hover {
            background-color: #3b8fd9;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <div class="error-container">
        <h1>404</h1>
        <h2>Oops! Page Not Found</h2>
        <p>The page you are looking for might have been removed, had its name changed,<br>or is temporarily unavailable.</p>
        <a href="/" class="home-button">Go Back Home</a>
    </div>

</body>
</html>