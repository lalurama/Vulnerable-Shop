<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $product->name }} - Product Detail</title>
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

        /* Main Content */
        .main-content {
            flex: 1;
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
            width: 100%;
        }

        .product-detail {
            background: #16213e;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .product-detail h3 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #fff;
        }

        .product-price {
            font-size: 2rem;
            color: #667eea;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .product-image {
            width: 100%;
            max-width: 500px;
            height: auto;
            border-radius: 10px;
            margin-bottom: 30px;
            background: #0f3460;
        }

        .product-description {
            margin-bottom: 30px;
        }

        .product-description label {
            display: block;
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 15px;
            color: #fff;
        }

        .product-description p {
            line-height: 1.8;
            color: #ccc;
            margin-bottom: 15px;
        }

        /* Stock Check Section */
        .stock-section {
            background: #0f3460;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .stock-section h4 {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: #fff;
        }

        .stock-form {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .stock-form select {
            flex: 1;
            min-width: 200px;
            padding: 12px;
            border: 2px solid #667eea;
            border-radius: 8px;
            font-size: 1rem;
            background: #16213e;
            color: #eee;
            cursor: pointer;
            transition: border-color 0.3s ease;
        }

        .stock-form select:focus {
            outline: none;
            border-color: #764ba2;
        }

        .button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            text-decoration: none;
            display: inline-block;
        }

        .button:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .stock-result {
            padding: 15px;
            background: #16213e;
            border-radius: 8px;
            text-align: center;
            font-weight: bold;
            color: #eee;
            min-height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stock-result.loading {
            color: #667eea;
        }

        .stock-result.success {
            background: #1e4d2b;
            color: #4ade80;
        }

        .stock-result.error {
            background: #4d1e1e;
            color: #f87171;
        }

        /* Navigation Links */
        .navigation-links {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .navigation-links a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .navigation-links a:hover {
            color: #764ba2;
            text-decoration: underline;
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
        }

        /* Responsive */
        @media (max-width: 640px) {
            .product-detail {
                padding: 20px;
            }

            .product-detail h3 {
                font-size: 2rem;
            }

            .stock-form {
                flex-direction: column;
            }

            .stock-form select {
                min-width: 100%;
            }
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

    <!-- Main Content -->
    <main class="main-content">
        <div class="product-detail">
            <h3>{{ $product->name }}</h3>

            <div class="product-price">${{ number_format($product->price, 2) }}</div>

            <img src="/image/productcatalog/products/{{ $product->id }}.jpg" alt="{{ $product->name }}"
                class="product-image"
                onerror="this.style.background='linear-gradient(135deg, #667eea 0%, #764ba2 100%)';">

            <div class="product-description">
                <label>Description:</label>
                <p>{{ $product->description }}</p>
            </div>

            <!-- Stock Check Section -->
            <div class="stock-section">
                <h4>Check Stock Availability</h4>
                <form method="POST" class="stock-form" id="stockCheckForm">
                    @csrf
                    <select name="stockApi" required>
                        <option value="">Select Store Location</option>
                        @foreach($stores as $store)
                            <option
                                value="http://localhost:8001/api/product/stock/check?productId={{ $product->id }}&storeId={{ $store->id }}">
                                {{ $store->location }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="button">Check stock</button>
                </form>
                <div class="stock-result" id="stockCheckResult"></div>
            </div>

            <!-- Navigation Links -->
            <div class="navigation-links">
                <a href="{{ route('landing') }}">← Return to list</a>
                @if($nextProduct)
                                <a href="{{ route('product.next', [
                        'currentProductId' => $product->id,
                        'path' => '/product?productId=' . $nextProduct->id
                    ]) }}">
                                    Next product →
                                </a>
                @endif
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2025 Shop. All rights reserved.</p>
    </footer>

    <script>
        window.contentType = 'application/x-www-form-urlencoded';

        function payload(data) {
            return new URLSearchParams(data).toString();
        }

        document.getElementById('stockCheckForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            checkStock('POST', '{{ route('product.stock.check') }}', formData);
        });

        function checkStock(method, path, data) {
            const resultElement = document.getElementById('stockCheckResult');
            resultElement.className = 'stock-result loading';
            resultElement.innerHTML = 'Checking stock...';

            const retry = (tries) => {
                if (tries === 0) {
                    resultElement.className = 'stock-result error';
                    resultElement.innerHTML = 'Could not fetch stock levels!';
                    return null;
                }

                return fetch(path, {
                    method: method,
                    headers: {
                        'Content-Type': window.contentType,
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: payload(data)
                })
                    .then(res => {
                        if (res.status === 200) {
                            return res.text().then(t => {
                                const text = isNaN(t) ? t : t + ' units';
                                resultElement.className = 'stock-result success';
                                resultElement.innerHTML = '✓ ' + text;
                                return text;
                            });
                        } else {
                            throw new Error('Not 200 status');
                        }
                    })
                    .catch(e => {
                        console.error('Attempt failed, retrying...', e);
                        return retry(tries - 1);
                    });
            };

            retry(3);
        }
    </script>
</body>

</html>