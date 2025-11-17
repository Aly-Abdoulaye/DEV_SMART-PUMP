<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Smart Pump</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
        }
        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #F5F5F5;
            padding: 15px;
        }
        .login-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            width: 800px;
            height: 60%;
            display: flex;
            position: relative;
            overflow: hidden;
        }
        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            height: 8px;
            width: 100%;
            background-color: #0A1F44;
        }
        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40%;
            padding-right: 20px;
        }
        .logo img {
            height: 80px;
            width: 260px;
            object-fit: contain;
        }
        .login-form {
            width: 60%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .login-form label {
            margin: 10px 0 5px;
            font-weight: 500;
            color: #555;
        }
        .login-form input {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            background-color: #F5F5F5;
        }
        .login-form input:focus {
            outline: none;
            border: 1px solid #bbb;
        }
        .forgot-password {
            text-align: right;
            font-size: 12px;
            color: #888;
            text-decoration: none;
            margin: 5px 0 15px;
        }
        .login-button {
            background-color: black;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            width: 100%;
            font-weight: bold;
            font-size: 14px;
        }
        .login-button:hover {
            background-color: #333;
        }

        /* Responsive - Tablette et Mobile */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                width: 100%;
                height: auto;
                padding: 20px;
            }
            .logo {
                width: 100%;
                justify-content: center;
                margin-bottom: 20px;
            }
            .logo img {
                height: 60px;
                width: 200px;
            }
            .login-form {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            .login-container {
                padding: 15px;
            }
            .login-form input {
                padding: 10px;
            }
            .login-button {
                padding: 10px;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="logo">
        <img src="{{ asset('img/Logo.png') }}" alt="Logo Smart Pump">
    </div>

    <form class="login-form" method="POST" action="{{ route('login') }}">
    @csrf
    <label for="email">Email address</label>
    <input type="email" name="email" id="email" placeholder="Entrez votre email" required>

    <label for="password">Password</label>
    <input type="password" name="password" id="password" placeholder="Entrez votre mot de passe" required>

    <a href="{{ route('password.request') }}" class="forgot-password">Mot de passe oublié ?</a>

    <button type="submit" class="login-button">Se connecter</button>
    </form>

</div>

</body>
</html>
