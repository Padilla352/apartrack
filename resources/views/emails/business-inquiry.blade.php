@component('mail::message')
# New Business Inquiry

Hello,

You have received a new inquiry regarding your business listing.

**Business Name:** {{ $business->business_name }}  

**From:** {{ $inquirer->name }}  
**Email:** {{ $inquirer->email }}

---

### Message

{{ $messageText }}

---

@component('mail::button', ['url' => route('business.show', $business->id)])
View Business Details
@endcomponent

Thank you,<br>
{{ config('app.name') }}

@endcomponent