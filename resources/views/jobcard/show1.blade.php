@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header">
                    <h3 class="text-center font-weight-light my-4">Jobcard Details</h3>
                </div>
                <div class="card-body p-4">
                    
                    <livewire:jobcard-editor :jobcard="$jobcard" />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection