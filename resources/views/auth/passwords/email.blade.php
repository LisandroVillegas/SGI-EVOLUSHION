<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-width=1.0">
    <title>Recuperar Contraseña - Evolushion SGI</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            /* Mismos colores que el login para mantener consistencia */
            --primary: #2b2b2b; 
            --primary-hover: #1a1a1a;
            --accent: #d4af37; 
            --secondary: #858796;
            --dark: #2d2d2d;
            --light: #f4f6f9;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--light);
            background-image: radial-gradient(circle at center, #ffffff 0%, #f4f6f9 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            display: flex;
            width: 100%;
            max-width: 900px;
            min-height: 550px;
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
            overflow: hidden;
        }

        .login-branding::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: linear-gradient(45deg, rgba(255,255,255,0.03) 25%, transparent 25%, transparent 50%, rgba(255,255,255,0.03) 50%, rgba(255,255,255,0.03) 75%, transparent 75%, transparent);
            background-size: 20px 20px;
            opacity: 0.5;
        }

        .login-logo {
            width: 180px;
            height: 180px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
            margin-bottom: 25px;
            position: relative;
            z-index: 1;
            background-color: #fff;
        }

        .login-branding h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: 2px;
            position: relative;
            z-index: 1;
        }

        .login-branding p {
            font-size: 1rem;
            font-weight: 300;
            opacity: 0.8;
            position: relative;
            z-index: 1;
        }

        .login-form-container {
            flex: 1.2;
            padding: 50px 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background-color: #ffffff;
        }

        .login-form-container h2 {
            color: var(--dark);
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .login-form-container .description {
            text-align: center;
            color: var(--secondary);
            font-size: 0.95rem;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--dark);
            font-weight: 500;
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
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
            left: 16px;
            top: 41px;
            color: #9e9e9e;
            font-size: 1.1rem;
        }

        .btn-primary {
            width: 100%;
            padding: 14px;
            background-color: var(--primary);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            letter-spacing: 0.5px;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .invalid-feedback {
            color: #e74a3b;
            font-size: 0.85rem;
            margin-top: 5px;
            display: block;
        }

        .is-invalid {
            border-color: #e74a3b !important;
        }

        .back-to-login {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 25px;
            color: var(--secondary);
            text-decoration: none;
            font-size: 0.95rem;
            transition: color 0.3s;
        }

        .back-to-login:hover {
            color: var(--primary);
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                height: auto;
                max-width: 450px;
            }
            .login-branding {
                padding: 40px 20px;
            }
            .login-logo {
                width: 140px;
                height: 140px;
            }
            .login-form-container {
                padding: 40px 30px;
            }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <!-- Panel Izquierdo (Logo de la empresa) -->
        <div class="login-branding">
            <img src="{{ asset('vendor/adminlte/dist/img/AdminLTELogo.jpg') }}" alt="Logo Evolushion" class="login-logo">
            <h1>EVOLUSHION</h1>
            <p>Sistema de Gestión de Inventario</p>
        </div>

        <!-- Panel Derecho (Formulario) -->
        <div class="login-form-container">
            <h2>Recuperar Contraseña</h2>
            <p class="description">Ingresa tu correo electrónico y te enviaremos las instrucciones para restablecer tu contraseña.</p>

            @if (session('status'))
                <div class="alert-success" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Correo Electrónico Registrado</label>
                    <i class="fas fa-envelope icon"></i>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Ej. admin@correo.com">
                    
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <button type="submit" class="btn-primary">
                    <i class="fas fa-paper-plane"></i> Enviar Enlace
                </button>

                <a class="back-to-login" href="{{ route('login') }}">
                    <i class="fas fa-arrow-left"></i> Volver al Inicio de Sesión
                </a>
            </form>
        </div>
    </div>

</body>
</html>