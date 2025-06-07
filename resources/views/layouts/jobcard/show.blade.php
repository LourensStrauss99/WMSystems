@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h3 class="mb-0">Job Card Details</h3>
                </div>
                <div class="card-body p-4">
                    <livewire:jobcard-editor :jobcard="$jobcard" />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection