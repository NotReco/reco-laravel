<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Cảnh báo thay đổi email — RecoDB</title>
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

                    <!-- Security Alert Banner -->
                    <tr>
                        <td style="padding:0;">
                            <div style="background:linear-gradient(135deg,#fff7ed 0%,#ffedd5 100%);border-bottom:2px solid #fed7aa;padding:16px 40px;display:flex;align-items:center;gap:10px;">
                                <table cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="vertical-align:middle;padding-right:12px;">
                                            <!-- Warning icon -->
                                            <div style="width:36px;height:36px;background:#f97316;border-radius:50%;display:flex;align-items:center;justify-content:center;text-align:center;line-height:36px;font-size:18px;">
                                                ⚠
                                            </div>
                                        </td>
                                        <td style="vertical-align:middle;">
                                            <p style="margin:0;font-size:13px;font-weight:700;color:#9a3412;letter-spacing:0.3px;">CẢNH BÁO BẢO MẬT</p>
                                            <p style="margin:2px 0 0;font-size:12px;color:#c2410c;">Hoạt động tài khoản cần xem xét</p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:36px 40px 28px;">

                            <h2 style="margin:0 0 16px;font-size:20px;font-weight:700;color:#1a1a2e;line-height:1.3;">
                                Địa chỉ email đã được thay đổi
                            </h2>

                            <p style="margin:0 0 12px;font-size:15px;color:#3c4043;line-height:1.6;">
                                Xin chào <strong style="color:#1a1a2e;">{{ $userName }}</strong>,
                            </p>

                            <p style="margin:0 0 24px;font-size:15px;color:#3c4043;line-height:1.6;">
                                Chúng tôi ghi nhận rằng địa chỉ email liên kết với tài khoản RecoDB của bạn
                                vừa được thay đổi lúc <strong style="color:#1a1a2e;">{{ now()->format('H:i, d/m/Y') }}</strong>.
                            </p>

                            <!-- Change detail box -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                                <tr>
                                    <td style="background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;padding:18px 20px;">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding-bottom:10px;border-bottom:1px solid #e2e8f0;">
                                                    <p style="margin:0;font-size:12px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Email cũ</p>
                                                    <p style="margin:4px 0 0;font-size:15px;color:#374151;font-weight:500;">{{ $oldEmail }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-top:10px;">
                                                    <p style="margin:0;font-size:12px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Email mới</p>
                                                    <p style="margin:4px 0 0;font-size:15px;color:#01b4e4;font-weight:600;">{{ $newEmail }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- If you did this -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:16px;">
                                <tr>
                                    <td style="background:#f0fdf4;border-radius:10px;border-left:4px solid #22c55e;padding:14px 16px;">
                                        <p style="margin:0;font-size:14px;font-weight:600;color:#166534;">✓ Nếu bạn đã thực hiện thay đổi này</p>
                                        <p style="margin:6px 0 0;font-size:13px;color:#15803d;line-height:1.5;">
                                            Bạn có thể bỏ qua email này. Hãy kiểm tra hộp thư của email mới
                                            <strong>({{ $newEmail }})</strong> để hoàn tất xác thực tài khoản.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- If you didn't do this -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:32px;">
                                <tr>
                                    <td style="background:#fff1f2;border-radius:10px;border-left:4px solid #f43f5e;padding:14px 16px;">
                                        <p style="margin:0;font-size:14px;font-weight:600;color:#881337;">⚠ Nếu không phải bạn thực hiện</p>
                                        <p style="margin:6px 0 0;font-size:13px;color:#9f1239;line-height:1.5;">
                                            Tài khoản của bạn có thể đã bị xâm phạm. Hãy đặt lại mật khẩu ngay lập tức
                                            và liên hệ chúng tôi nếu cần hỗ trợ.
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
                                Email này được gửi đến <strong>{{ $oldEmail }}</strong> vì đây là địa chỉ email cũ của tài khoản.<br>
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
