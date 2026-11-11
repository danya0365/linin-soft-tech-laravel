<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>External API Spec (v1) — LinenSoftTech</title>
  <meta name="description" content="API specification for LinenSoftTech: dashboard aggregates + resource reads (machines, operations, customers, etc.). How to obtain Sanctum tokens." />
  <meta name="robots" content="noindex" />
  <script type="application/json" id="api-manifest">{!! json_encode($manifest, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}</script>
  <style>
    :root {
      --bg: #0b1220; --panel: #121a2b; --panel2: #1a2438; --text: #e8eef8;
      --muted: #9aadC2; --accent: #60a5fa; --ok: #34d399;
      --warn: #fbbf24; --border: #2a3650; --code: #070d18;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0; color: var(--text); line-height: 1.55;
      font-family: "IBM Plex Sans Thai", "Sarabun", ui-sans-serif, system-ui, sans-serif;
      background:
        radial-gradient(900px 500px at 0% 0%, #1e3a5f55, transparent 55%),
        radial-gradient(800px 400px at 100% 10%, #0f766e33, transparent 50%),
        var(--bg);
    }
    .wrap { max-width: 980px; margin: 0 auto; padding: 28px 18px 96px; }
    h1 { font-size: 1.85rem; margin: 0 0 8px; letter-spacing: -0.02em; }
    h2 { font-size: 1.25rem; margin: 36px 0 12px; padding-top: 8px; border-top: 1px solid var(--border); }
    h3 { font-size: 1.05rem; margin: 22px 0 8px; color: #c7d7ee; }
    p, li { color: #d5e0f0; }
    .sub { color: var(--muted); margin: 0 0 20px; }
    a { color: var(--accent); }
    .toc { background: var(--panel); border: 1px solid var(--border); border-radius: 12px; padding: 14px 18px; margin: 18px 0 28px; }
    .toc ol { margin: 8px 0 0; padding-left: 1.2rem; }
    .card { background: var(--panel); border: 1px solid var(--border); border-radius: 12px; padding: 16px 18px; margin: 12px 0 18px; }
    .warnbox { background: rgba(251,191,36,.08); border: 1px solid rgba(251,191,36,.35); border-radius: 12px; padding: 14px 16px; margin: 14px 0; }
    .okbox { background: rgba(52,211,153,.08); border: 1px solid rgba(52,211,153,.3); border-radius: 12px; padding: 14px 16px; margin: 14px 0; }
    .steps { counter-reset: step; list-style: none; padding: 0; margin: 0; }
    .steps li {
      position: relative; padding: 12px 12px 12px 52px; margin: 0 0 8px;
      background: var(--panel2); border-radius: 10px; border: 1px solid var(--border);
    }
    .steps li::before {
      counter-increment: step; content: counter(step);
      position: absolute; left: 12px; top: 12px; width: 28px; height: 28px;
      border-radius: 999px; background: #2563eb; color: white; display: grid; place-items: center;
      font-weight: 700; font-size: .85rem;
    }
    table { width: 100%; border-collapse: collapse; background: var(--panel); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; margin: 12px 0; }
    th, td { padding: 10px 12px; border-bottom: 1px solid var(--border); text-align: left; vertical-align: top; font-size: .9rem; }
    th { background: var(--panel2); color: var(--muted); font-size: .72rem; text-transform: uppercase; letter-spacing: .04em; }
    tr:last-child td { border-bottom: 0; }
    code, pre { font-family: "IBM Plex Mono", ui-monospace, SFMono-Regular, Menlo, monospace; }
    code { background: #0a1324; padding: 1px 6px; border-radius: 5px; font-size: .86em; }
    pre {
      background: var(--code); border: 1px solid var(--border); border-radius: 10px;
      padding: 14px 16px; overflow: auto; font-size: .78rem; line-height: 1.45; color: #dbe7f7;
    }
    .method {
      display: inline-block; background: rgba(96,165,250,.18); color: #93c5fd;
      font-weight: 700; font-size: .72rem; padding: 2px 8px; border-radius: 6px; margin-right: 6px;
    }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: .75rem; font-weight: 700; }
    .badge.read { background: rgba(52,211,153,.15); color: var(--ok); }
    .badge.write { background: rgba(251,191,36,.15); color: var(--warn); }
    .badge.full { background: rgba(167,139,250,.18); color: #c4b5fd; }
    nav.top { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 18px; }
    nav.top a {
      text-decoration: none; background: var(--panel2); border: 1px solid var(--border);
      padding: 6px 12px; border-radius: 999px; font-size: .85rem;
    }
  </style>
</head>
<body>
  <div class="wrap">
    <nav class="top">
      <a href="#how-to-get-token">ขอ Token</a>
      <a href="#permissions">Permissions</a>
      <a href="#resources">Resource APIs</a>
      <a href="#machines">สถานะเครื่อง</a>
      <a href="#dashboard">Dashboard APIs</a>
      <a href="#integration-checklist">Checklist</a>
      <a href="#manifest-json">JSON Manifest</a>
      <a href="{{ $docsJsonUrl }}">docs JSON</a>
      <a href="{{ $swaggerUrl }}"><strong>Swagger Try it out</strong></a>
    </nav>

    <h1>External API Spec <span style="color:var(--muted);font-size:.7em">v1</span></h1>
    <p class="sub">
      LinenSoftTech API สำหรับโปรเจคแยก / มือถือ / AI agent<br>
      Base URL: <code>{{ $baseUrl }}</code> · Auth: Sanctum Bearer<br>
      Public docs: <code>{{ $docsUrl }}</code> · Machine JSON: <code>{{ $docsJsonUrl }}</code><br>
      สองกลุ่ม: <strong>Dashboard</strong> (<code>dashboard:read</code>) + <strong>Resources</strong> (<code>resources:read</code>)
    </p>

    <div class="okbox">
      <strong>สำหรับ AI ที่ integrate:</strong>
      fetch <code>{{ $docsJsonUrl }}</code> (แนะนำ) หรือหน้านี้แล้วอ่าน <code>script#api-manifest</code> →
      ขอ admin สร้าง token ระดับ <strong>Read only</strong> (ได้ทั้ง dashboard + resources) →
      เรียก <code>GET /api/v1/washing-machines</code> และ <code>GET /api/v1/dashboard</code>
      <br><br>
      <strong>อยากยิงเทสแบบ Swagger:</strong>
      เปิด <a href="{{ $swaggerUrl }}">{{ $swaggerUrl }}</a>
      → Authorize ด้วย Bearer token จาก Admin → Try it out
    </div>

    <div class="warnbox">
      <strong>ถาม: ให้ Swagger “ขอ token” ได้ไหม?</strong><br>
      <em>ไม่จำเป็น และไม่ควรทำแบบสาธารณะ</em> —
      ถ้ามีปุ่มออก token ในหน้า docs ที่ใครก็เข้าได้ จะเท่ากับเปิดประตู API ทั้งระบบ
      แนวทางที่ถูก (และเป็นมาตรฐาน Swagger): admin สร้าง token ที่
      <a href="{{ $tokenAdminUrl }}"><code>/admin/api-tokens</code></a>
      แล้ววางในปุ่ม <strong>Authorize</strong> ของ Swagger เท่านั้น
    </div>

    <div class="toc">
      <strong>สารบัญ</strong>
      <ol>
        <li><a href="#how-to-get-token">How to get a token</a></li>
        <li><a href="#permissions">Permission levels</a></li>
        <li><a href="#auth">Authentication</a></li>
        <li><a href="#resources">Resource read APIs (ตารางสำคัญ)</a></li>
        <li><a href="#machines">Machine status fields</a></li>
        <li><a href="#dashboard">Dashboard APIs</a></li>
        <li><a href="#errors">Errors</a></li>
        <li><a href="#integration-checklist">Checklist</a></li>
        <li><a href="#manifest-json">JSON Manifest</a></li>
      </ol>
    </div>

    <h2 id="how-to-get-token">1. How to get a token</h2>
    <div class="warnbox">
      ไม่มี self-service login แลก token — <strong>admin ต้องสร้างให้</strong> ที่
      <a href="{{ $tokenAdminUrl }}"><code>{{ $tokenAdminUrl }}</code></a>
    </div>
    <ol class="steps">
      <li>Login เว็บด้วย user ที่ <code>is_can_access_admin = true</code></li>
      <li>ไป <a href="{{ $tokenAdminUrl }}"><code>/admin/api-tokens</code></a> → <strong>สร้าง Token</strong></li>
      <li>ตั้งชื่อ เช่น <code>Partner AI App</code></li>
      <li>เลือก <strong>Read only</strong> (ได้ <code>dashboard:read</code> + <code>resources:read</code>)</li>
      <li>คัดลอก plainTextToken ครั้งเดียว (<code>{id}|{secret}</code>) เก็บใน env โปรเจคแยก</li>
      <li>ถ้า token หาย → Revoke แล้วสร้างใหม่</li>
    </ol>
    <div class="card">
<pre>LINEN_API_BASE_URL={{ $baseUrl }}
LINEN_API_TOKEN=12|paste_plain_text_here
LINEN_API_DOCS={{ $docsUrl }}
LINEN_API_DOCS_JSON={{ $docsJsonUrl }}</pre>
<pre>Authorization: Bearer 12|paste_plain_text_here
Accept: application/json</pre>
    </div>
    <div class="warnbox">
      Token เก่าที่สร้างก่อนมี <code>resources:read</code> อาจมีแค่ <code>dashboard:read</code> —
      จะเรียกตารางเครื่องจักร/ลูกค้าไม่ได้ (403) → สร้าง Read only token ใหม่
    </div>

    <h2 id="permissions">2. Permission levels</h2>
    <table>
      <thead><tr><th>UI</th><th>Abilities</th><th>คำอธิบาย</th></tr></thead>
      <tbody>
        @foreach($permissionLevels as $key => $level)
          <tr>
            <td>
              <span class="badge {{ $key === 'full' ? 'full' : ($key === 'read_write' ? 'write' : 'read') }}">
                {{ $level['label'] ?? $key }}
              </span>
              @if($key === config('api-tokens.default_permission_level'))
                <span style="color:var(--muted);font-size:.8rem">(default)</span>
              @endif
            </td>
            <td><code>{{ implode(', ', $level['abilities'] ?? []) }}</code></td>
            <td>{{ $level['description'] ?? '' }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <h2 id="auth">3. Authentication</h2>
    <div class="card">
      <ul>
        <li>ต้องใช้ personal access token จริง (session อย่างเดียว → 401)</li>
        <li>Dashboard routes: middleware <code>ability:dashboard:read</code></li>
        <li>Resource routes: middleware <code>ability:resources:read</code></li>
        <li>List response: <code>data.items</code> + <code>data.meta</code> (pagination, <code>per_page</code> สูงสุด 100)</li>
      </ul>
    </div>

    <h2 id="resources">4. Resource read APIs (ตารางสำคัญ)</h2>
    <p>ทุกเส้นด้านล่างต้องการ <code>resources:read</code> (หรือ <code>*</code>)</p>

    <h3 id="machines">4.1 สถานะเครื่อง / รถ — washing / dryer / trucks</h3>
    <div class="card">
      <p><span class="method">GET</span><code>/api/v1/washing-machines</code> · <code>/api/v1/washing-machines/{id}</code></p>
      <p><span class="method">GET</span><code>/api/v1/dryer-machines</code> · <code>/{id}</code></p>
      <p><span class="method">GET</span><code>/api/v1/trucks</code> · <code>/{id}</code></p>
      <p><strong>Query (list):</strong> <code>service_status=available|broken</code> · <code>in_use=1|0</code> · <code>per_page</code> · <code>page</code></p>
      <p><strong>สถานะที่ส่งกลับ:</strong></p>
      <ul>
        <li><code>service_status</code>: <code>available</code> | <code>broken</code></li>
        <li><code>operation_id</code>: ถ้ามีค่า = กำลังถูกใช้อยู่ในงานนั้น</li>
        <li><code>status_text</code>: <code>พร้อมใช้งาน</code> | <code>กำลังใช้งาน</code> | <code>เสีย/ซ่อม</code></li>
      </ul>
<pre>curl -sS -H "Authorization: Bearer $LINEN_API_TOKEN" \
  -H "Accept: application/json" \
  "{{ $baseUrl }}/api/v1/washing-machines?service_status=available"</pre>
<pre>{
  "success": true,
  "data": {
    "items": [
      {
        "id": 1,
        "name": "Washer A",
        "maximum_weight": 50,
        "service_status": "available",
        "operation_id": null,
        "status_text": "พร้อมใช้งาน"
      }
    ],
    "meta": { "current_page": 1, "per_page": 20, "total": 1, "last_page": 1 }
  }
}</pre>
    </div>

    <h3>4.2 Customers / groups</h3>
    <div class="card">
      <p><span class="method">GET</span><code>/api/v1/customers</code> · <code>/{id}</code> — query: <code>customer_group_id</code>, <code>q</code></p>
      <p><span class="method">GET</span><code>/api/v1/customer-groups</code> · <code>/{id}</code></p>
    </div>

    <h3>4.3 Employees / departments</h3>
    <div class="card">
      <p><span class="method">GET</span><code>/api/v1/employees</code> · <code>/{id}</code> — query: <code>department_id</code>, <code>q</code> (name/code)</p>
      <p><span class="method">GET</span><code>/api/v1/departments</code> · <code>/{id}</code></p>
      <p>Employee <strong>ไม่คืน</strong> field <code>password</code></p>
    </div>

    <h3>4.4 Linen catalog</h3>
    <div class="card">
      <p><span class="method">GET</span><code>/api/v1/linen-types</code> · <code>/{id}</code></p>
      <p><span class="method">GET</span><code>/api/v1/linen-products</code> · <code>/{id}</code> — query: <code>linen_type_id</code>, <code>q</code></p>
    </div>

    <h3>4.5 Operations (งาน)</h3>
    <div class="card">
      <p><span class="method">GET</span><code>/api/v1/operations</code> · <code>/{id}</code></p>
      <p><strong>Query:</strong> <code>status=in-progress|close</code> · <code>operation_type</code> (wash/dry/iron/packing/collect/deliver/payment) · <code>customer_id</code></p>
      <p>Show รวม relations: customer, employees ต่อสถานี, machines, truck, linenProducts + pivot weights</p>
<pre>curl -sS -H "Authorization: Bearer $LINEN_API_TOKEN" \
  -H "Accept: application/json" \
  "{{ $baseUrl }}/api/v1/operations?status=in-progress"</pre>
      <p>หมายเหตุ: <code>/api/v1/dashboard/operations</code> เป็นสรุปตัวเลขรวม — คนละเส้นกับ <code>/api/v1/operations</code> ที่เป็นรายการงาน</p>
    </div>

    <h3>4.6 Energy resources / inventories</h3>
    <div class="card">
      <p><span class="method">GET</span><code>/api/v1/energy-resources</code> · <code>/{id}</code></p>
      <p><span class="method">GET</span><code>/api/v1/inventories</code> · <code>/{id}</code> — query: <code>inventory_group_id</code>, <code>q</code></p>
    </div>

    <h2 id="dashboard">5. Dashboard APIs</h2>
    <p>ต้องการ <code>dashboard:read</code> — รายละเอียดเดิมยังใช้ได้:</p>
    <table>
      <thead><tr><th>Path</th><th>ข้อมูล</th></tr></thead>
      <tbody>
        <tr><td><code>GET /api/v1/dashboard</code></td><td>overall + operation stats</td></tr>
        <tr><td><code>GET /api/v1/dashboard/operations</code></td><td>สรุปน้ำหนัก/จำนวนงาน</td></tr>
        <tr><td><code>GET /api/v1/dashboard/sales</code></td><td>year + latest days</td></tr>
        <tr><td><code>GET /api/v1/dashboard/energy</code></td><td>summary + days</td></tr>
        <tr><td><code>GET /api/v1/dashboard/income</code></td><td>income series</td></tr>
        <tr><td><code>GET /api/v1/dashboard/expense</code></td><td>expense series</td></tr>
      </tbody>
    </table>

    <h2 id="errors">6. Errors</h2>
    <table>
      <thead><tr><th>HTTP</th><th>ความหมาย</th></tr></thead>
      <tbody>
        <tr><td>401</td><td>ไม่มี/ผิด/revoke token หรือใช้แค่ session</td></tr>
        <tr><td>403</td><td>token ไม่มี ability ที่ต้องการ</td></tr>
        <tr><td>404</td><td>ไม่พบ id</td></tr>
        <tr><td>422</td><td>query ไม่ valid</td></tr>
      </tbody>
    </table>

    <h2 id="integration-checklist">7. Checklist สำหรับ AI โปรเจคแยก</h2>
    <ol class="steps">
      <li>Fetch JSON แนะนำ: <code>GET {{ $docsJsonUrl }}</code> (หรือ HTML นี้แล้ว parse <code>#api-manifest</code>)</li>
      <li>ขอ admin สร้าง token <strong>Read only</strong> ใหม่ (ต้องมีทั้ง dashboard + resources)</li>
      <li>ตั้ง <code>LINEN_API_BASE_URL</code> + <code>LINEN_API_TOKEN</code></li>
      <li>ทดสอบ <code>GET /api/v1/washing-machines</code> และ <code>GET /api/v1/dashboard</code></li>
      <li>ดึง operations ด้วย <code>?status=in-progress</code> สำหรับบอร์ดงาน</li>
      <li>อย่า commit token ลง git</li>
    </ol>

    <h2 id="manifest-json">8. Machine-readable JSON manifest</h2>
    <p>
      แนะนำให้ AI เรียกตรงๆ: <a href="{{ $docsJsonUrl }}"><code>{{ $docsJsonUrl }}</code></a><br>
      หรืออ่านจาก <code>script#api-manifest</code> ในหน้านี้
    </p>
    <pre id="manifest-json">{{ json_encode($manifest, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) }}</pre>

    <p class="sub" style="margin-top:28px">
      Admin: <a href="{{ $tokenAdminUrl }}">{{ $tokenAdminUrl }}</a>
    </p>
  </div>
</body>
</html>
