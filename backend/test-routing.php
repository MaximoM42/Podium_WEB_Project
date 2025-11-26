<?php
// Test de routing y configuración

header("Content-Type: text/html; charset=UTF-8");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Test de Routing</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .info { background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #2196F3; }
        .success { background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #28a745; color: #155724; }
        .error { background: #f8d7da; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #dc3545; color: #721c24; }
        .warning { background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #ffc107; color: #856404; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        pre { background: #2d2d2d; color: #f8f8f2; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .test-section { margin: 25px 0; }
        button { background: #2196F3; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 1em; margin: 5px; }
        button:hover { background: #1976D2; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Diagnóstico de Routing</h1>

        <div class="test-section">
            <h2>📋 Información del Request</h2>
            <div class="info">
                <strong>REQUEST_URI:</strong> <code><?php echo $_SERVER['REQUEST_URI'] ?? 'No definido'; ?></code><br>
                <strong>PHP_SELF:</strong> <code><?php echo $_SERVER['PHP_SELF'] ?? 'No definido'; ?></code><br>
                <strong>SCRIPT_NAME:</strong> <code><?php echo $_SERVER['SCRIPT_NAME'] ?? 'No definido'; ?></code><br>
                <strong>REQUEST_METHOD:</strong> <code><?php echo $_SERVER['REQUEST_METHOD'] ?? 'No definido'; ?></code><br>
                <strong>QUERY_STRING:</strong> <code><?php echo $_SERVER['QUERY_STRING'] ?? 'No definido'; ?></code>
            </div>
        </div>

        <div class="test-section">
            <h2>📁 Verificación de Archivos</h2>
            <?php
            $files = [
                '.htaccess' => file_exists(__DIR__ . '/.htaccess'),
                'index.php' => file_exists(__DIR__ . '/index.php'),
                'config/config.php' => file_exists(__DIR__ . '/config/config.php'),
            ];
            
            foreach ($files as $file => $exists) {
                if ($exists) {
                    echo "<div class='success'>✅ <strong>$file</strong> existe</div>";
                } else {
                    echo "<div class='error'>❌ <strong>$file</strong> NO existe</div>";
                }
            }
            ?>
        </div>

        <div class="test-section">
            <h2>⚙️ Configuración de Apache</h2>
            <?php
            // Verificar si mod_rewrite está cargado
            if (function_exists('apache_get_modules')) {
                $modules = apache_get_modules();
                $mod_rewrite = in_array('mod_rewrite', $modules);
                
                if ($mod_rewrite) {
                    echo "<div class='success'>✅ <strong>mod_rewrite</strong> está habilitado</div>";
                } else {
                    echo "<div class='error'>❌ <strong>mod_rewrite</strong> NO está habilitado</div>";
                    echo "<div class='warning'>";
                    echo "<strong>Solución:</strong><br>";
                    echo "1. Abre <code>C:\\xampp\\apache\\conf\\httpd.conf</code><br>";
                    echo "2. Busca la línea: <code>#LoadModule rewrite_module modules/mod_rewrite.so</code><br>";
                    echo "3. Quita el <code>#</code> al principio<br>";
                    echo "4. Reinicia Apache";
                    echo "</div>";
                }
            } else {
                echo "<div class='warning'>⚠️ No se puede verificar apache_get_modules()</div>";
            }

            // Verificar AllowOverride
            echo "<div class='info'>";
            echo "<strong>ℹ️ Verifica AllowOverride:</strong><br>";
            echo "1. Abre <code>C:\\xampp\\apache\\conf\\httpd.conf</code><br>";
            echo "2. Busca <code>&lt;Directory \"C:/xampp/htdocs\"&gt;</code><br>";
            echo "3. Debe tener: <code>AllowOverride All</code> (no \"None\")";
            echo "</div>";
            ?>
        </div>

        <div class="test-section">
            <h2>🧪 Tests de Routing</h2>
            <p>Click en los botones para probar diferentes endpoints:</p>
            
            <button onclick="testEndpoint('/backend/test-routing.php')">Test Routing (Este archivo)</button>
            <button onclick="testEndpoint('/backend/')">Backend Root</button>
            <button onclick="testEndpoint('/backend/categories')">Categories</button>
            <button onclick="testEndpoint('/backend/vehicles')">Vehicles</button>
            
            <div id="test-results" style="margin-top: 20px;"></div>
        </div>

        <div class="test-section">
            <h2>📄 Contenido del .htaccess</h2>
            <?php
            $htaccess_path = __DIR__ . '/.htaccess';
            if (file_exists($htaccess_path)) {
                echo "<div class='success'>✅ .htaccess encontrado</div>";
                echo "<pre>" . htmlspecialchars(file_get_contents($htaccess_path)) . "</pre>";
            } else {
                echo "<div class='error'>❌ .htaccess NO encontrado</div>";
                echo "<div class='warning'>";
                echo "<strong>Crear archivo .htaccess con este contenido:</strong>";
                echo "<pre>RewriteEngine On

# Habilitar CORS
Header always set Access-Control-Allow-Origin \"http://localhost:5173\"
Header always set Access-Control-Allow-Methods \"GET, POST, PUT, DELETE, OPTIONS\"
Header always set Access-Control-Allow-Headers \"Content-Type, Authorization, X-Requested-With\"
Header always set Access-Control-Allow-Credentials \"true\"

# Responder a OPTIONS request
RewriteCond %{REQUEST_METHOD} OPTIONS
RewriteRule ^(.*)$ \$1 [R=200,L]

# Redirigir todo al index.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]</pre>";
                echo "</div>";
            }
            ?>
        </div>

        <div class="test-section">
            <h2>🚀 Soluciones Rápidas</h2>
            
            <div class="warning">
                <h3>Si el routing no funciona:</h3>
                <ol>
                    <li><strong>Opción 1: Habilitar mod_rewrite</strong>
                        <pre>1. Edita: C:\xampp\apache\conf\httpd.conf
2. Busca: #LoadModule rewrite_module modules/mod_rewrite.so
3. Quita el #
4. Reinicia Apache</pre>
                    </li>
                    <li><strong>Opción 2: Habilitar .htaccess</strong>
                        <pre>1. Edita: C:\xampp\apache\conf\httpd.conf
2. Busca: &lt;Directory "C:/xampp/htdocs"&gt;
3. Cambia: AllowOverride None
4. Por: AllowOverride All
5. Reinicia Apache</pre>
                    </li>
                    <li><strong>Opción 3: Acceder directamente a index.php</strong>
                        <pre>URL: http://localhost/backend/index.php?resource=categories</pre>
                    </li>
                </ol>
            </div>
        </div>
    </div>

    <script>
        async function testEndpoint(url) {
            const resultsDiv = document.getElementById('test-results');
            resultsDiv.innerHTML = '<div style="padding:10px;background:#e2e3e5;border-radius:5px;">⏳ Probando: ' + url + '</div>';
            
            try {
                const response = await fetch(url);
                const text = await response.text();
                
                let preview = text.substring(0, 200);
                if (text.length > 200) preview += '...';
                
                resultsDiv.innerHTML = `
                    <div style="padding:10px;background:#d4edda;border-radius:5px;margin-top:10px;">
                        <strong>✅ Status:</strong> ${response.status} ${response.statusText}<br>
                        <strong>URL:</strong> ${url}<br>
                        <strong>Preview:</strong><br>
                        <pre style="max-height:200px;overflow-y:auto;">${preview.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</pre>
                    </div>
                `;
            } catch (error) {
                resultsDiv.innerHTML = `
                    <div style="padding:10px;background:#f8d7da;border-radius:5px;margin-top:10px;">
                        <strong>❌ Error:</strong> ${error.message}
                    </div>
                `;
            }
        }
    </script>
</body>
</html>

