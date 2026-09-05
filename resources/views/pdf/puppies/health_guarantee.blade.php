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
      @if($puppy->color)
      <tr><td>Color</td><td>{{ $puppy->color }}</td></tr>
      @endif
      @if($puppy->microchipped && $puppy->microchip_number)
      <tr><td>Microchip</td><td>{{ $puppy->microchip_number }}</td></tr>
      @endif
    </table>

    <div class="section-heading">Health at Time of Issue</div>
    <table class="info-table">
      @if($puppy->vet_checked)
      <tr><td>Vet Checked</td><td>{{ $puppy->vet_check_date ? $puppy->vet_check_date->format('d M Y') : 'Yes' }}</td></tr>
      @endif
      @if($puppy->vaccination_status)
      <tr><td>Vaccinations</td><td>{{ ucwords(str_replace('_',' ',$puppy->vaccination_status)) }}</td></tr>
      @endif
      @if($puppy->dewormed)
      <tr><td>Dewormed</td><td>Yes</td></tr>
      @endif
      @if($puppy->microchipped)
      <tr><td>Microchipped</td><td>Yes</td></tr>
      @endif
    </table>
    @if($puppy->vaccination_notes)
    <p class="text-small text-muted mt-8">{{ $puppy->vaccination_notes }}</p>
    @endif
  </div>
</div>

@if($puppy->health_guarantee && $puppy->health_guarantee_notes)
<div class="section-heading">Guarantee Terms</div>
<div class="notice-box">{{ $puppy->health_guarantee_notes }}</div>
@else
<div class="section-heading">Health Guarantee Terms</div>
<p class="terms-text">
  <strong>{{ $settings->company_name ?? 'Riches Corsos' }}</strong> guarantees that the above-named puppy has been raised in a clean, healthy home environment and has received appropriate care prior to placement.
</p>
@endif

<div class="section-heading">Standard Terms &amp; Conditions</div>
<p class="terms-text">
  <strong>1. Coverage Period.</strong> This health guarantee covers the puppy for a period as agreed at the time of sale. Please refer to your purchase agreement for the specific coverage period.
</p>
<p class="terms-text">
  <strong>2. Genetic Conditions.</strong> Should the puppy be diagnosed with a life-threatening hereditary or congenital condition by a licensed veterinarian within the coverage period, Riches Corsos will work with the buyer to reach a fair resolution.
</p>
<p class="terms-text">
  <strong>3. Buyer Responsibilities.</strong> The buyer agrees to provide appropriate veterinary care, nutrition, exercise, and a safe living environment. This guarantee is void if the puppy is subjected to neglect, abuse, or improper care.
</p>
<p class="terms-text">
  <strong>4. Exclusions.</strong> This guarantee does not cover conditions resulting from accidents, injuries, parasites, infectious diseases, or conditions caused by the buyer's environment or care. It does not cover cosmetic conditions.
</p>
<p class="terms-text">
  <strong>5. Veterinary Verification.</strong> Any claim under this guarantee must be supported by written documentation from a licensed veterinarian.
</p>
<p class="terms-text">
  <strong>6. Limitation.</strong> This guarantee represents Riches Corsos' own contractual commitment and does not imply any external veterinary, government, or registry endorsement.
</p>

<div class="notice-box">
  <strong>Issued by {{ $settings->company_name ?? 'Riches Corsos' }}</strong> &mdash;
  This document represents a company guarantee issued by Riches Corsos. It is not a veterinary certificate.
  Health information recorded above reflects Riches Corsos' own records at the time of issue.
</div>

@include('pdf.components.signature', ['showBuyerLine' => true])

@endsection
