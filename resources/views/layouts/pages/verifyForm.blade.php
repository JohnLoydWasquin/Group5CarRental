@extends('layouts.app')

@section('content')
<style>
    .kyc-container {
        margin-top: 120px;
        margin-bottom: 60px;
    }
    .kyc-card {
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        border: 1px solid #e5e7eb;
    }
</style>

<div class="container kyc-container">
    <h2 class="fw-bold mb-4">Account Verification</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @include('layouts.kyc._form', ['kyc' => $kyc, 'user' => $user])
</div>
@endsection
