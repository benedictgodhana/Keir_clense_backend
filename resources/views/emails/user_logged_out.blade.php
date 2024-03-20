<x-mail::message>
# User Logged Out

Hello {{ $user->name }},

You have been successfully logged out of our system. Thank you for using our platform.

<x-mail::button :url="''">
Button Text
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
