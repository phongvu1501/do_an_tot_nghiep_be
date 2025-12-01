@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Xác thực OTP</h2>

    @if(session('success'))
        <div style="color:green">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div style="color:red">{{ session('error') }}</div>
    @endif

    <form action="{{ route('verify.otp') }}" method="POST">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <div>
            <label>Nhập mã OTP:</label>
            <input type="text" name="otp" value="{{ old('otp') }}" required>
            @error('otp')
                <div style="color:red">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit">Xác nhận OTP</button>
    </form>
</div>
@endsection
