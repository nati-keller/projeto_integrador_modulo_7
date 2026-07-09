@props(['status'])
@php
    $status = $status instanceof \App\Enums\MargemStatus ? $status : \App\Enums\MargemStatus::from($status);
@endphp
<span class="{{ $status->badgeClass() }}">{{ $status->label() }}</span>
