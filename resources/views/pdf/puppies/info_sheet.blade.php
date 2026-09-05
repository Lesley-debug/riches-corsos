@extends('pdf.layouts.riches-corsos')

@section('content')

<div class="two-col">
  {{-- LEFT: Photo + quick stats --}}
  <div class="col-left">
    <div class="puppy-photo-wrap">
      @if($puppyImagePath)
        <img src="{{ \App\Services\PuppyDocumentService::imageDataUri($puppyImagePath) }}" alt="{{ $puppy->name }}" />
      @else
        <div class="puppy-photo-placeholder">No photograph on file</div>
      @endif
    </div>

    <div class="section-heading">Quick Reference</div>
    <table class="info-table">
      <tr><td>Puppy ID</td><td>#{{ $puppy->id }}</td></tr>
      <tr><td>Status</td><td>{{ ucfirst(str_replace('_',' ',$puppy->status)) }}</td></tr>
      @if($puppy->price)
      <tr><td>Price</td><td>${{ number_format($puppy->price, 0) }}</td></tr>
      @endif
      @if($puppy->deposit_required && $puppy->deposit_amount)
      <tr><td>Deposit</td><td>${{ number_format($puppy->deposit_amount, 0) }}</td></tr>
      @endif
      @if($puppy->available_date)
      <tr><td>Available</td><td>{{ $puppy->available_date->format('d M Y') }}</td></tr>
      @endif
    </table>

    @if($puppy->badges && count($puppy->badges))
    <div class="mt-12">
      @foreach($puppy->badges as $badge)
        <span class="badge">{{ str_replace('_',' ',$badge) }}</span>
      @endforeach
    </div>
    @endif
  </div>

  {{-- RIGHT: Core details --}}
  <div class="col-right">
    <div class="puppy-name">{{ $puppy->name }}</div>
    <div class="puppy-breed-line">{{ $puppy->breed }} &middot; {{ ucfirst($puppy->sex) }}</div>

    <div class="section-heading">Puppy Details</div>
    <table class="info-table">
      <tr><td>Date of Birth</td><td>{{ $puppy->date_of_birth->format('d F Y') }}</td></tr>
      <tr><td>Age</td><td>{{ $puppy->age_in_weeks }} weeks</td></tr>
      <tr><td>Breed</td><td>{{ $puppy->breed }}</td></tr>
      <tr><td>Sex</td><td>{{ ucfirst($puppy->sex) }}</td></tr>
      @if($puppy->color)
      <tr><td>Color</td><td>{{ $puppy->color }}</td></tr>
      @endif
      @if($puppy->markings)
      <tr><td>Markings</td><td>{{ $puppy->markings }}</td></tr>
      @endif
      @if($puppy->weight)
      <tr><td>Current Weight</td><td>{{ $puppy->weight }}</td></tr>
      @endif
      @if($puppy->expected_adult_weight)
      <tr><td>Expected Adult</td><td>{{ $puppy->expected_adult_weight }}</td></tr>
      @endif
      @if($puppy->energy_level)
      <tr><td>Energy Level</td><td>{{ ucfirst($puppy->energy_level) }}</td></tr>
      @endif
    </table>

    @if($puppy->description)
    <div class="section-heading">Description</div>
    <p class="text-small" style="line-height:1.7;color:#1E2420;">{{ $puppy->description }}</p>
    @endif

    @if($puppy->temperament && count($puppy->temperament))
    <div class="section-heading">Temperament</div>
    <div>
      @foreach($puppy->temperament as $t)
        <span class="badge">{{ $t }}</span>
      @endforeach
    </div>
    @endif

    @if($puppy->compatibility && count($puppy->compatibility))
    <div class="section-heading">Family Compatibility</div>
    <div>
      @foreach($puppy->compatibility as $c)
        <span class="badge badge-green">{{ $c }}</span>
      @endforeach
    </div>
    @endif
  </div>
</div>

{{-- Health --}}
@if($puppy->vet_checked || $puppy->vaccination_status || $puppy->dewormed || $puppy->microchipped || $puppy->health_guarantee)
<div class="section-heading">Health Information</div>
<table class="info-table-full">
  <tr>
    @if($puppy->vet_checked)
    <td><strong>Vet Checked</strong><br>
      <span class="text-muted text-small">{{ $puppy->vet_check_date ? $puppy->vet_check_date->format('d M Y') : 'Yes' }}</span>
    </td>
    @endif
    @if($puppy->vaccination_status)
    <td><strong>Vaccinations</strong><br>
      <span class="text-muted text-small">{{ ucwords(str_replace('_',' ',$puppy->vaccination_status)) }}</span>
    </td>
    @endif
    @if($puppy->dewormed)
    <td><strong>Dewormed</strong><br><span class="text-muted text-small">Yes</span></td>
    @endif
    @if($puppy->microchipped)
    <td><strong>Microchipped</strong><br><span class="text-muted text-small">Yes</span></td>
    @endif
    @if($puppy->health_guarantee)
    <td><strong>Health Guarantee</strong><br><span class="text-muted text-small">Included</span></td>
    @endif
  </tr>
</table>
@if($puppy->vaccination_notes)
  <p class="text-small text-muted mt-8">{{ $puppy->vaccination_notes }}</p>
@endif
@endif

{{-- Parents --}}
@if($sire || $dam)
<div class="section-heading">Parentage</div>
<div class="two-col">
  @if($sire)
  <div class="col-left" style="padding-right:10px;">
    <div class="parent-card">
      <div class="parent-card-header">Father (Sire)</div>
      <div class="parent-card-body">
        @if($sireImagePath)
        <div class="parent-card-img-col">
          <img src="{{ \App\Services\PuppyDocumentService::imageDataUri($sireImagePath) }}" class="parent-card-img" alt="{{ $sire->name }}" />
        </div>
        @endif
        <div class="parent-card-info-col">
          <div class="parent-card-name">{{ $sire->name }}</div>
          <div class="parent-card-meta">
            {{ $sire->breed }}
            @if($sire->color) &middot; {{ $sire->color }} @endif
            @if($sire->weight) &middot; {{ $sire->weight }} @endif
          </div>
          @if($sire->registration_number)
          <div class="parent-card-meta">Reg: {{ $sire->registration_number }}</div>
          @endif
        </div>
      </div>
    </div>
  </div>
  @endif
  @if($dam)
  <div class="col-right">
    <div class="parent-card">
      <div class="parent-card-header">Mother (Dam)</div>
      <div class="parent-card-body">
        @if($damImagePath)
        <div class="parent-card-img-col">
          <img src="{{ \App\Services\PuppyDocumentService::imageDataUri($damImagePath) }}" class="parent-card-img" alt="{{ $dam->name }}" />
        </div>
        @endif
        <div class="parent-card-info-col">
          <div class="parent-card-name">{{ $dam->name }}</div>
          <div class="parent-card-meta">
            {{ $dam->breed }}
            @if($dam->color) &middot; {{ $dam->color }} @endif
            @if($dam->weight) &middot; {{ $dam->weight }} @endif
          </div>
          @if($dam->registration_number)
          <div class="parent-card-meta">Reg: {{ $dam->registration_number }}</div>
          @endif
        </div>
      </div>
    </div>
  </div>
  @endif
</div>
@endif

<div class="notice-box mt-12">
  <strong>Issued by {{ $settings->company_name ?? 'Riches Corsos' }}</strong> &mdash;
  This document is provided for informational purposes and reflects the records held by Riches Corsos at the time of issue.
</div>

@include('pdf.components.signature', ['showBuyerLine' => false])

@endsection
