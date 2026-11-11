<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>External API — Swagger Try it out</title>
  <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5.17.14/swagger-ui.css" />
  <style>
    body { margin: 0; background: #0b1220; }
    .topbar { display: none; }
    .guide {
      max-width: 960px; margin: 0 auto; padding: 16px 18px 0;
      font-family: "IBM Plex Sans Thai", "Sarabun", system-ui, sans-serif; color: #e8eef8;
    }
    .guide .box {
      background: #121a2b; border: 1px solid #2a3650; border-radius: 12px;
      padding: 14px 16px; margin-bottom: 12px; line-height: 1.5; font-size: .95rem;
    }
    .guide a { color: #60a5fa; }
    .guide code { background: #0a1324; padding: 1px 6px; border-radius: 5px; font-size: .88em; }
    .guide ol { margin: 8px 0 0; padding-left: 1.2rem; }
    .guide .warn {
      background: rgba(251,191,36,.08); border-color: rgba(251,191,36,.4); color: #fde68a;
    }
    #swagger-ui { max-width: 1200px; margin: 0 auto 40px; }
  </style>
</head>
<body>
  <div class="guide">
    <div class="box">
      <strong>Swagger Try it out — External API v1</strong><br>
      เอกสารอธิบาย: <a href="{{ route('docs.external-api') }}">{{ route('docs.external-api') }}</a>
      · Manifest JSON: <a href="{{ route('docs.external-api.json') }}">{{ route('docs.external-api.json') }}</a>
      · OpenAPI: <a href="{{ route('docs.external-api.openapi') }}">{{ route('docs.external-api.openapi') }}</a>
      <ol>
        <li>ไปสร้าง token ที่ <a href="{{ url('/admin/api-tokens/create') }}">/admin/api-tokens/create</a> (ต้อง login admin) — เลือก <strong>Read only</strong></li>
        <li>คัดลอก plainTextToken ครั้งเดียว</li>
        <li>กด <strong>Authorize</strong> ด้านบนของ Swagger → วาง token → Authorize</li>
        <li>เลือก endpoint → <strong>Try it out</strong> → Execute (ยิง API จริง)</li>
      </ol>
    </div>
    <div class="box warn">
      <strong>ไม่มีปุ่ม “ขอ token” ในหน้านี้โดยเจตนา</strong> —
      ถ้า Swagger สาธารณะออก token ได้ ใครก็เรียก API ของโรงงานได้
      วิธีที่ถูกคือ admin ออก token แล้ววางใน Authorize (มาตรฐานเดียวกับ Swagger ส่วนใหญ่)
    </div>
  </div>

  <div id="swagger-ui"></div>

  <script src="https://unpkg.com/swagger-ui-dist@5.17.14/swagger-ui-bundle.js" crossorigin></script>
  <script src="https://unpkg.com/swagger-ui-dist@5.17.14/swagger-ui-standalone-preset.js" crossorigin></script>
  <script>
    window.ui = SwaggerUIBundle({
      url: @json(route('docs.external-api.openapi')),
      dom_id: '#swagger-ui',
      deepLinking: true,
      presets: [SwaggerUIBundle.presets.apis, SwaggerUIStandalonePreset],
      plugins: [SwaggerUIBundle.plugins.DownloadUrl],
      layout: 'StandaloneLayout',
      tryItOutEnabled: true,
      persistAuthorization: true,
      docExpansion: 'list',
      defaultModelsExpandDepth: -1,
      displayRequestDuration: true,
    });
  </script>
</body>
</html>
