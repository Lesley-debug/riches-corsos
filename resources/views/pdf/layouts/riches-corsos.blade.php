<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>{{ $doc->title ?? 'Riches Corsos Document' }}</title>
<style>
  /* ── Reset ── */
  * { margin: 0; padding: 0; box-sizing: border-box; }

  /* ── Page ── */
  body {
    font-family: 'DejaVu Sans', Arial, sans-serif;
    font-size: 10pt;
    color: #1E2420;
    background: #ffffff;
    line-height: 1.5;
  }

  /* ── Layout ── */
  .page {
    width: 100%;
    min-height: 100%;
    padding: 0;
  }

  /* ── Header ── */
  .doc-header {
    border-bottom: 2px solid #2F6B4F;
    padding: 22px 36px 18px;
    display: table;
    width: 100%;
  }
  .doc-header-logo-col {
    display: table-cell;
    vertical-align: middle;
    width: 90px;
  }
  .doc-header-logo {
    width: 72px;
    height: auto;
  }
  .doc-header-brand-col {
    display: table-cell;
    vertical-align: middle;
    padding-left: 14px;
  }
  .doc-header-company {
    font-size: 16pt;
    font-weight: bold;
    color: #1E2420;
    letter-spacing: 0.04em;
    text-transform: uppercase;
  }
  .doc-header-tagline {
    font-size: 8pt;
    color: #6E7670;
    margin-top: 2px;
    letter-spacing: 0.02em;
  }
  .doc-header-contact-col {
    display: table-cell;
    vertical-align: middle;
    text-align: right;
    font-size: 7.5pt;
    color: #6E7670;
    line-height: 1.6;
  }
  .doc-header-contact-col a {
    color: #6E7670;
    text-decoration: none;
  }

  /* ── Document title band ── */
  .doc-title-band {
    background: #2F6B4F;
    color: #ffffff;
    padding: 10px 36px;
    display: table;
    width: 100%;
  }
  .doc-title-band-left {
    display: table-cell;
    vertical-align: middle;
  }
  .doc-title-text {
    font-size: 13pt;
    font-weight: bold;
    letter-spacing: 0.03em;
    text-transform: uppercase;
  }
  .doc-title-band-right {
    display: table-cell;
    vertical-align: middle;
    text-align: right;
    font-size: 7.5pt;
    color: rgba(255,255,255,0.8);
    line-height: 1.7;
  }

  /* ── Body ── */
  .doc-body {
    padding: 24px 36px;
  }

  /* ── Two-column layout ── */
  .two-col {
    display: table;
    width: 100%;
    margin-bottom: 18px;
  }
  .col-left {
    display: table-cell;
    vertical-align: top;
    width: 38%;
    padding-right: 18px;
  }
  .col-right {
    display: table-cell;
    vertical-align: top;
  }

  /* ── Puppy photo ── */
  .puppy-photo-wrap {
    width: 100%;
    border-radius: 6px;
    overflow: hidden;
    border: 1px solid #E7E9E5;
    margin-bottom: 12px;
  }
  .puppy-photo-wrap img {
    width: 100%;
    height: auto;
    display: block;
  }
  .puppy-photo-placeholder {
    width: 100%;
    height: 160px;
    background: #E9F3EC;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 8pt;
    color: #9FBBAB;
    text-align: center;
    padding: 20px;
  }

  /* ── Section heading ── */
  .section-heading {
    font-size: 7.5pt;
    font-weight: bold;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: #2F6B4F;
    border-bottom: 1px solid #E7E9E5;
    padding-bottom: 4px;
    margin-bottom: 10px;
    margin-top: 16px;
  }
  .section-heading:first-child {
    margin-top: 0;
  }

  /* ── Info table ── */
  .info-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 9pt;
  }
  .info-table td {
    padding: 5px 0;
    vertical-align: top;
    border-bottom: 1px solid #F0F2EF;
  }
  .info-table td:first-child {
    color: #6E7670;
    font-size: 8pt;
    width: 42%;
    padding-right: 8px;
  }
  .info-table td:last-child {
    font-weight: 500;
    color: #1E2420;
  }

  /* ── Full-width info table ── */
  .info-table-full {
    width: 100%;
    border-collapse: collapse;
    font-size: 9pt;
  }
  .info-table-full td {
    padding: 5px 8px;
    vertical-align: top;
    border: 1px solid #E7E9E5;
  }
  .info-table-full th {
    padding: 6px 8px;
    background: #F4F7F5;
    font-size: 7.5pt;
    font-weight: bold;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: #2F6B4F;
    border: 1px solid #E7E9E5;
    text-align: left;
  }
  .info-table-full tr:nth-child(even) td {
    background: #FAFBFA;
  }

  /* ── Puppy name heading ── */
  .puppy-name {
    font-size: 18pt;
    font-weight: bold;
    color: #1E2420;
    margin-bottom: 2px;
    line-height: 1.1;
  }
  .puppy-breed-line {
    font-size: 9pt;
    color: #6E7670;
    margin-bottom: 14px;
  }

  /* ── Badge chip ── */
  .badge {
    display: inline-block;
    background: #E9F3EC;
    color: #234F3A;
    font-size: 7pt;
    font-weight: bold;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    padding: 3px 8px;
    border-radius: 3px;
    margin-right: 4px;
    margin-bottom: 4px;
  }
  .badge-green {
    background: #2F6B4F;
    color: #ffffff;
  }

  /* ── Notice box ── */
  .notice-box {
    border: 1px solid #E7E9E5;
    border-left: 3px solid #2F6B4F;
    background: #F9FBF9;
    padding: 10px 14px;
    font-size: 8.5pt;
    color: #1E2420;
    margin: 14px 0;
    line-height: 1.6;
  }
  .notice-box.template {
    border-left-color: #b45309;
    background: #fffbf5;
  }

  /* ── Terms text ── */
  .terms-text {
    font-size: 8pt;
    color: #6E7670;
    line-height: 1.7;
    margin-bottom: 10px;
  }
  .terms-text strong {
    color: #1E2420;
  }

  /* ── Signature area ── */
  .signature-area {
    display: table;
    width: 100%;
    margin-top: 24px;
  }
  .signature-col {
    display: table-cell;
    vertical-align: bottom;
    width: 48%;
    padding-right: 16px;
  }
  .signature-col:last-child {
    padding-right: 0;
    padding-left: 16px;
  }
  .signature-line {
    border-top: 1px solid #1E2420;
    padding-top: 6px;
    font-size: 8pt;
    color: #1E2420;
    line-height: 1.6;
  }
  .signature-img {
    max-height: 48px;
    max-width: 160px;
    margin-bottom: 4px;
    display: block;
  }
  .signature-blank {
    height: 40px;
  }

  /* ── Parent card ── */
  .parent-card {
    border: 1px solid #E7E9E5;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 12px;
  }
  .parent-card-header {
    background: #F4F7F5;
    padding: 6px 10px;
    font-size: 7.5pt;
    font-weight: bold;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #2F6B4F;
  }
  .parent-card-body {
    display: table;
    width: 100%;
    padding: 10px;
  }
  .parent-card-img-col {
    display: table-cell;
    vertical-align: top;
    width: 64px;
    padding-right: 10px;
  }
  .parent-card-img {
    width: 56px;
    height: 56px;
    object-fit: cover;
    border-radius: 4px;
    border: 1px solid #E7E9E5;
  }
  .parent-card-info-col {
    display: table-cell;
    vertical-align: top;
    font-size: 8.5pt;
  }
  .parent-card-name {
    font-weight: bold;
    font-size: 10pt;
    color: #1E2420;
    margin-bottom: 2px;
  }
  .parent-card-meta {
    color: #6E7670;
    font-size: 8pt;
    line-height: 1.5;
  }

  /* ── Footer ── */
  .doc-footer {
    border-top: 1px solid #E7E9E5;
    padding: 10px 36px;
    display: table;
    width: 100%;
    font-size: 7.5pt;
    color: #9FBBAB;
    margin-top: 24px;
  }
  .doc-footer-left {
    display: table-cell;
    vertical-align: middle;
  }
  .doc-footer-center {
    display: table-cell;
    vertical-align: middle;
    text-align: center;
  }
  .doc-footer-right {
    display: table-cell;
    vertical-align: middle;
    text-align: right;
  }

  /* ── Utility ── */
  .mt-8  { margin-top: 8px; }
  .mt-12 { margin-top: 12px; }
  .mt-16 { margin-top: 16px; }
  .mb-8  { margin-bottom: 8px; }
  .mb-12 { margin-bottom: 12px; }
  .text-muted { color: #6E7670; }
  .text-green { color: #2F6B4F; }
  .text-small { font-size: 8pt; }
  .text-bold  { font-weight: bold; }
  .w-full { width: 100%; }
  .page-break { page-break-after: always; }
</style>
</head>
<body>
<div class="page">

  {{-- ── HEADER ── --}}
  <div class="doc-header">
    <div class="doc-header-logo-col">
      @if($logoPath)
        <img src="{{ \App\Services\PuppyDocumentService::imageDataUri($logoPath) }}" class="doc-header-logo" alt="Riches Corsos" />
      @endif
    </div>
    <div class="doc-header-brand-col">
      <div class="doc-header-company">{{ $settings->company_name ?? 'Riches Corsos' }}</div>
      @if($settings->tagline)
        <div class="doc-header-tagline">{{ $settings->tagline }}</div>
      @endif
    </div>
    <div class="doc-header-contact-col">
      @if($settings->phone)  <div>{{ $settings->phone }}</div> @endif
      @if($settings->email)  <div>{{ $settings->email }}</div> @endif
      @if($settings->website)<div>{{ $settings->website }}</div>@endif
      @if($settings->address)<div>{{ $settings->address }}</div>@endif
    </div>
  </div>

  {{-- ── TITLE BAND ── --}}
  <div class="doc-title-band">
    <div class="doc-title-band-left">
      <div class="doc-title-text">{{ $doc->title }}</div>
    </div>
    <div class="doc-title-band-right">
      @if($doc->document_number)
        <div>Ref: {{ $doc->document_number }}</div>
      @endif
      <div>Issued: {{ ($issuedAt ?? now())->format('d F Y') }}</div>
    </div>
  </div>

  {{-- ── BODY ── --}}
  <div class="doc-body">
    @yield('content')
  </div>

  {{-- ── FOOTER ── --}}
  <div class="doc-footer">
    <div class="doc-footer-left">
      {{ $settings->company_name ?? 'Riches Corsos' }}
      @if($settings->email) · {{ $settings->email }} @endif
    </div>
    <div class="doc-footer-center">
      @if($doc->document_number) {{ $doc->document_number }} @endif
    </div>
    <div class="doc-footer-right">
      Issued {{ ($issuedAt ?? now())->format('d M Y') }}
    </div>
  </div>

</div>
</body>
</html>
