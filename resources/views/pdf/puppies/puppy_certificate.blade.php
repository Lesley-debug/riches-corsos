@extends('pdf.layouts.riches-corsos')

@section('content')

<div style="text-align:center; margin-bottom:18px;">
  <div style="font-size:8pt; letter-spacing:0.14em; text-transform:uppercase; color:#6E7670; margin-bottom:6px;">
    This certifies that
  </div>
  <div style="font-size:26pt; font-weight:bold; color:#1E2420; line-height:1.1; margin-bottom:4px;">
    {{ $puppy->name }}
  </div>
  <div style="font-size:10pt; color:#6E7670;">
    {{ $puppy->breed }} &middot; {{ ucfirst($puppy->sex) }}
    @if($puppy->color) &middot; {{ $puppy->color }} @endif
  </div>
</div>

<div style="border-top:1px solid #E7E9E5; border-bottom:1px solid #E7E9E5; padding:16px 0; margin-bottom:18px;">
  <div class="two-col">
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
      <div class="section-heading">Certificate Details</div>
      <table class="info-table">
        <tr><td>Puppy ID</td><td>#{{ $puppy->id }}</td></tr>
        <tr><td>Certificate No.</td><td>{{ $doc->document_number }}</td></tr>
        <tr><td>Date of Birth</td><td>{{ $puppy->date_of_birth->format('d F Y') }}</td></tr>
        <tr><td>Age at Issue</td><td>{{ $puppy->age_in_weeks }} weeks</td></tr>
        <tr><td>Breed</td><td>{{ $puppy->breed }}</td></tr>
        <tr><td>Sex</td><td>{{ ucfirst($puppy->sex) }}</td></tr>
        @if($puppy->color)
        <tr><td>Color</td><td>{{ $puppy->color }}</td></tr>
        @endif
        @if($puppy->microchipped && $puppy->microchip_number)
        <tr><td>Microchip</td><td>{{ $puppy->microchip_number }}</td></tr>
        @endif
        @if($sire)
        <tr><td>Sire (Father)</td><td>{{ $sire->name }}</td></tr>
        @endif
        @if($dam)
        <tr><td>Dam (Mother)</td><td>{{ $dam->name }}</td></tr>
        @endif
        <tr><td>Issued By</td><td>{{ $settings->company_name ?? 'Riches Corsos' }}</td></tr>
        <tr><td>Date Issued</td><td>{{ ($issuedAt ?? now())->format('d F Y') }}</td></tr>
      </table>

      @if($puppy->badges && count($puppy->badges))
      <div class="mt-12">
        @foreach($puppy->badges as $badge)
          <span class="badge">{{ str_replace('_',' ',$badge) }}</span>
        @endforeach
      </div>
      @endif
    </div>
  </div>
</div>

@if($puppy->description)
<div class="section-heading">About {{ $puppy->name }}</div>
<p class="text-small" style="line-height:1.7;color:#1E2420;margin-bottom:14px;">{{ $puppy->description }}</p>
@endif

<div class="notice-box">
  This certificate is issued by <strong>{{ $settings->company_name ?? 'Riches Corsos' }}</strong> and confirms the details recorded above at the time of issue.
  It does not constitute official kennel club registration unless a separate registration certificate is provided.
</div>

@include('pdf.components.signature', ['showBuyerLine' => false])

@endsection
