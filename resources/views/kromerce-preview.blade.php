<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Kromerce - Plataforma e-commerce para MiPYMES. Vende productos y servicios con pagos internacionales y locales.">
    <title>Kromerce - E-commerce para MiPYMES</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <!-- Styles -->
    <style>
        :root {
            --background: 0 0% 100%;
            --foreground: 222.2 84% 4.9%;
            --card: 0 0% 100%;
            --card-foreground: 222.2 84% 4.9%;
            --primary: 221.2 83.2% 53.3%;
            --primary-foreground: 210 40% 98%;
            --secondary: 210 40% 96%;
            --secondary-foreground: 222.2 84% 4.9%;
            --muted: 210 40% 96%;
            --muted-foreground: 215.4 16.3% 46.9%;
            --border: 214.3 31.8% 91.4%;
            --ring: 221.2 83.2% 53.3%;
            --radius: 0.5rem;
        }

        * {
            border-color: hsl(var(--border));
        }

        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            background-color: hsl(var(--background));
            color: hsl(var(--foreground));
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Navbar Styles */
        .navbar {
            width: 90%;
            margin: 1.25rem auto;
            position: sticky;
            top: 1.25rem;
            z-index: 40;
            border-radius: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem;
            background-color: hsl(var(--card));
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            border: 1px solid hsl(var(--border));
        }

        .logo {
            font-weight: 700;
            font-size: 1.125rem;
            display: flex;
            align-items: center;
            text-decoration: none;
            color: hsl(var(--foreground));
        }

        .logo-icon {
            background: linear-gradient(to top right, rgb(37 99 235), rgb(59 130 246), rgb(37 99 235));
            border-radius: 0.5rem;
            width: 2.25rem;
            height: 2.25rem;
            margin-right: 0.5rem;
            border: 1px solid rgb(96 165 250);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.125rem;
        }

        .nav-links {
            display: none;
            align-items: center;
            gap: 1.5rem;
        }

        .nav-link {
            font-size: 0.875rem;
            font-weight: 500;
            color: hsl(var(--foreground));
            text-decoration: none;
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: hsl(var(--primary));
        }

        .nav-actions {
            display: none;
            align-items: center;
            gap: 1rem;
        }

        .mobile-menu-btn {
            display: block;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.5rem;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            border-radius: 9999px;
            padding: 0.25rem 0.625rem;
            font-size: 0.75rem;
            font-weight: 600;
            background-color: hsl(var(--secondary));
            color: hsl(var(--secondary-foreground));
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: colors 0.2s;
            padding: 0.5rem 1rem;
            cursor: pointer;
            border: 1px solid transparent;
        }

        .btn-primary {
            background-color: hsl(var(--primary));
            color: hsl(var(--primary-foreground));
        }

        .btn-primary:hover {
            background-color: hsl(var(--primary) / 0.9);
        }

        .btn-secondary {
            background-color: hsl(var(--secondary));
            color: hsl(var(--secondary-foreground));
        }

        .btn-secondary:hover {
            background-color: hsl(var(--secondary) / 0.8);
        }

        /* Hero Styles */
        .hero {
            padding: 5rem 0;
        }

        .hero-content {
            text-align: center;
            display: grid;
            place-items: center;
            max-width: 1200px;
            gap: 2rem;
            margin: 0 auto;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 9999px;
            padding: 0.5rem;
            font-size: 0.875rem;
            border: 1px solid hsl(var(--border));
            background-color: hsl(var(--card));
            color: hsl(var(--card-foreground));
        }

        .hero-title {
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
            font-size: 3rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .hero-title .gradient {
            background: linear-gradient(to right, rgb(37 99 235), rgb(6 182 212));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-description {
            max-width: 400px;
            margin: 0 auto;
            text-align: center;
            font-size: 1.25rem;
            color: hsl(var(--muted-foreground));
            line-height: 1.6;
        }

        .hero-actions {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            align-items: center;
        }

        .hero-actions-row {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .hero-features {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
            padding-top: 2rem;
        }

        .hero-feature {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: hsl(var(--muted-foreground));
        }

        .feature-dot {
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 50%;
        }

        .feature-dot.green { background-color: rgb(34 197 94); }
        .feature-dot.blue { background-color: rgb(37 99 235); }
        .feature-dot.purple { background-color: rgb(168 85 247); }

        /* Dashboard Preview */
        .dashboard-preview {
            position: relative;
            margin-top: 3.5rem;
        }

        .dashboard-shadow {
            position: absolute;
            top: -1.5rem;
            right: 3rem;
            width: 90%;
            height: 3rem;
            background: rgb(37 99 235 / 0.2);
            filter: blur(3rem);
            border-radius: 50%;
        }

        .dashboard-card {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            border-radius: 0.5rem;
            position: relative;
            display: flex;
            align-items: center;
            border-top: 2px solid rgb(37 99 235 / 0.3);
            background-color: hsl(var(--card));
            padding: 2rem;
        }

        .dashboard-content {
            width: 100%;
            display: grid;
            gap: 1rem;
        }

        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .stat-card {
            background-color: hsl(var(--muted));
            border-radius: 0.5rem;
            padding: 1rem;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .stat-value.blue { color: rgb(37 99 235); }
        .stat-value.green { color: rgb(34 197 94); }
        .stat-value.purple { color: rgb(168 85 247); }

        .stat-label {
            font-size: 0.875rem;
            color: hsl(var(--muted-foreground));
        }

        .dashboard-products {
            background-color: hsl(var(--muted));
            border-radius: 0.5rem;
            padding: 1rem;
        }

        .products-title {
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .product-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.875rem;
            padding: 0.25rem 0;
        }

        .product-trend {
            color: rgb(34 197 94);
        }

        .dashboard-gradient {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 5rem;
            background: linear-gradient(to bottom, hsl(var(--background) / 0), hsl(var(--background) / 0.5), hsl(var(--background)));
            border-radius: 0.5rem;
        }

        /* Responsive */
        @media (min-width: 768px) {
            .nav-links, .nav-actions {
                display: flex;
            }
            
            .mobile-menu-btn {
                display: none;
            }

            .hero-title {
                font-size: 3.75rem;
            }

            .hero-actions {
                flex-direction: row;
                gap: 1rem;
            }

            .hero-actions-row {
                gap: 1rem;
            }

            .btn {
                width: auto;
            }
        }

        @media (min-width: 1024px) {
            .navbar {
                width: 75%;
                max-width: 1200px;
            }

            .hero {
                padding: 8rem 0;
            }

            .dashboard-card {
                max-width: 1200px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <header class="navbar">
        <!-- Logo -->
        <a href="/" class="logo">
            <div class="logo-icon">K</div>
            Kromerce
        </a>

        <!-- Desktop Menu -->
        <nav class="nav-links">
            <a href="#products" class="nav-link">Productos</a>
            <a href="#services" class="nav-link">Servicios</a>
            <a href="#pricing" class="nav-link">Planes</a>
            <a href="#contact" class="nav-link">Contacto</a>
        </nav>

        <!-- Desktop CTA -->
        <div class="nav-actions">
            <span class="badge">Nuevo</span>
            <button class="btn btn-primary">Comenzar Gratis</button>
        </div>

        <!-- Mobile Menu -->
        <button class="mobile-menu-btn">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <!-- Badge -->
            <div class="hero-badge">
                <span style="margin-right: 0.5rem; color: rgb(37 99 235);">🚀</span>
                <span>Plataforma para MiPYMES</span>
            </div>

            <!-- Main Title -->
            <div class="hero-title">
                <h1>
                    Vende tus productos y
                    <span class="gradient">servicios</span>
                    al mundo
                </h1>
            </div>

            <!-- Description -->
            <p class="hero-description">
                La plataforma e-commerce diseñada para MiPYMES latinas. 
                Acepta pagos internacionales y locales, incluso desde Cuba.
            </p>

            <!-- CTA Buttons -->
            <div class="hero-actions">
                <div class="hero-actions-row">
                    <button class="btn btn-primary">Comenzar Gratis</button>
                    <button class="btn btn-secondary">Ver Demo</button>
                </div>

                <!-- Features Pills -->
                <div class="hero-features">
                    <div class="hero-feature">
                        <span class="feature-dot green"></span>
                        <span>Sin comisiones de setup</span>
                    </div>
                    <div class="hero-feature">
                        <span class="feature-dot blue"></span>
                        <span>Pagos locales e internacionales</span>
                    </div>
                    <div class="hero-feature">
                        <span class="feature-dot purple"></span>
                        <span>Soporte en español</span>
                    </div>
                </div>
            </div>

            <!-- Dashboard Preview -->
            <div class="dashboard-preview">
                <!-- Gradient Shadow -->
                <div class="dashboard-shadow"></div>

                <!-- Dashboard Card -->
                <div class="dashboard-card">
                    <div class="dashboard-content">
                        <!-- Mock Dashboard -->
                        <div class="dashboard-stats">
                            <div class="stat-card">
                                <div class="stat-value blue">$2,450</div>
                                <div class="stat-label">Ventas este mes</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value green">142</div>
                                <div class="stat-label">Pedidos</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value purple">89</div>
                                <div class="stat-label">Clientes</div>
                            </div>
                        </div>
                        
                        <!-- Mock Product List -->
                        <div class="dashboard-products">
                            <div class="products-title">Productos populares</div>
                            <div style="display: grid; gap: 0.5rem;">
                                <div class="product-item">
                                    <span>Producto A</span>
                                    <span class="product-trend">+12%</span>
                                </div>
                                <div class="product-item">
                                    <span>Servicio B</span>
                                    <span class="product-trend">+8%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gradient Effect -->
                <div class="dashboard-gradient"></div>
            </div>
        </div>
    </section>
</body>
</html>
