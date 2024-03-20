<x-mail::message>
# User Logged In

Hello {{ $user->name }},

You have been successfully logged in to our system.

<x-mail::button :url="''">
Button Text
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
