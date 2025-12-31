<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Catalog - Shop</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #1a1a2e;
            color: #eee;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar */
        .navbar {
            background: #16213e;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #fff;
            text-decoration: none;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .navbar nav a {
            color: #eee;
            text-decoration: none;
            margin-left: 30px;
            transition: color 0.3s;
            font-weight: 500;
        }

        .navbar nav a:hover {
            color: #667eea;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 80px 20px;
            text-align: center;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .hero p {
            font-size: 1.3rem;
            color: rgba(255, 255, 255, 0.9);
            max-width: 600px;
            margin: 0 auto;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 20px;
            width: 100%;
        }

        .section-title {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 40px;
            color: #fff;
        }

        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-bottom: 40px;
        }

        .product-card {
            background: #16213e;
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        .product-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            background: #0f3460;
        }

        .product-info {
            padding: 25px;
        }

        .product-info h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #fff;
        }

        .product-price {
            font-size: 1.8rem;
            color: #667eea;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: none;
            cursor: pointer;
        }

        .button:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        /* Footer */
        .footer {
            background: #16213e;
            padding: 40px 20px;
            text-align: center;
            margin-top: auto;
            border-top: 1px solid #0f3460;
        }

        .footer p {
            color: #aaa;
            margin-bottom: 10px;
        }

        .footer a {
            color: #667eea;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 968px) {
            .product-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .hero h1 {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 640px) {
            .product-grid {
                grid-template-columns: 1fr;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .navbar nav a {
                margin-left: 15px;
            }
        }

        /* Placeholder for missing images */
        .product-card img[src=""],
        .product-card img:not([src]) {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="container">
            <a href="{{ route('landing') }}" class="logo">🛍️ Shop</a>
            <nav>
                <a href="{{ route('landing') }}">Home</a>
            </nav>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="hero">
        <h1>Welcome to Our Store</h1>
        <p>Discover amazing products with great prices. Shop now and enjoy exclusive deals!</p>
    </section>

    <!-- Main Content -->
    <main class="main-content">
        <h2 class="section-title">Featured Products</h2>

        <div class="product-grid">
            @foreach($products as $product)
                <div class="product-card">
                    <img src="/image/productcatalog/products/{{ $product->id }}.jpg" alt="{{ $product->name }}"
                        onerror="this.style.background='linear-gradient(135deg, #667eea 0%, #764ba2 100%)'; this.alt='Product Image';">

                    <div class="product-info">
                        <h3>{{ $product->name }}</h3>
                        <div class="product-price">${{ number_format($product->price, 2) }}</div>
                        <a href="{{ route('product.detail', ['productId' => $product->id]) }}" class="button">
                            View details
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2025 Shop. All rights reserved.</p>
        <p>Made with ❤️ by <a href="#">Your Company</a></p>
    </footer>
</body>

</html>