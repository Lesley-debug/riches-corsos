@extends('pdf.layouts.riches-corsos')

@section('content')

<div style="text-align:center; margin-bottom:18px;">
  <div style="font-size:8pt; letter-spacing:0.12em; text-transform:uppercase; color:#6E7670; margin-bottom:4px;">
    Parentage Record
  </div>
  <div style="font-size:20pt; font-weight:bold; color:#1E2420;">{{ $puppy->name }}</div>
  <div style="font-size:9pt; color:#6E7670;">{{ $puppy->breed }} &middot; {{ ucfirst($puppy->sex) }} &middot; Born {{ $puppy->date_of_birth->format('d F Y') }}</div>
</div>

{{-- Puppy row --}}
<div style="border:1px solid #2F6B4F; border-radius:6px; padding:14px; margin-bottom:18px; display:table; width:100%;">
  <div style="display:table-cell; vertical-align:middle; width:80px;">
    @if($puppyImagePath)
      <img src="{{ \App\Services\PuppyDocumentService::imageDataUri($puppyImagePath) }}" style="width:68px;height:68px;object-fit:cover;border-radius:4px;border:1px solid #E7E9E5;" alt="{{ $puppy->name }}" />
    @else
      <div style="width:68px;height:68px;background:#E9F3EC;border-radius:4px;"></div>
    @endif
  </div>
  <div style="display:table-cell; vertical-align:middle; padding-left:14px;">
    <div style="font-size:14pt; font-weight:bold; color:#1E2420;">{{ $puppy->name }}</div>
    <div style="font-size:8.5pt; color:#6E7670;">
      {{ $puppy->breed }} &middot; {{ ucfirst($puppy->sex) }}
      @if($puppy->color) &middot; {{ $puppy->color }} @endif
      &middot; ID #{{ $puppy->id }}
    </div>
    @if($puppy->microchipped && $puppy->microchip_number)
    <div style="font-size:8pt; color:#6E7670;">Microchip: {{ $puppy->microchip_number }}</div>
    @endif
  </div>
</div>

@if($sire || $dam)
<div class="section-heading">Parents</div>
<div class="two-col">
  {{-- SIRE --}}
  <div class="col-left" style="padding-right:12px;">
    @if($sire)
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
          <div class="parent-card-meta">{{ $sire->breed }}@if($sire->color) &middot; {{ $sire->color }}@endif</div>
          @if($sire->date_of_birth)<div class="parent-card-meta">Born: {{ $sire->date_of_birth->format('d M Y') }}</div>@endif
          @if($sire->weight)<div class="parent-card-meta">Weight: {{ $sire->weight }}</div>@endif
          @if($sire->registration_organization)<div class="parent-card-meta">Reg: {{ $sire->registration_organization }}</div>@endif
          @if($sire->registration_number)<div class="parent-card-meta">Reg No: {{ $sire->registration_number }}</div>@endif
          @if($sire->titles && count($sire->titles))
          <div class="mt-8">
            @foreach($sire->titles as $title)
              <span class="badge">{{ $title }}</span>
            @endforeach
          </div>
          @endif
        </div>
      </div>
    </div>
    @if($sire->health_tests && count($sire->health_tests))
    <div class="section-heading">Sire Health Tests</div>
    <table class="info-table-full">
      <tr><th>Test</th><th>Result</th></tr>
      @foreach($sire->health_tests as $test => $result)
      <tr><td>{{ $test }}</td><td>{{ $result }}</td></tr>
      @endforeach
    </table>
    @endif
    @if($sire->description)
    <p class="text-small text-muted mt-8" style="line-height:1.6;">{{ $sire->description }}</p>
    @endif
    @else
    <div class="notice-box">Sire information not recorded.</div>
    @endif
  </div>

  {{-- DAM --}}
  <div class="col-right">
    @if($dam)
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
          <div class="parent-card-meta">{{ $dam->breed }}@if($dam->color) &middot; {{ $dam->color }}@endif</div>
          @if($dam->date_of_birth)<div class="parent-card-meta">Born: {{ $dam->date_of_birth->format('d M Y') }}</div>@endif
          @if($dam->weight)<div class="parent-card-meta">Weight: {{ $dam->weight }}</div>@endif
          @if($dam->registration_organization)<div class="parent-card-meta">Reg: {{ $dam->registration_organization }}</div>@endif
          @if($dam->registration_number)<div class="parent-card-meta">Reg No: {{ $dam->registration_number }}</div>@endif
          @if($dam->titles && count($dam->titles))
          <div class="mt-8">
            @foreach($dam->titles as $title)
              <span class="badge">{{ $title }}</span>
            @endforeach
          </div>
          @endif
        </div>
      </div>
    </div>
    @if($dam->health_tests && count($dam->health_tests))
    <div class="section-heading">Dam Health Tests</div>
    <table class="info-table-full">
      <tr><th>Test</th><th>Result</th></tr>
      @foreach($dam->health_tests as $test => $result)
      <tr><td>{{ $test }}</td><td>{{ $result }}</td></tr>
      @endforeach
    </table>
    @endif
    @if($dam->description)
    <p class="text-small text-muted mt-8" style="line-height:1.6;">{{ $dam->description }}</p>
    @endif
    @else
    <div class="notice-box">Dam information not recorded.</div>
    @endif
  </div>
</div>
@else
<div class="notice-box">No parent information has been recorded for this puppy.</div>
@endif

<div class="notice-box mt-12">
  <strong>Issued by {{ $settings->company_name ?? 'Riches Corsos' }}</strong> &mdash;
  This parentage record reflects information held by Riches Corsos. It does not constitute official pedigree registration
  unless a separate registration certificate from a recognised kennel club is provided.
</div>

@include('pdf.components.signature', ['showBuyerLine' => false])

@endsection
