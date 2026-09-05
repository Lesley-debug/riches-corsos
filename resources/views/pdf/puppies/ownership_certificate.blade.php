@extends('pdf.layouts.riches-corsos')

@section('content')

<div style="text-align:center; margin-bottom:18px;">
  <div style="font-size:8pt; letter-spacing:0.14em; text-transform:uppercase; color:#6E7670; margin-bottom:4px;">Certificate of Ownership</div>
  <div style="font-size:8pt; color:#9FBBAB;">{{ $doc->document_number }}</div>
</div>

<div class="two-col" style="margin-bottom:18px;">
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
    <div class="section-heading">Puppy</div>
    <table class="info-table">
      <tr><td>Name</td><td>{{ $puppy->name }}</td></tr>
      <tr><td>Puppy ID</td><td>#{{ $puppy->id }}</td></tr>
      <tr><td>Breed</td><td>{{ $puppy->breed }}</td></tr>
      <tr><td>Sex</td><td>{{ ucfirst($puppy->sex) }}</td></tr>
      <tr><td>Date of Birth</td><td>{{ $puppy->date_of_birth->format('d F Y') }}</td></tr>
      @if($puppy->color)
      <tr><td>Color</td><td>{{ $puppy->color }}</td></tr>
      @endif
      @if($puppy->microchipped && $puppy->microchip_number)
      <tr><td>Microchip</td><td>{{ $puppy->microchip_number }}</td></tr>
      @endif
    </table>

    <div class="section-heading">New Owner</div>
    @if($order)
    <table class="info-table">
      <tr><td>Name</td><td>{{ $order->buyer_name }}</td></tr>
      <tr><td>Email</td><td>{{ $order->buyer_email }}</td></tr>
      <tr><td>Phone</td><td>{{ $order->buyer_phone }}</td></tr>
      @if($order->buyer_address)
      <tr><td>Address</td><td>{{ $order->buyer_address }}</td></tr>
      @endif
      <tr><td>Order Ref</td><td>#{{ $order->id }}</td></tr>
      <tr><td>Transfer Date</td><td>{{ ($issuedAt ?? now())->format('d F Y') }}</td></tr>
    </table>
    @else
    <table class="info-table">
      <tr><td>Name</td><td>___________________________</td></tr>
      <tr><td>Address</td><td>___________________________</td></tr>
      <tr><td>Transfer Date</td><td>___________________________</td></tr>
    </table>
    @endif
  </div>
</div>

<div class="notice-box">
  <strong>Ownership Statement</strong><br>
  This document confirms that ownership of the above-named puppy has been transferred from
  <strong>{{ $settings->company_name ?? 'Riches Corsos' }}</strong> to the new owner named above,
  subject to the terms and conditions agreed at the time of purchase.
  This document is issued by Riches Corsos and does not constitute official kennel club registration.
</div>

@include('pdf.components.signature', ['showBuyerLine' => true])

@endsection
