@extends('layouts.app')
@section('content')
<div class="text-center py-5">
    <h2>🎉 Đặt vé thành công!</h2>
    <p>Cảm ơn bạn đã đặt vé tại Holomia VR. Chúng tôi sẽ liên hệ xác nhận sớm nhất!</p>
    <a href="{{ route('ticket.shop') }}" class="btn btn-primary">Tiếp tục mua vé</a>
</div>
@endsection