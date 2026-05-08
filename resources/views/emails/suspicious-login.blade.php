<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Cảnh báo đăng nhập — RecoDB</title>
</head>
<body style="margin:0;padding:0;background-color:#f0f4f8;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0f4f8;padding:40px 16px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:540px;margin:0 auto;background:#ffffff;border-radius:14px;box-shadow:0 2px 10px rgba(0,0,0,0.08),0 0 0 1px rgba(0,0,0,0.04);">

                    <!-- Logo Header -->
                    <tr>
                        <td style="padding:28px 40px 24px;border-bottom:1px solid #e8eaed;text-align:center;">
                            <img src="https://i.ibb.co/ynjxvNhx/logo-dark.jpg" alt="RecoDB"
                                style="height:30px;display:inline-block;margin:0 auto;">
                        </td>
                    </tr>

                    <!-- Alert Banner: màu khác nhau theo type -->
                    <tr>
                        <td style="padding:0;">
                            @if($type === 'locked')
                            {{-- ĐỎ: Tài khoản đã bị khóa --}}
                            <div style="background:linear-gradient(135deg,#fff1f2 0%,#ffe4e6 100%);border-bottom:2px solid #fecdd3;padding:16px 40px;">
                                <table cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="vertical-align:middle;padding-right:14px;">
                                            <div style="width:40px;height:40px;background:#ef4444;border-radius:50%;text-align:center;line-height:40px;font-size:20px;">
                                                🔒
                                            </div>
                                        </td>
                                        <td style="vertical-align:middle;">
                                            <p style="margin:0;font-size:13px;font-weight:700;color:#991b1b;letter-spacing:0.3px;">TÀI KHOẢN BỊ KHÓA TẠM THỜI</p>
                                            <p style="margin:2px 0 0;font-size:12px;color:#b91c1c;">Do {{ $failedCount }} lần nhập sai mật khẩu liên tiếp</p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            @else
                            {{-- VÀNG: Cảnh báo sớm --}}
                            <div style="background:linear-gradient(135deg,#fffbeb 0%,#fef3c7 100%);border-bottom:2px solid #fde68a;padding:16px 40px;">
                                <table cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="vertical-align:middle;padding-right:14px;">
                                            <div style="width:40px;height:40px;background:#f59e0b;border-radius:50%;text-align:center;line-height:40px;font-size:20px;">
                                                ⚠
                                            </div>
                                        </td>
                                        <td style="vertical-align:middle;">
                                            <p style="margin:0;font-size:13px;font-weight:700;color:#92400e;letter-spacing:0.3px;">PHÁT HIỆN ĐĂNG NHẬP ĐÁNG NGỜ</p>
                                            <p style="margin:2px 0 0;font-size:12px;color:#b45309;">Đã có {{ $failedCount }} lần nhập sai mật khẩu</p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            @endif
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:36px 40px 28px;">

                            @if($type === 'locked')
                            <h2 style="margin:0 0 16px;font-size:20px;font-weight:700;color:#1a1a2e;line-height:1.3;">
                                Tài khoản của bạn đã bị khóa
                            </h2>
                            @else
                            <h2 style="margin:0 0 16px;font-size:20px;font-weight:700;color:#1a1a2e;line-height:1.3;">
                                Ai đó đang cố đăng nhập tài khoản của bạn
                            </h2>
                            @endif

                            <p style="margin:0 0 12px;font-size:15px;color:#3c4043;line-height:1.6;">
                                Xin chào <strong style="color:#1a1a2e;">{{ $userName }}</strong>,
                            </p>

                            <p style="margin:0 0 24px;font-size:15px;color:#3c4043;line-height:1.6;">
                                @if($type === 'locked')
                                    Tài khoản RecoDB của bạn vừa bị <strong style="color:#dc2626;">khóa tạm thời {{ $lockMinutes }} phút</strong>
                                    do có <strong>{{ $failedCount }} lần nhập sai mật khẩu liên tiếp</strong> lúc <strong>{{ $time }}</strong>.
                                @else
                                    Chúng tôi phát hiện có <strong>{{ $failedCount }} lần nhập sai mật khẩu liên tiếp</strong>
                                    trên tài khoản của bạn lúc <strong>{{ $time }}</strong>.
                                    Nếu tiếp tục sai, tài khoản sẽ bị khóa tạm thời.
                                @endif
                            </p>

                            <!-- Thông tin chi tiết -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                                <tr>
                                    <td style="background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;padding:18px 20px;">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding-bottom:10px;border-bottom:1px solid #e2e8f0;">
                                                    <p style="margin:0;font-size:12px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Địa chỉ IP</p>
                                                    <p style="margin:4px 0 0;font-size:15px;color:#374151;font-weight:500;font-family:monospace;">{{ $ip }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-top:10px;{{ $type === 'locked' ? 'padding-bottom:10px;border-bottom:1px solid #e2e8f0;' : '' }}">
                                                    <p style="margin:0;font-size:12px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Thời gian</p>
                                                    <p style="margin:4px 0 0;font-size:15px;color:#374151;font-weight:500;">{{ $time }}</p>
                                                </td>
                                            </tr>
                                            @if($type === 'locked')
                                            <tr>
                                                <td style="padding-top:10px;">
                                                    <p style="margin:0;font-size:12px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Thời gian khóa</p>
                                                    <p style="margin:4px 0 0;font-size:15px;color:#dc2626;font-weight:600;">{{ $lockMinutes }} phút</p>
                                                </td>
                                            </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Nếu là bạn -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:16px;">
                                <tr>
                                    <td style="background:#f0fdf4;border-radius:10px;border-left:4px solid #22c55e;padding:14px 16px;">
                                        <p style="margin:0;font-size:14px;font-weight:600;color:#166534;">✓ Nếu đây là bạn</p>
                                        <p style="margin:6px 0 0;font-size:13px;color:#15803d;line-height:1.5;">
                                            @if($type === 'locked')
                                                Tài khoản sẽ tự động mở khóa sau <strong>{{ $lockMinutes }} phút</strong>.
                                                Nếu bạn quên mật khẩu, hãy dùng chức năng khôi phục mật khẩu bên dưới.
                                            @else
                                                Có thể bạn đang gặp sự cố khi đăng nhập. Nếu quên mật khẩu,
                                                hãy sử dụng chức năng khôi phục mật khẩu để đặt lại.
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Nếu không phải bạn -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:32px;">
                                <tr>
                                    <td style="background:#fff1f2;border-radius:10px;border-left:4px solid #f43f5e;padding:14px 16px;">
                                        <p style="margin:0;font-size:14px;font-weight:600;color:#881337;">⚠ Nếu không phải bạn</p>
                                        <p style="margin:6px 0 0;font-size:13px;color:#9f1239;line-height:1.5;">
                                            Tài khoản của bạn đang bị tấn công. Hãy <strong>đổi mật khẩu ngay lập tức</strong>
                                            và bật xác thực hai bước (2FA) để bảo vệ tài khoản.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:32px;">
                                <tr>
                                    <td>
                                        <a href="{{ $resetUrl }}"
                                            style="display:inline-block;background:#f43f5e;color:#ffffff;text-decoration:none;padding:13px 28px;border-radius:8px;font-weight:700;font-size:15px;">
                                            Đặt lại mật khẩu ngay
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Sign off -->
                            <p style="margin:0;font-size:15px;color:#3c4043;line-height:1.6;">
                                Trân trọng,<br>
                                <strong style="color:#1a1a2e;">Đội ngũ RecoDB</strong>
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding:20px 40px;border-top:1px solid #e8eaed;border-radius:0 0 14px 14px;background:#f8fafc;">
                            <p style="margin:0;font-size:12px;color:#80868b;line-height:1.6;text-align:center;">
                                Email bảo mật được gửi tự động khi phát hiện hoạt động đáng ngờ trên tài khoản của bạn.<br>
                                © {{ date('Y') }} RecoDB · Đã đăng ký bản quyền
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
