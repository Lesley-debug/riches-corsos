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
      <tr><td>Status</td><td>{{ ucfirst(str_replace('_',' ',$puppy->status)) }}</td></tr>
    </table>
  </div>
</div>

@if($puppy->description)
<div class="section-heading">Description</div>
<p class="text-small" style="line-height:1.7;color:#1E2420;">{{ $puppy->description }}</p>
@endif

<div class="notice-box mt-12">
  <strong>Issued by {{ $settings->company_name ?? 'Riches Corsos' }}</strong> &mdash;
  Document reference: {{ $doc->document_number }}. Issued {{ ($issuedAt ?? now())->format('d F Y') }}.
</div>

@include('pdf.components.signature', ['showBuyerLine' => false])

@endsection
