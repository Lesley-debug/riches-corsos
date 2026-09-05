@extends('pdf.layouts.riches-corsos')

@section('content')

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
    <div class="section-heading">Seller</div>
    <table class="info-table">
      <tr><td>Company</td><td>{{ $settings->company_name ?? 'Riches Corsos' }}</td></tr>
      @if($settings->address)<tr><td>Address</td><td>{{ $settings->address }}</td></tr>@endif
      @if($settings->phone)<tr><td>Phone</td><td>{{ $settings->phone }}</td></tr>@endif
      @if($settings->email)<tr><td>Email</td><td>{{ $settings->email }}</td></tr>@endif
    </table>

    <div class="section-heading">Buyer</div>
    @if($order)
    <table class="info-table">
      <tr><td>Name</td><td>{{ $order->buyer_name }}</td></tr>
      <tr><td>Email</td><td>{{ $order->buyer_email }}</td></tr>
      <tr><td>Phone</td><td>{{ $order->buyer_phone }}</td></tr>
      @if($order->buyer_address)
      <tr><td>Address</td><td>{{ $order->buyer_address }}</td></tr>
      @endif
    </table>
    @else
    <table class="info-table">
      <tr><td>Name</td><td>___________________________</td></tr>
      <tr><td>Address</td><td>___________________________</td></tr>
      <tr><td>Phone</td><td>___________________________</td></tr>
    </table>
    @endif
  </div>
</div>

<div class="section-heading">Puppy Being Purchased</div>
<table class="info-table-full">
  <tr>
    <th>Name</th><th>Breed</th><th>Sex</th><th>Date of Birth</th><th>Puppy ID</th>
  </tr>
  <tr>
    <td>{{ $puppy->name }}</td>
    <td>{{ $puppy->breed }}</td>
    <td>{{ ucfirst($puppy->sex) }}</td>
    <td>{{ $puppy->date_of_birth->format('d M Y') }}</td>
    <td>#{{ $puppy->id }}</td>
  </tr>
</table>

<div class="section-heading">Financial Summary</div>
<table class="info-table-full">
  <tr>
    <th>Item</th><th>Amount</th>
  </tr>
  @if($puppy->price)
  <tr><td>Purchase Price</td><td>${{ number_format($puppy->price, 2) }}</td></tr>
  @endif
  @if($puppy->deposit_required && $puppy->deposit_amount)
  <tr><td>Deposit Paid</td><td>${{ number_format($puppy->deposit_amount, 2) }}</td></tr>
  @if($puppy->price && $puppy->deposit_amount)
  <tr><td><strong>Balance Due</strong></td><td><strong>${{ number_format($puppy->price - $puppy->deposit_amount, 2) }}</strong></td></tr>
  @endif
  @endif
  @if($order)
  <tr><td>Order Reference</td><td>#{{ $order->id }}</td></tr>
  @endif
</table>

<div class="section-heading">Terms &amp; Conditions</div>
<p class="terms-text">
  <strong>1. Sale.</strong> The Seller agrees to transfer ownership of the above-named puppy to the Buyer upon receipt of the agreed purchase price in full.
</p>
<p class="terms-text">
  <strong>2. Deposit.</strong> Any deposit paid is non-refundable unless the Seller is unable to fulfil the sale. The deposit secures the puppy and is deducted from the total purchase price.
</p>
<p class="terms-text">
  <strong>3. Health.</strong> The puppy is sold with the health guarantee described in the separate Health Guarantee document provided at the time of sale.
</p>
<p class="terms-text">
  <strong>4. Buyer Responsibilities.</strong> The Buyer agrees to provide appropriate veterinary care, nutrition, exercise, and a safe, loving home environment for the life of the puppy.
</p>
<p class="terms-text">
  <strong>5. Return Policy.</strong> Should the Buyer be unable to keep the puppy at any time, the Buyer agrees to contact Riches Corsos before rehoming. Riches Corsos reserves the right to take the puppy back rather than allow it to be sold or given to an unknown party.
</p>
<p class="terms-text">
  <strong>6. Entire Agreement.</strong> This agreement, together with the Health Guarantee, constitutes the entire agreement between the parties regarding the sale of this puppy.
</p>

<div class="notice-box">
  Agreement Date: {{ ($issuedAt ?? now())->format('d F Y') }} &nbsp;&middot;&nbsp; Document: {{ $doc->document_number }}
</div>

@include('pdf.components.signature', ['showBuyerLine' => true])

@endsection
