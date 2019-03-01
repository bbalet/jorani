<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ServiceDesk Service Swagger</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico" sizes="32x32">
    <link rel="stylesheet" type="text/css" href="assets/swagger-ui-3.20.9/swagger-ui.css">
    <script src="assets/swagger-ui-3.20.9/swagger-ui-standalone-preset.js"></script>
    <script src="assets/swagger-ui-3.20.9/swagger-ui-bundle.js"></script>
</head>
<body>

    <a href="requirements.php">Back to Requirements</a>

    <div id="swagger-ui"></div>

<script>
<?php
$baseUrl = dirname((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
$baseUrl = $baseUrl . "/api/doc";
?>
    window.onload = function() {
        const ui = SwaggerUIBundle({
        url: "<?php echo $baseUrl; ?>",
        dom_id: '#swagger-ui',
        presets: [
            SwaggerUIBundle.presets.apis,
            SwaggerUIStandalonePreset
        ],
        layout: "StandaloneLayout"
    });
    }
</script>
</body>
</html>