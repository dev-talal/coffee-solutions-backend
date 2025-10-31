@component('mail::message')
# Welcome!

Your account has been created successfully.

**Email:** {{ $email }}  
**Password:** {{ $password }}

@if($isCustomer)
@component('mail::button', ['url' => 'https://play.google.com/store/apps/details?id=your.app.package'])
Download on Play Store
@endcomponent

@component('mail::button', ['url' => 'https://apps.apple.com/app/idYOUR_APP_ID', 'color' => 'success'])
Download on App Store
@endcomponent
@else
@component('mail::button', ['url' => env('FRONTEND_URL')])
Access Your Account
@endcomponent
@endif

Please log in and change your password after your first login.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
