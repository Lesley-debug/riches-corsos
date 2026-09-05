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
      @if($puppy->color)<tr><td>Color</td><td>{{ $puppy->color }}</td></tr>@endif
    </table>
  </div>
  <div class="col-right">
    <div class="section-heading">Welcome to the Family</div>
    <p class="text-small" style="line-height:1.7;color:#1E2420;margin-bottom:10px;">
      Congratulations on your new Cane Corso puppy. This guide has been prepared by
      <strong>{{ $settings->company_name ?? 'Riches Corsos' }}</strong> to help you give
      {{ $puppy->name }} the best possible start in their new home.
      For any medical concerns, always consult a qualified veterinarian.
    </p>

    <div class="section-heading">Feeding</div>
    <p class="terms-text">
      Feed a high-quality puppy food formulated for large breeds. Puppies typically require 3–4 small meals per day up to 12 weeks, reducing to 2–3 meals per day from 3–6 months, and 2 meals per day from 6 months onwards. Always follow the feeding guidelines on your chosen food and adjust based on your puppy's growth and body condition. Fresh water must be available at all times.
    </p>

    <div class="section-heading">Exercise</div>
    <p class="terms-text">
      Young Cane Corso puppies should not be over-exercised. A general guideline is 5 minutes of structured exercise per month of age, twice daily. Free play in a safe, enclosed area is appropriate. Avoid high-impact activities such as jumping or running on hard surfaces until the puppy is fully grown (typically 18–24 months).
    </p>
  </div>
</div>

<div class="section-heading">Grooming</div>
<p class="terms-text">
  The Cane Corso has a short, dense coat that requires minimal grooming. Brush weekly with a rubber grooming mitt or soft bristle brush to remove loose hair. Bathe every 4–6 weeks or as needed. Check and clean ears regularly. Trim nails every 3–4 weeks. Begin grooming routines early so your puppy becomes comfortable with handling.
</p>

<div class="section-heading">Training &amp; Socialisation</div>
<p class="terms-text">
  Begin basic obedience training as soon as your puppy arrives home. The Cane Corso is an intelligent, eager-to-please breed that responds well to consistent, positive reinforcement. Early socialisation is essential — expose your puppy to a wide variety of people, animals, sounds, and environments during the critical socialisation window (up to 16 weeks). Enrol in a puppy class if possible.
</p>

<div class="section-heading">Veterinary Care</div>
<p class="terms-text">
  Schedule a veterinary check-up within the first few days of bringing your puppy home. Ensure vaccinations are kept up to date according to your veterinarian's schedule. Discuss flea, tick, and worming prevention with your vet. Spaying or neutering should be discussed with your veterinarian at the appropriate age.
</p>

<div class="section-heading">Safe Environment</div>
<p class="terms-text">
  Puppy-proof your home before your puppy arrives. Remove hazards such as electrical cables, toxic plants, small objects, and household chemicals. Provide a comfortable, warm sleeping area. A crate can be a valuable tool for house training and providing a safe space for your puppy.
</p>

<div class="section-heading">Contact Us</div>
<p class="terms-text">
  We are here to support you throughout your puppy's life. Please do not hesitate to reach out with any questions.
  @if($settings->phone) Phone: {{ $settings->phone }}. @endif
  @if($settings->email) Email: {{ $settings->email }}. @endif
  @if($settings->whatsapp) WhatsApp: {{ $settings->whatsapp }}. @endif
</p>

<div class="notice-box">
  <strong>Important:</strong> This guide contains general care recommendations from {{ $settings->company_name ?? 'Riches Corsos' }}.
  It is not a substitute for professional veterinary advice. Always consult a qualified veterinarian for medical concerns.
</div>

@endsection
