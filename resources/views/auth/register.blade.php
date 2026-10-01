<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - Evolushion SGI</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2b2b2b; 
            --primary-hover: #1a1a1a;
            --secondary: #858796;
            --dark: #2d2d2d;
            --light: #f4f6f9;
        }
        
        * {
            margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--light);
            background-image: radial-gradient(circle at center, #ffffff 0%, #f4f6f9 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }

        .login-container {
            display: flex;
            width: 100%;
            max-width: 900px;
            min-height: 600px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin: 20px;
        }

        .login-branding {
            flex: 1;
            background: var(--primary);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
        }

        .login-logo {
            width: 160px;
            height: 160px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 20px;
            background-color: #fff;
        }

        .login-branding h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: 2px;
        }

        .login-form-container {
            flex: 1.2;
            padding: 40px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background-color: #ffffff;
        }

        .login-form-container h2 {
            color: var(--dark);
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: 25px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: var(--dark);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 10px 15px 10px 42px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            color: #4a4a4a;
            background-color: #fcfcfc;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(43, 43, 43, 0.1);
        }

        .form-group .icon {
            position: absolute;
            left: 14px;
            top: 36px;
            color: #9e9e9e;
            font-size: 1rem;
        }

        .btn-primary {
            width: 100%;
            padding: 12px;
            background-color: var(--primary);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .invalid-feedback {
            color: #e74a3b;
            font-size: 0.8rem;
            margin-top: 4px;
            display: block;
        }

        .is-invalid {
            border-color: #e74a3b !important;
        }

        .auth-links {
            margin-top: 20px;
            text-align: center;
        }

        .login-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .login-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .login-container { flex-direction: column; }
            .login-branding { padding: 30px 20px; }
            .login-form-container { padding: 30px 20px; }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <!-- Panel Izquierdo -->
        <div class="login-branding">
            <img src="{{ asset('vendor/adminlte/dist/img/AdminLTELogo.jpg') }}" alt="Logo Evolushion" class="login-logo">
            <h1>EVOLUSHION</h1>
            <p>Registro de Trabajadoras</p>
        </div>

        <!-- Panel Derecho -->
        <div class="login-form-container">
            <h2>Crear Nueva Cuenta</h2>
            
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group">
                    <label for="name">Nombre Completo</label>
                    <i class="fas fa-user icon"></i>
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Ej. María López">
                    @error('name')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <i class="fas fa-envelope icon"></i>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Ej. maria@correo.com">
                    @error('email')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <i class="fas fa-lock icon"></i>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Mínimo 8 caracteres">
                    @error('password')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password-confirm">Confirmar Contraseña</label>
                    <i class="fas fa-check-double icon"></i>
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Repite la contraseña">
                </div>

                <button type="submit" class="btn-primary">
                    <i class="fas fa-user-plus"></i> Registrar Cuenta
                </button>

                <div class="auth-links">
                    <a class="login-link" href="{{ route('login') }}">
                        ¿Ya tienes cuenta? Inicia sesión
                    </a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>