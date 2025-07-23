@extends('layouts.mobile')

@section('header', 'Quotes')

@section('content')
    <h2 class="fw-bold mb-3">Quotes</h2>
    <div id="mobile-quote-list" style="display: flex; flex-direction: column; gap: 1rem;">
        @foreach($quotes as $quote)
            <div onclick="selectQuote({{ $quote->id }})" id="quote-card-{{ $quote->id }}" style="background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 1rem; display: flex; flex-direction: column; align-items: flex-start; cursor:pointer; margin-bottom: 0.5rem;">
                <div style="font-size: 1.1rem; font-weight: bold; margin-bottom: 0.5rem; color: #2563eb;">
                    {{ $quote->quote_number }}
                </div>
                <div style="font-size: 0.95rem; color: #888; margin-bottom: 0.3rem;">Date: {{ $quote->created_at->format('Y-m-d') }}</div>
                <div style="font-size: 0.95rem; color: #888; margin-bottom: 0.3rem;">Client: {{ $quote->client_name }}</div>
                <div style="font-size: 0.95rem; color: #888; margin-bottom: 0.3rem;">Address: {{ $quote->client_address }}</div>
            </div>
        @endforeach
    </div>
    <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
        <a id="mobile-quote-view" class="btn btn-info" style="flex:1; text-align:center;" href="#" disabled>View</a>
        <a id="mobile-quote-edit" class="btn btn-primary" style="flex:1; text-align:center;" href="#" disabled>Edit</a>
    </div>
    <div style="margin-top: 1rem;">
        {{ $quotes->links() }}
    </div>
    <script>
        let selectedQuoteId = null;
        function selectQuote(id) {
            // Remove highlight from all
            document.querySelectorAll('[id^=quote-card-]').forEach(card => card.style.background = '#fff');
            // Highlight selected
            document.getElementById('quote-card-' + id).style.background = '#e3f2fd';
            selectedQuoteId = id;
            // Enable buttons
            document.getElementById('mobile-quote-view').href = '/mobile/quotes/' + id;
            document.getElementById('mobile-quote-view').removeAttribute('disabled');
            document.getElementById('mobile-quote-edit').href = '/mobile/quotes/' + id + '/edit';
            document.getElementById('mobile-quote-edit').removeAttribute('disabled');
        }
    </script>
@endsection 