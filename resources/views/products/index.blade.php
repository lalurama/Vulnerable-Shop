<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Stock Checker - Product List</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        h1 {
            color: white;
            text-align: center;
            margin-bottom: 40px;
            font-size: 2.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .product-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        }

        .product-name {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .product-description {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .product-price {
            font-size: 1.8rem;
            color: #667eea;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .stock-checker {
            border-top: 2px solid #f0f0f0;
            padding-top: 20px;
        }

        .stock-checker h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }

        .stock-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .stock-form select {
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: border-color 0.3s ease;
        }

        .stock-form select:focus {
            outline: none;
            border-color: #667eea;
        }

        .button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .button:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .button:active {
            transform: scale(0.98);
        }

        .stock-result {
            margin-top: 15px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            text-align: center;
            font-weight: bold;
            color: #333;
            min-height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stock-result.loading {
            color: #667eea;
        }

        .stock-result.success {
            background: #d4edda;
            color: #155724;
        }

        .stock-result.error {
            background: #f8d7da;
            color: #721c24;
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }

            .products-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🛍️ Product Stock Checker</h1>

        <div class="products-grid">
            @foreach($products as $product)
                <div class="product-card">
                    <div class="product-name">{{ $product->name }}</div>
                    <div class="product-description">{{ $product->description }}</div>
                    <div class="product-price">${{ number_format($product->price, 2) }}</div>

                    <div class="stock-checker">
                        <h3>Check Stock Availability</h3>
                        <form class="stock-form stockCheckForm" data-product-id="{{ $product->id }}">
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
                            <button type="submit" class="button">Check Stock</button>
                        </form>
                        <div class="stock-result" id="stockCheckResult-{{ $product->id }}"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        window.contentType = 'application/x-www-form-urlencoded';

        function payload(data) {
            return new URLSearchParams(data).toString();
        }

        document.querySelectorAll('.stockCheckForm').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const productId = this.getAttribute('data-product-id');
                const formData = new FormData(this);
                checkStock('POST', '{{ route('product.stock.check') }}', formData, productId);
            });
        });

        function checkStock(method, path, data, productId) {
            const resultElement = document.getElementById('stockCheckResult-' + productId);
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