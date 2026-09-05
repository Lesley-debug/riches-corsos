{{--
  Signature component
  Props: $settings, $signaturePath, $issuedAt, $showBuyerLine (bool)
--}}
<div class="signature-area">
  <div class="signature-col">
    @if($signaturePath)
      <img src="{{ \App\Services\PuppyDocumentService::imageDataUri($signaturePath) }}" class="signature-img" alt="Authorized Signature" />
    @else
      <div class="signature-blank"></div>
    @endif
    <div class="signature-line">
      <strong>{{ $settings->representative_name ?? 'Authorized Representative' }}</strong><br>
      {{ $settings->representative_title ?? 'Riches Corsos' }}<br>
      {{ $settings->company_name ?? 'Riches Corsos' }}<br>
      Date: {{ ($issuedAt ?? now())->format('d F Y') }}
    </div>
  </div>

  @if($showBuyerLine ?? false)
  <div class="signature-col">
    <div class="signature-blank"></div>
    <div class="signature-line">
      <strong>Buyer / New Owner</strong><br>
      Printed Name: ___________________________<br>
      Date: ___________________________
    </div>
  </div>
  @endif
</div>
