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
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Reusable Components */
        .section {
            padding: 5rem 0;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 1rem;
        }

        .section-subtitle {
            font-size: 1.25rem;
            color: hsl(var(--muted-foreground));
            text-align: center;
            max-width: 600px;
            margin: 0 auto 3rem;
        }

        .grid {
            display: grid;
            gap: 2rem;
        }

        .grid-cols-1 { grid-template-columns: repeat(1, 1fr); }
        .grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
        .grid-cols-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-cols-4 { grid-template-columns: repeat(4, 1fr); }

        /* Navbar (same as before) */
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

        /* Hero (enhanced) */
        .hero {
            padding: 8rem 0;
            background: linear-gradient(135deg, hsl(var(--background)) 0%, hsl(var(--muted) / 0.3) 100%);
        }

        .hero-content {
            text-align: center;
            max-width: 1200px;
            margin: 0 auto;
            gap: 2rem;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 9999px;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            border: 1px solid hsl(var(--border));
            background-color: hsl(var(--card));
            color: hsl(var(--card-foreground));
            margin-bottom: 2rem;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
        }

        .hero-title .gradient {
            background: linear-gradient(to right, rgb(37 99 235), rgb(6 182 212));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-description {
            font-size: 1.375rem;
            color: hsl(var(--muted-foreground));
            max-width: 600px;
            margin: 0 auto 2rem;
        }

        .hero-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-bottom: 3rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.2s;
            padding: 0.75rem 1.5rem;
            cursor: pointer;
            border: 1px solid transparent;
            text-decoration: none;
        }

        .btn-primary {
            background-color: hsl(var(--primary));
            color: hsl(var(--primary-foreground));
            box-shadow: 0 4px 6px -1px rgb(37 99 235 / 0.3);
        }

        .btn-primary:hover {
            background-color: hsl(var(--primary) / 0.9);
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgb(37 99 235 / 0.3);
        }

        .btn-secondary {
            background-color: hsl(var(--secondary));
            color: hsl(var(--secondary-foreground));
            border: 1px solid hsl(var(--border));
        }

        .btn-secondary:hover {
            background-color: hsl(var(--secondary) / 0.8);
            transform: translateY(-1px);
        }

        .hero-features {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 2rem;
            padding-top: 2rem;
        }

        .hero-feature {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1rem;
            color: hsl(var(--muted-foreground));
        }

        .feature-dot {
            width: 0.75rem;
            height: 0.75rem;
            border-radius: 50%;
        }

        .feature-dot.green { background-color: rgb(34 197 94); }
        .feature-dot.blue { background-color: rgb(37 99 235); }
        .feature-dot.purple { background-color: rgb(168 85 247); }

        /* Features Section */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .feature-card {
            background-color: hsl(var(--card));
            border: 1px solid hsl(var(--border));
            border-radius: 1rem;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1);
            border-color: hsl(var(--primary));
        }

        .feature-icon {
            width: 4rem;
            height: 4rem;
            background: linear-gradient(135deg, rgb(37 99 235), rgb(6 182 212));
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: white;
        }

        .feature-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .feature-description {
            color: hsl(var(--muted-foreground));
            line-height: 1.6;
        }

        /* Products Section */
        .products-showcase {
            background-color: hsl(var(--muted) / 0.3);
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .product-card {
            background-color: hsl(var(--card));
            border: 1px solid hsl(var(--border));
            border-radius: 1rem;
            overflow: hidden;
            transition: all 0.3s;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1);
        }

        .product-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, rgb(37 99 235 / 0.2), rgb(6 182 212 / 0.2));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: hsl(var(--primary));
        }

        .product-content {
            padding: 1.5rem;
        }

        .product-title {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .product-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: hsl(var(--primary));
            margin-bottom: 1rem;
        }

        .product-description {
            color: hsl(var(--muted-foreground));
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        /* Pricing Section */
        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .pricing-card {
            background-color: hsl(var(--card));
            border: 1px solid hsl(var(--border));
            border-radius: 1rem;
            padding: 2.5rem;
            text-align: center;
            position: relative;
            transition: all 0.3s;
        }

        .pricing-card.featured {
            border-color: hsl(var(--primary));
            transform: scale(1.05);
            box-shadow: 0 20px 25px -5px rgb(37 99 235 / 0.3);
        }

        .pricing-badge {
            position: absolute;
            top: -1rem;
            right: 1rem;
            background: linear-gradient(135deg, rgb(37 99 235), rgb(6 182 212));
            color: white;
            padding: 0.25rem 1rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .pricing-plan {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .pricing-price {
            font-size: 3rem;
            font-weight: 800;
            color: hsl(var(--primary));
            margin-bottom: 0.5rem;
        }

        .pricing-description {
            color: hsl(var(--muted-foreground));
            margin-bottom: 2rem;
        }

        .pricing-features {
            list-style: none;
            padding: 0;
            margin-bottom: 2rem;
        }

        .pricing-feature {
            padding: 0.5rem 0;
            color: hsl(var(--foreground));
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .pricing-feature::before {
            content: "✓";
            color: rgb(34 197 94);
            font-weight: 700;
        }

        /* Payments Section */
        .payments-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .payment-method {
            background-color: hsl(var(--card));
            border: 1px solid hsl(var(--border));
            border-radius: 1rem;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s;
        }

        .payment-method:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1);
        }

        .payment-icon {
            width: 4rem;
            height: 4rem;
            background: linear-gradient(135deg, rgb(37 99 235), rgb(6 182 212));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
            color: white;
        }

        .payment-title {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .payment-description {
            color: hsl(var(--muted-foreground));
            font-size: 0.875rem;
        }

        /* Testimonials */
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .testimonial-card {
            background-color: hsl(var(--card));
            border: 1px solid hsl(var(--border));
            border-radius: 1rem;
            padding: 2rem;
            position: relative;
        }

        .testimonial-quote {
            font-size: 1.125rem;
            font-style: italic;
            color: hsl(var(--muted-foreground));
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .author-avatar {
            width: 3rem;
            height: 3rem;
            background: linear-gradient(135deg, rgb(37 99 235), rgb(6 182 212));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .author-info {
            flex: 1;
        }

        .author-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .author-title {
            color: hsl(var(--muted-foreground));
            font-size: 0.875rem;
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, rgb(37 99 235), rgb(6 182 212));
            color: white;
            text-align: center;
            padding: 5rem 0;
        }

        .cta-title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .cta-description {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .btn-white {
            background-color: white;
            color: rgb(37 99 235);
            font-weight: 600;
        }

        .btn-white:hover {
            background-color: hsl(var(--muted));
            transform: translateY(-2px);
        }

        /* Footer */
        .footer {
            background-color: hsl(var(--muted) / 0.5);
            padding: 3rem 0 1rem;
            border-top: 1px solid hsl(var(--border));
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 0.5rem;
        }

        .footer-links a {
            color: hsl(var(--muted-foreground));
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: hsl(var(--primary));
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid hsl(var(--border));
            color: hsl(var(--muted-foreground));
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title { font-size: 2.5rem; }
            .section-title { font-size: 2rem; }
            .cta-title { font-size: 2rem; }
            .pricing-card.featured { transform: scale(1); }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <header class="navbar">
        <a href="/" class="logo">
            <div class="logo-icon">K</div>
            Kromerce
        </a>
        <nav style="display: none;">
            <a href="#features">Features</a>
            <a href="#products">Products</a>
            <a href="#pricing">Pricing</a>
            <a href="#contact">Contact</a>
        </nav>
        <button class="btn btn-primary">Comenzar Gratis</button>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-badge">
                    <span style="margin-right: 0.5rem;">🚀</span>
                    <span>Plataforma para MiPYMES</span>
                </div>
                <h1 class="hero-title">
                    Vende tus productos y<br>
                    <span class="gradient">servicios</span> al mundo
                </h1>
                <p class="hero-description">
                    La plataforma e-commerce diseñada para MiPYMES latinas. 
                    Acepta pagos internacionales y locales, incluso desde Cuba.
                </p>
                <div class="hero-actions">
                    <button class="btn btn-primary">Comenzar Gratis</button>
                    <button class="btn btn-secondary">Ver Demo</button>
                </div>
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
                        <span>Soporte en español 24/7</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section" id="features">
        <div class="container">
            <h2 class="section-title">Características Poderosas</h2>
            <p class="section-subtitle">
                Todo lo que necesitas para vender online sin complicaciones técnicas
            </p>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🛍️</div>
                    <h3 class="feature-title">Tienda Online</h3>
                    <p class="feature-description">
                        Crea tu catálogo de productos y servicios con imágenes, descripciones y precios personalizados.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💳</div>
                    <h3 class="feature-title">Pagos Múltiples</h3>
                    <p class="feature-description">
                        Acepta tarjetas, transferencias locales y métodos específicos para cada país.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3 class="feature-title">Analytics</h3>
                    <p class="feature-description">
                        Visualiza tus ventas, clientes y productos más populares en tiempo real.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📱</div>
                    <h3 class="feature-title">Mobile First</h3>
                    <p class="feature-description">
                        Tu tienda se ve perfecta en celulares, tablets y computadoras.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔒</div>
                    <h3 class="feature-title">Seguro</h3>
                    <p class="feature-description">
                        Protección SSL y cumplimiento de normativas de pago internacionales.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🌍</div>
                    <h3 class="feature-title">Multi-país</h3>
                    <p class="feature-description">
                        Vende a clientes en diferentes países con monedas y pagos locales.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Showcase -->
    <section class="section products-showcase" id="products">
        <div class="container">
            <h2 class="section-title">Ejemplos de Productos</h2>
            <p class="section-subtitle">
                Así se ven tus productos en la plataforma Kromerce
            </p>
            <div class="product-grid">
                <div class="product-card">
                    <div class="product-image">👕</div>
                    <div class="product-content">
                        <h3 class="product-title">Camisa Personalizada</h3>
                        <div class="product-price">$25.99</div>
                        <p class="product-description">
                            Camisas de algodón orgánico con diseños únicos. Perfectas para regalar o vender.
                        </p>
                        <button class="btn btn-primary" style="width: 100%;">Ver Detalles</button>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-image">🎨</div>
                    <div class="product-content">
                        <h3 class="product-title">Curso de Arte Digital</h3>
                        <div class="product-price">$49.99</div>
                        <p class="product-description">
                            Aprende a crear arte digital desde cero. Incluye videos y materiales descargables.
                        </p>
                        <button class="btn btn-primary" style="width: 100%;">Ver Detalles</button>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-image">☕</div>
                    <div class="product-content">
                        <h3 class="product-title">Café Especial</h3>
                        <div class="product-price">$18.50</div>
                        <p class="product-description">
                            Café tostado localmente de granos seleccionados. Envío a todo el país.
                        </p>
                        <button class="btn btn-primary" style="width: 100%;">Ver Detalles</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="section" id="pricing">
        <div class="container">
            <h2 class="section-title">Planes para Cada Necesidad</h2>
            <p class="section-subtitle">
                Empieza gratis y escala a medida que creces
            </p>
            <div class="pricing-grid">
                <div class="pricing-card">
                    <h3 class="pricing-plan">Básico</h3>
                    <div class="pricing-price">Gratis</div>
                    <p class="pricing-description">Perfecto para empezar</p>
                    <ul class="pricing-features">
                        <li class="pricing-feature">Hasta 10 productos</li>
                        <li class="pricing-feature">Pagos básicos</li>
                        <li class="pricing-feature">Soporte por email</li>
                        <li class="pricing-feature">Kromerce branding</li>
                    </ul>
                    <button class="btn btn-secondary" style="width: 100%;">Comenzar Gratis</button>
                </div>
                <div class="pricing-card featured">
                    <div class="pricing-badge">Popular</div>
                    <h3 class="pricing-plan">Profesional</h3>
                    <div class="pricing-price">$29<span style="font-size: 1rem;">/mes</span></div>
                    <p class="pricing-description">Para negocios en crecimiento</p>
                    <ul class="pricing-features">
                        <li class="pricing-feature">Productos ilimitados</li>
                        <li class="pricing-feature">Todos los métodos de pago</li>
                        <li class="pricing-feature">Soporte prioritario</li>
                        <li class="pricing-feature">Sin branding</li>
                        <li class="pricing-feature">Analytics avanzado</li>
                    </ul>
                    <button class="btn btn-primary" style="width: 100%;">Prueba 14 días</button>
                </div>
                <div class="pricing-card">
                    <h3 class="pricing-plan">Enterprise</h3>
                    <div class="pricing-price">$99<span style="font-size: 1rem;">/mes</span></div>
                    <p class="pricing-description">Para grandes operaciones</p>
                    <ul class="pricing-features">
                        <li class="pricing-feature">Todo lo profesional</li>
                        <li class="pricing-feature">API personalizada</li>
                        <li class="pricing-feature">Manager dedicado</li>
                        <li class="pricing-feature">Integraciones custom</li>
                        <li class="pricing-feature">SLA garantizado</li>
                    </ul>
                    <button class="btn btn-secondary" style="width: 100%;">Contactar Ventas</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Payment Methods -->
    <section class="section products-showcase" id="payments">
        <div class="container">
            <h2 class="section-title">Métodos de Pago</h2>
            <p class="section-subtitle">
                Acepta pagos como tus clientes prefieren
            </p>
            <div class="payments-grid">
                <div class="payment-method">
                    <div class="payment-icon">💳</div>
                    <h3 class="payment-title">Tarjetas Internacionales</h3>
                    <p class="payment-description">
                        Visa, Mastercard, Amex desde cualquier país del mundo.
                    </p>
                </div>
                <div class="payment-method">
                    <div class="payment-icon">📱</div>
                    <h3 class="payment-title">Transfermóvil</h3>
                    <p class="payment-description">
                        Pagos móviles populares en Cuba y el Caribe.
                    </p>
                </div>
                <div class="payment-method">
                    <div class="payment-icon">🏦</div>
                    <h3 class="payment-title">Transferencias</h3>
                    <p class="payment-description">
                        Transferencias bancarias locales e internacionales.
                    </p>
                </div>
                <div class="payment-method">
                    <div class="payment-icon">💰</div>
                    <h3 class="payment-title">Efectivo</h3>
                    <p class="payment-description">
                        Opciones de pago en efectivo para pedidos locales.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="section" id="testimonials">
        <div class="container">
            <h2 class="section-title">Lo que dicen nuestros clientes</h2>
            <p class="section-subtitle">
                MiPYMES como tú ya están creciendo con Kromerce
            </p>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <p class="testimonial-quote">
                        "Kromerce me permitió vender mis artesanías a clientes en Europa y Estados Unidos, 
                        algo que antes era imposible con los métodos de pago tradicionales."
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">MA</div>
                        <div class="author-info">
                            <div class="author-name">María Alvarez</div>
                            <div class="author-title">Artesana • Colombia</div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <p class="testimonial-quote">
                        "La plataforma es súper fácil de usar. En menos de una hora ya tenía mi tienda online 
                        funcionando y recibiendo mis primeros pedidos."
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">JP</div>
                        <div class="author-info">
                            <div class="author-name">Juan Pérez</div>
                            <div class="author-title">Tecnólogo • México</div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <p class="testimonial-quote">
                        "El soporte para pagos locales es increíble. Mis clientes pueden pagar con Transfermóvil 
                        y yo recibo el dinero directamente en mi cuenta."
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">CR</div>
                        <div class="author-info">
                            <div class="author-name">Carmen Rodríguez</div>
                            <div class="author-title">Diseñadora • Cuba</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2 class="cta-title">¿Listo para empezar a vender?</h2>
            <p class="cta-description">
                Únete a miles de MiPYMES que ya están creciendo con Kromerce
            </p>
            <button class="btn btn-white">Comenzar Gratis Ahora</button>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Producto</h3>
                    <ul class="footer-links">
                        <li><a href="#features">Características</a></li>
                        <li><a href="#pricing">Planes</a></li>
                        <li><a href="#payments">Pagos</a></li>
                        <li><a href="#integrations">Integraciones</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Empresa</h3>
                    <ul class="footer-links">
                        <li><a href="#about">Sobre Nosotros</a></li>
                        <li><a href="#blog">Blog</a></li>
                        <li><a href="#careers">Carreras</a></li>
                        <li><a href="#contact">Contacto</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Soporte</h3>
                    <ul class="footer-links">
                        <li><a href="#help">Centro de Ayuda</a></li>
                        <li><a href="#docs">Documentación</a></li>
                        <li><a href="#api">API</a></li>
                        <li><a href="#status">Estado del Sistema</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Legal</h3>
                    <ul class="footer-links">
                        <li><a href="#privacy">Privacidad</a></li>
                        <li><a href="#terms">Términos</a></li>
                        <li><a href="#cookies">Cookies</a></li>
                        <li><a href="#compliance">Cumplimiento</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Kromerce. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>
</body>
</html>
