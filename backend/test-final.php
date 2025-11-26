<?php
require_once 'config/config.php';
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Final - Podium Backend</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 10px;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }
        .test-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .success { border-left-color: #28a745; background: #d4edda; color: #155724; }
        .error { border-left-color: #dc3545; background: #f8d7da; color: #721c24; }
        .info { border-left-color: #17a2b8; background: #d1ecf1; color: #0c5460; }
        h2 { color: #667eea; margin-bottom: 15px; font-size: 1.3em; }
        .test-result { margin: 10px 0; padding: 10px; border-radius: 5px; }
        button {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            margin: 5px;
            transition: background 0.3s;
        }
        button:hover { background: #5568d3; }
        code {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        .url-test {
            display: inline-block;
            margin: 5px 0;
            padding: 8px 12px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏁 Test Final - Podium Backend</h1>
        <p class="subtitle">Verificación completa del sistema</p>

        <!-- Test 1: Configuración -->
        <div class="test-box">
            <h2>1️⃣ Configuración</h2>
            <?php
            echo "<div class='test-result success'>";
            echo "✅ <strong>DB_HOST:</strong> " . DB_HOST . "<br>";
            echo "✅ <strong>DB_NAME:</strong> " . DB_NAME . "<br>";
            echo "✅ <strong>DB_USER:</strong> " . DB_USER . "<br>";
            echo "✅ <strong>BASE_URL:</strong> " . BASE_URL;
            echo "</div>";
            ?>
        </div>

        <!-- Test 2: Conexión MySQL -->
        <div class="test-box">
            <h2>2️⃣ Conexión a MySQL</h2>
            <?php
            try {
                $db = Database::getInstance()->getConnection();
                echo "<div class='test-result success'>";
                echo "✅ <strong>Conexión exitosa</strong> a MySQL<br>";
                
                // Contar registros
                $stmt = $db->query("SELECT 
                    (SELECT COUNT(*) FROM categories) as cats,
                    (SELECT COUNT(*) FROM vehicles) as vehs,
                    (SELECT COUNT(*) FROM races) as races,
                    (SELECT COUNT(*) FROM positions) as poss
                ");
                $counts = $stmt->fetch();
                
                echo "📊 <strong>Datos:</strong><br>";
                echo "  - Categorías: " . $counts['cats'] . "<br>";
                echo "  - Vehículos: " . $counts['vehs'] . "<br>";
                echo "  - Carreras: " . $counts['races'] . "<br>";
                echo "  - Posiciones: " . $counts['poss'];
                echo "</div>";
            } catch (Exception $e) {
                echo "<div class='test-result error'>";
                echo "❌ <strong>Error:</strong> " . $e->getMessage();
                echo "</div>";
            }
            ?>
        </div>

        <!-- Test 3: Endpoints -->
        <div class="test-box">
            <h2>3️⃣ Test de Endpoints</h2>
            <p>Click en los botones para probar cada endpoint:</p>
            
            <button onclick="testURL('/backend/categories')">Test /categories</button>
            <button onclick="testURL('/backend/vehicles')">Test /vehicles</button>
            <button onclick="testURL('/backend/races')">Test /races</button>
            <button onclick="testURL('/backend/races/TC/positions')">Test /races/TC/positions</button>
            
            <div id="endpoint-results" style="margin-top: 20px;"></div>
        </div>

        <!-- Test 4: Estructura de Archivos -->
        <div class="test-box">
            <h2>4️⃣ Archivos del Sistema</h2>
            <?php
            $files = [
                '.htaccess',
                'index.php',
                'config/config.php',
                'config/database.php',
                'controllers/CategoryController.php',
                'controllers/VehicleController.php',
                'controllers/RaceController.php',
            ];
            
            echo "<div class='test-result info'>";
            foreach ($files as $file) {
                $exists = file_exists(__DIR__ . '/' . $file);
                $icon = $exists ? '✅' : '❌';
                echo "$icon <strong>$file</strong><br>";
            }
            echo "</div>";
            ?>
        </div>

        <!-- Test 5: URLs a Probar -->
        <div class="test-box">
            <h2>5️⃣ URLs para Probar en el Navegador</h2>
            <div class="test-result info">
                <p><strong>Backend Index:</strong></p>
                <div class="url-test">http://localhost/backend/</div>
                
                <p style="margin-top:15px;"><strong>Categorías:</strong></p>
                <div class="url-test">http://localhost/backend/categories</div>
                
                <p style="margin-top:15px;"><strong>Vehículos:</strong></p>
                <div class="url-test">http://localhost/backend/vehicles</div>
                
                <p style="margin-top:15px;"><strong>Carreras:</strong></p>
                <div class="url-test">http://localhost/backend/races</div>
                
                <p style="margin-top:15px;"><strong>Documentación:</strong></p>
                <div class="url-test">http://localhost/backend/api-docs.php</div>
            </div>
        </div>

        <!-- Test 6: Frontend -->
        <div class="test-box">
            <h2>6️⃣ Iniciar Frontend</h2>
            <div class="test-result info">
                <p><strong>1. Abre una terminal en la carpeta del proyecto</strong></p>
                <p><strong>2. Ejecuta:</strong> <code>npm run dev</code></p>
                <p><strong>3. Abre:</strong> <code>http://localhost:5173</code></p>
            </div>
        </div>
    </div>

    <script>
        async function testURL(url) {
            const resultsDiv = document.getElementById('endpoint-results');
            resultsDiv.innerHTML = '<div style="padding:15px;background:#e2e3e5;border-radius:5px;">⏳ Probando: ' + url + '...</div>';
            
            try {
                const response = await fetch(url);
                const data = await response.json();
                
                const statusClass = response.ok ? 'success' : 'error';
                const icon = response.ok ? '✅' : '❌';
                
                resultsDiv.innerHTML = `
                    <div class="test-result ${statusClass}">
                        <strong>${icon} Status: ${response.status} ${response.statusText}</strong><br>
                        <strong>URL:</strong> ${url}<br>
                        <strong>Respuesta:</strong>
                        <pre style="margin-top:10px;background:#2d2d2d;color:#f8f8f2;padding:10px;border-radius:5px;max-height:200px;overflow-y:auto;">${JSON.stringify(data, null, 2)}</pre>
                    </div>
                `;
            } catch (error) {
                resultsDiv.innerHTML = `
                    <div class="test-result error">
                        <strong>❌ Error:</strong> ${error.message}<br>
                        <p style="margin-top:10px;">Verifica que Apache esté corriendo y que mod_rewrite esté habilitado.</p>
                    </div>
                `;
            }
        }
    </script>
</body>
</html>

