<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Security Settings - IP Blacklist</title>
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
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            color: white;
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .header p {
            color: rgba(255,255,255,0.9);
            font-size: 1.1rem;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 30px;
        }

        .card h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toggle-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .toggle-info {
            flex: 1;
        }

        .toggle-info h3 {
            color: #333;
            margin-bottom: 5px;
            font-size: 1.2rem;
        }

        .toggle-info p {
            color: #666;
            font-size: 0.9rem;
        }

        .toggle-switch {
            position: relative;
            width: 60px;
            height: 34px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9rem;
            margin-top: 10px;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .info-box h4 {
            color: #1976D2;
            margin-bottom: 10px;
        }

        .info-box ul {
            margin-left: 20px;
            color: #333;
        }

        .info-box li {
            margin-bottom: 5px;
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
            transition: transform 0.2s ease;
            text-decoration: none;
            display: inline-block;
        }

        .button:hover {
            transform: scale(1.05);
        }

        .button-secondary {
            background: #6c757d;
            margin-left: 10px;
        }

        .whitelist-section {
            margin-top: 30px;
        }

        .whitelist-input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            margin-bottom: 10px;
        }

        .whitelist-list {
            list-style: none;
            padding: 0;
        }

        .whitelist-item {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .back-link {
            color: white;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
            font-size: 1.1rem;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('products.index') }}" class="back-link">← Back to Products</a>

        <div class="header">
            <h1>🛡️ Security Settings</h1>
            <p>Manage IP Blacklist & SSRF Protection</p>
        </div>

        <div id="alert" class="alert"></div>

        <div class="card">
            <h2>🔒 IP Blacklist Filter</h2>

            <div class="toggle-container">
                <div class="toggle-info">
                    <h3>Block Local/Private IPs</h3>
                    <p>Prevent SSRF attacks by blocking requests to internal networks</p>
                    <span class="status-badge" id="statusBadge">
                        @if($blacklistStatus)
                            <span class="status-active">● ACTIVE</span>
                        @else
                            <span class="status-inactive">● INACTIVE</span>
                        @endif
                    </span>
                </div>

                <label class="toggle-switch">
                    <input type="checkbox" id="blacklistToggle" {{ $blacklistStatus ? 'checked' : '' }}>
                    <span class="slider"></span>
                </label>
            </div>

            <div class="info-box">
                <h4>📋 What gets blocked when active:</h4>
                <ul>
                    <li>localhost, 127.0.0.1, ::1</li>
                    <li>Private networks: 10.x.x.x, 192.168.x.x, 172.16-31.x.x</li>
                    <li>Link-local: 169.254.x.x</li>
                    <li>Domains that resolve to private IPs</li>
                </ul>
            </div>
        </div>

        <div class="card">
            <h2>✅ IP Whitelist (Exceptions)</h2>
            <p style="color: #666; margin-bottom: 20px;">Add IPs or domains that should bypass the blacklist filter</p>

            <div class="whitelist-section">
                <input
                    type="text"
                    id="whitelistInput"
                    class="whitelist-input"
                    placeholder="e.g., localhost:8001, 192.168.1.100"
                >
                <button class="button" onclick="addToWhitelist()">Add to Whitelist</button>

                <ul class="whitelist-list" id="whitelistList">
                    @forelse($whitelist as $ip)
                    <li class="whitelist-item">
                        <span>{{ $ip }}</span>
                        <button class="button button-secondary" onclick="removeFromWhitelist('{{ $ip }}')">Remove</button>
                    </li>
                    @empty
                    <li style="color: #999; padding: 20px; text-align: center;">No whitelist entries</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="card">
            <h2>🧪 Test Blacklist</h2>
            <p style="color: #666; margin-bottom: 15px;">Test if a URL would be blocked by the current settings</p>

            <input
                type="text"
                id="testUrl"
                class="whitelist-input"
                placeholder="http://localhost:8001/api/test"
            >
            <button class="button" onclick="testUrl()">Test URL</button>

            <div id="testResult" style="margin-top: 15px;"></div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Toggle Blacklist
        document.getElementById('blacklistToggle').addEventListener('change', function() {
            const enabled = this.checked;

            fetch('/security/toggle-blacklist', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ enable: enabled })
            })
            .then(res => res.json())
            .then(data => {
                showAlert(data.message, 'success');
                updateStatusBadge(enabled);
            })
            .catch(err => {
                showAlert('Failed to update setting', 'error');
                this.checked = !enabled; // Revert
            });
        });

        // Add to Whitelist
        function addToWhitelist() {
            const input = document.getElementById('whitelistInput');
            const value = input.value.trim();

            if (!value) {
                showAlert('Please enter an IP or domain', 'error');
                return;
            }

            const currentWhitelist = Array.from(document.querySelectorAll('.whitelist-item span'))
                .map(el => el.textContent);

            if (currentWhitelist.includes(value)) {
                showAlert('Already in whitelist', 'error');
                return;
            }

            currentWhitelist.push(value);

            fetch('/security/whitelist', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ whitelist: currentWhitelist })
            })
            .then(res => res.json())
            .then(data => {
                showAlert('Added to whitelist', 'success');
                location.reload();
            })
            .catch(err => {
                showAlert('Failed to update whitelist', 'error');
            });
        }

        // Remove from Whitelist
        function removeFromWhitelist(ip) {
            const currentWhitelist = Array.from(document.querySelectorAll('.whitelist-item span'))
                .map(el => el.textContent)
                .filter(item => item !== ip);

            fetch('/security/whitelist', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ whitelist: currentWhitelist })
            })
            .then(res => res.json())
            .then(data => {
                showAlert('Removed from whitelist', 'success');
                location.reload();
            })
            .catch(err => {
                showAlert('Failed to update whitelist', 'error');
            });
        }

        // Test URL
        function testUrl() {
            const url = document.getElementById('testUrl').value.trim();
            const resultDiv = document.getElementById('testResult');

            if (!url) {
                resultDiv.innerHTML = '<div class="alert alert-error" style="display: block;">Please enter a URL</div>';
                return;
            }

            // Simulate test (you can make actual API call)
            fetch('/security/status')
            .then(res => res.json())
            .then(data => {
                const isBlacklisted = checkIfBlacklisted(url);
                const isWhitelisted = data.whitelist.some(item => url.includes(item));

                let message = '';
                let cssClass = '';

                if (!data.enabled) {
                    message = '⚠️ Blacklist is DISABLED - URL would be ALLOWED';
                    cssClass = 'alert-error';
                } else if (isWhitelisted) {
                    message = '✅ URL is WHITELISTED - Would be ALLOWED';
                    cssClass = 'alert-success';
                } else if (isBlacklisted) {
                    message = '🚫 URL would be BLOCKED by blacklist';
                    cssClass = 'alert-error';
                } else {
                    message = '✅ URL would be ALLOWED';
                    cssClass = 'alert-success';
                }

                resultDiv.innerHTML = `<div class="alert ${cssClass}" style="display: block;">${message}</div>`;
            });
        }

        // Helper: Check if URL contains blacklisted patterns
        function checkIfBlacklisted(url) {
            const blacklistPatterns = [
                'localhost', '127.0.0.1', '::1', '0.0.0.0',
                '10.', '192.168.', '172.16.', '172.17.', '172.18.',
                '172.19.', '172.20.', '172.21.', '172.22.', '172.23.',
                '172.24.', '172.25.', '172.26.', '172.27.', '172.28.',
                '172.29.', '172.30.', '172.31.', '169.254.'
            ];

            return blacklistPatterns.some(pattern => url.toLowerCase().includes(pattern));
        }

        // Update Status Badge
        function updateStatusBadge(enabled) {
            const badge = document.getElementById('statusBadge');
            if (enabled) {
                badge.innerHTML = '<span class="status-active">● ACTIVE</span>';
            } else {
                badge.innerHTML = '<span class="status-inactive">● INACTIVE</span>';
            }
        }

        // Show Alert
        function showAlert(message, type) {
            const alert = document.getElementById('alert');
            alert.className = `alert alert-${type}`;
            alert.textContent = message;
            alert.style.display = 'block';

            setTimeout(() => {
                alert.style.display = 'none';
            }, 3000);
        }
    </script>
</body>
</html>
