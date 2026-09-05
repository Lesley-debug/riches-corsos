@extends('pdf.layouts.riches-corsos')

@section('content')

<div class="notice-box template" style="margin-bottom:16px;">
  <strong>Template — Requires completion by a certified laboratory.</strong>
  This document has been prepared by {{ $settings->company_name ?? 'Riches Corsos' }} as a record template.
  All test results must be provided by and verified with the testing laboratory.
  Do not treat this as a completed genetic test report.
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
      <tr><td>Breed</td><td>{{ $puppy->breed }}</td></tr>
      <tr><td>Sex</td><td>{{ ucfirst($puppy->sex) }}</td></tr>
      <tr><td>Date of Birth</td><td>{{ $puppy->date_of_birth->format('d F Y') }}</td></tr>
      @if($puppy->microchipped && $puppy->microchip_number)
      <tr><td>Microchip</td><td>{{ $puppy->microchip_number }}</td></tr>
      @endif
    </table>

    <div class="section-heading">Sample Information</div>
    <table class="info-table">
      <tr><td>Sample Reference</td><td>___________________________</td></tr>
      <tr><td>Sample Type</td><td>___________________________</td></tr>
      <tr><td>Collection Date</td><td>___________________________</td></tr>
      <tr><td>Laboratory</td><td>___________________________</td></tr>
      <tr><td>Lab Reference</td><td>___________________________</td></tr>
      <tr><td>Test Date</td><td>___________________________</td></tr>
      <tr><td>Report Date</td><td>___________________________</td></tr>
    </table>
  </div>
</div>

<div class="section-heading">Tests Performed &amp; Results</div>
<table class="info-table-full">
  <tr>
    <th>Test Name</th>
    <th>Result</th>
    <th>Status</th>
    <th>Notes</th>
  </tr>
  @for($i = 0; $i < 5; $i++)
  <tr>
    <td>___________________________</td>
    <td>_______________</td>
    <td>_______________</td>
    <td>___________________________</td>
  </tr>
  @endfor
</table>

<div class="section-heading">Laboratory Notes</div>
<div style="border:1px solid #E7E9E5;border-radius:4px;padding:10px;min-height:50px;font-size:8.5pt;color:#6E7670;">
  ___________________________________________________________________________________________<br>
  ___________________________________________________________________________________________
</div>

<div class="signature-area" style="margin-top:20px;">
  <div class="signature-col">
    <div class="signature-blank"></div>
    <div class="signature-line">
      <strong>Laboratory Representative</strong><br>
      Name: ___________________________<br>
      Title: ___________________________<br>
      Date: ___________________________
    </div>
  </div>
  <div class="signature-col">
    <div class="signature-blank"></div>
    <div class="signature-line">
      <strong>Laboratory Stamp / Seal</strong><br>
      &nbsp;<br>&nbsp;
    </div>
  </div>
</div>

@endsection
