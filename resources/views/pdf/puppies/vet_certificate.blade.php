@extends('pdf.layouts.riches-corsos')

@section('content')

<div class="notice-box template" style="margin-bottom:16px;">
  <strong>Template — This is NOT a completed veterinary certificate.</strong>
  This document has been prepared by {{ $settings->company_name ?? 'Riches Corsos' }} as a blank template.
  All examination findings must be completed and signed by a licensed veterinarian.
  This document has no medical or legal validity until completed by a qualified veterinary professional.
</div>

<div class="two-col" style="margin-bottom:14px;">
  <div class="col-left">
    <div class="puppy-photo-wrap">
      @if($puppyImagePath)
        <img src="{{ \App\Services\PuppyDocumentService::imageDataUri($puppyImagePath) }}" alt="{{ $puppy->name }}" />
      @else
        <div class="puppy-photo-placeholder">No photograph on file</div>
      @endif
    </div>
  </div>
  <div class="col-right">
    <div class="section-heading">Animal Details</div>
    <table class="info-table">
      <tr><td>Name</td><td>{{ $puppy->name }}</td></tr>
      <tr><td>Puppy ID</td><td>#{{ $puppy->id }}</td></tr>
      <tr><td>Species</td><td>Canine</td></tr>
      <tr><td>Breed</td><td>{{ $puppy->breed }}</td></tr>
      <tr><td>Sex</td><td>{{ ucfirst($puppy->sex) }}</td></tr>
      <tr><td>Date of Birth</td><td>{{ $puppy->date_of_birth->format('d F Y') }}</td></tr>
      @if($puppy->color)<tr><td>Color</td><td>{{ $puppy->color }}</td></tr>@endif
      @if($puppy->microchipped && $puppy->microchip_number)
      <tr><td>Microchip</td><td>{{ $puppy->microchip_number }}</td></tr>
      @endif
    </table>
  </div>
</div>

<div class="section-heading">Examination Details</div>
<table class="info-table">
  <tr><td>Examination Date</td><td>___________________________</td></tr>
  <tr><td>Veterinarian Name</td><td>___________________________</td></tr>
  <tr><td>License / Reg No.</td><td>___________________________</td></tr>
  <tr><td>Veterinary Clinic</td><td>___________________________</td></tr>
  <tr><td>Clinic Address</td><td>___________________________</td></tr>
  <tr><td>Clinic Phone</td><td>___________________________</td></tr>
</table>

<div class="section-heading">Examination Findings</div>
<table class="info-table-full">
  <tr><th>System</th><th>Finding</th></tr>
  <tr><td>General Condition</td><td>___________________________</td></tr>
  <tr><td>Eyes</td><td>___________________________</td></tr>
  <tr><td>Ears</td><td>___________________________</td></tr>
  <tr><td>Cardiovascular</td><td>___________________________</td></tr>
  <tr><td>Respiratory</td><td>___________________________</td></tr>
  <tr><td>Musculoskeletal</td><td>___________________________</td></tr>
  <tr><td>Skin / Coat</td><td>___________________________</td></tr>
  <tr><td>Gastrointestinal</td><td>___________________________</td></tr>
</table>

<div class="section-heading">Health Status</div>
<div style="border:1px solid #E7E9E5; border-radius:4px; padding:10px; min-height:40px; font-size:8.5pt; color:#6E7670;">
  ___________________________________________________________________________________________<br>
  ___________________________________________________________________________________________
</div>

<div class="section-heading">Treatment / Recommendations</div>
<div style="border:1px solid #E7E9E5; border-radius:4px; padding:10px; min-height:40px; font-size:8.5pt; color:#6E7670;">
  ___________________________________________________________________________________________<br>
  ___________________________________________________________________________________________
</div>

<div class="signature-area" style="margin-top:20px;">
  <div class="signature-col">
    <div class="signature-blank"></div>
    <div class="signature-line">
      <strong>Veterinarian Signature</strong><br>
      Name: ___________________________<br>
      License No.: ___________________________<br>
      Date: ___________________________
    </div>
  </div>
  <div class="signature-col">
    <div class="signature-blank"></div>
    <div class="signature-line">
      <strong>Clinic Stamp</strong><br>
      &nbsp;<br>
      &nbsp;
    </div>
  </div>
</div>

@endsection
