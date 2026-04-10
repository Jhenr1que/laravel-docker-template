<h1>{{ __('site.mail.contact_subject', ['name' => $contact['name']]) }}</h1>
<p><strong>{{ __('site.contact.form.name') }}:</strong> {{ $contact['name'] }}</p>
<p><strong>{{ __('site.contact.form.email') }}:</strong> {{ $contact['email'] }}</p>
<p><strong>{{ __('site.contact.form.company') }}:</strong> {{ $contact['company'] ?? '-' }}</p>
<p><strong>{{ __('site.contact.form.phone') }}:</strong> {{ $contact['phone'] ?? '-' }}</p>
<p><strong>{{ __('site.contact.form.message') }}:</strong></p>
<p>{{ nl2br(e($contact['message'])) }}</p>