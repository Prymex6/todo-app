@component('mail::message')
# Task Shared With You

Hello!

{{ $ownerName }} has shared a task with you:

**{{ $task->name }}**  
Due: {{ $task->due_date->format('Y-m-d H:i') }}  
Priority: {{ $task->priority_label }}  
Status: {{ $task->status_label }}

@component('mail::button', ['url' => $shareLink])
View Shared Task
@endcomponent

@if($allowEditing)
⚠️ You have **editing** permissions for this task  
@endif

🔗 This share link will expire on {{ $expiryDate }}  

Thanks,  
{{ config('app.name') }}
@endcomponent