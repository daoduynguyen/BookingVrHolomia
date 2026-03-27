<!DOCTYPE html>
<html>
<head>
    <title>Phản hồi từ Holomia VR</title>
</head>
<body style="font-family: 'Be Vietnam Pro', sans-serif; line-height: 1.6;">
    <h3>Xin chào {{ $customerName }},</h3>
    
    <p>Cảm ơn bạn đã liên hệ với Holomia VR. Về thắc mắc của bạn, chúng tôi xin phản hồi như sau:</p>
    
    <div style="background: #f4f4f4; padding: 15px; border-left: 4px solid #17a2b8; margin: 20px 0;">
        {!! nl2br(e($replyContent)) !!}
    </div>

    <p>Nếu cần hỗ trợ thêm, vui lòng liên hệ lại với chúng tôi.</p>
    
    <p>Trân trọng,<br>
    <strong>Đội ngũ Holomia VR</strong></p>
</body>
</html>