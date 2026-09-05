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
    <table class="info-table">
      <tr><td>Puppy</td><td>{{ $puppy->name }}</td></tr>
      <tr><td>Breed</td><td>{{ $puppy->breed }}</td></tr>
      <tr><td>Date of Birth</td><td>{{ $puppy->date_of_birth->format('d M Y') }}</td></tr>
      <tr><td>Age at Issue</td><td>{{ $puppy->age_in_weeks }} weeks</td></tr>
      @if($puppy->weight)<tr><td>Current Weight</td><td>{{ $puppy->weight }}</td></tr>@endif
      @if($puppy->expected_adult_weight)<tr><td>Expected Adult</td><td>{{ $puppy->expected_adult_weight }}</td></tr>@endif
    </table>
  </div>
  <div class="col-right">
    <div class="section-heading">Feeding Schedule by Age</div>
    <table class="info-table-full">
      <tr>
        <th>Age</th><th>Meals / Day</th><th>Portion Guidance</th>
      </tr>
      <tr><td>8 – 12 weeks</td><td>3 – 4</td><td>Follow large-breed puppy food guidelines</td></tr>
      <tr><td>3 – 6 months</td><td>3</td><td>Adjust as puppy grows; monitor body condition</td></tr>
      <tr><td>6 – 12 months</td><td>2</td><td>Transition to adult portions gradually</td></tr>
      <tr><td>12 months+</td><td>2</td><td>Adult large-breed food; consult your vet</td></tr>
    </table>

    <div class="section-heading">Food Type Recommendations</div>
    <p class="terms-text">
      Choose a high-quality dry kibble or raw diet formulated for large-breed puppies. Look for a named protein source (e.g. chicken, beef, lamb) as the first ingredient. Avoid foods with excessive fillers, artificial colours, or preservatives. If transitioning food, do so gradually over 7–10 days to avoid digestive upset.
    </p>

    <div class="section-heading">Hydration</div>
    <p class="terms-text">
      Fresh, clean water must be available at all times. Monitor water intake — excessive thirst can indicate a health issue. Change water daily and clean the bowl regularly.
    </p>
  </div>
</div>

<div class="section-heading">Foods to Avoid</div>
<table class="info-table-full">
  <tr>
    <th>Food</th><th>Reason</th>
  </tr>
  <tr><td>Chocolate, coffee, caffeine</td><td>Toxic to dogs</td></tr>
  <tr><td>Grapes, raisins, currants</td><td>Can cause kidney failure</td></tr>
  <tr><td>Onions, garlic, leeks</td><td>Toxic to dogs</td></tr>
  <tr><td>Xylitol (artificial sweetener)</td><td>Highly toxic</td></tr>
  <tr><td>Macadamia nuts</td><td>Toxic to dogs</td></tr>
  <tr><td>Cooked bones</td><td>Splintering hazard</td></tr>
  <tr><td>Alcohol</td><td>Toxic to dogs</td></tr>
  <tr><td>Raw dough / yeast</td><td>Can expand in stomach</td></tr>
</table>

<div class="section-heading">Treats &amp; Supplements</div>
<p class="terms-text">
  Treats should make up no more than 10% of daily caloric intake. Choose natural, single-ingredient treats where possible. Discuss any supplements with your veterinarian before adding them to your puppy's diet.
</p>

<div class="notice-box">
  <strong>Important:</strong> This feeding guide contains general recommendations from {{ $settings->company_name ?? 'Riches Corsos' }}.
  Portion sizes and dietary needs vary by individual puppy. Always consult a qualified veterinarian or veterinary nutritionist for personalised dietary advice.
</div>

@endsection
