@foreach($jobcards as $jobcard)
    <li class="list-group-item d-flex justify-content-between align-items-center jobcard-row"
        ondblclick="window.location='{{ route('progress.jobcard.show', $jobcard->id) }}'">
        <div>
            <strong>#{{ $jobcard->jobcard_number }}</strong><br>
            {{ $jobcard->client->name ?? '' }}<br>
            <small>{{ $jobcard->job_date }}</small>
        </div>
    </li>
@endforeach