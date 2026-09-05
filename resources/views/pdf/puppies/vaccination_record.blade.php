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
    <div class="section-heading">Puppy Details</div>
    <table class="info-table">
      <tr><td>Name</td><td>{{ $puppy->name }}</td></tr>
      <tr><td>Puppy ID</td><td>#{{ $puppy->id }}</td></tr>
      <tr><td>Breed</td><td>{{ $puppy->breed }}</td></tr>
      <tr><td>Sex</td><td>{{ ucfirst($puppy->sex) }}</td></tr>
      <tr><td>Date of Birth</td><td>{{ $puppy->date_of_birth->format('d F Y') }}</td></tr>
      @if($puppy->color)<tr><td>Color</td><td>{{ $puppy->color }}</td></tr>@endif
      @if($puppy->microchipped && $puppy->microchip_number)
      <tr><td>Microchip No.</td><td>{{ $puppy->microchip_number }}</td></tr>
      @endif
    </table>
  </div>
</div>

<div class="section-heading">Vaccination Status</div>
<table class="info-table-full">
  <tr>
    <th>Item</th>
    <th>Status / Detail</th>
  </tr>
  <tr>
    <td>Vaccination Status</td>
    <td>{{ $puppy->vaccination_status ? ucwords(str_replace('_', ' ', $puppy->vaccination_status)) : 'Not recorded' }}</td>
  </tr>
  <tr>
    <td>Dewormed</td>
    <td>{{ $puppy->dewormed ? 'Yes' : 'No' }}</td>
  </tr>
  <tr>
    <td>Vet Checked</td>
    <td>
      @if($puppy->vet_checked)
        Yes{{ $puppy->vet_check_date ? ' — ' . $puppy->vet_check_date->format('d M Y') : '' }}
      @else
        No
      @endif
    </td>
  </tr>
  <tr>
    <td>Microchipped</td>
    <td>
      @if($puppy->microchipped)
        Yes{{ $puppy->microchip_number ? ' — #' . $puppy->microchip_number : '' }}
      @else
        No
      @endif
    </td>
  </tr>
</table>

@if($puppy->vaccination_notes)
<div class="section-heading">Vaccination Notes</div>
<div class="notice-box">{{ $puppy->vaccination_notes }}</div>
@endif

<div class="notice-box" style="margin-top:16px;">
  <strong>Issued by {{ $settings->company_name ?? 'Riches Corsos' }}</strong> —
  This record reflects health information on file at the time of issue ({{ ($issuedAt ?? now())->format('d F Y') }}).
  The buyer is advised to schedule a vet check within 72 hours of taking the puppy home.
</div>

@include('pdf.components.signature', ['showBuyerLine' => false])

@endsection
