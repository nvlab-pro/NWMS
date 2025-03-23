<div class="p-3">
    <ul class="list-group list-group-flush">
        @foreach($fields as $key => $value)
            <li class="list-group-item">
                <strong>{{ $key }}:</strong> {{ is_array($value) ? json_encode($value) : $value }}
            </li>
        @endforeach
    </ul>
</div>
