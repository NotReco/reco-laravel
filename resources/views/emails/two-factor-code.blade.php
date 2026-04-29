<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Mã xác thực đăng nhập — RecoDB</title>
</head>
<body style="margin:0;padding:0;background-color:#f0f4f8;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0f4f8;padding:40px 16px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;margin:0 auto;background:#ffffff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.08),0 0 0 1px rgba(0,0,0,0.04);">

                    <!-- Logo Header -->
                    <tr>
                        <td style="padding:32px 40px 28px;border-bottom:1px solid #e8eaed;text-align:center;">
                            <img src="https://i.ibb.co/ynjxvNhx/logo-dark.jpg" alt="RecoDB"
                                style="height:32px;display:inline-block;margin:0 auto;">
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:36px 40px 28px;">

                            <h2 style="margin:0 0 16px;font-size:20px;font-weight:600;color:#1a1a2e;line-height:1.3;">
                                Mã xác thực đăng nhập
                            </h2>

                            <p style="margin:0 0 12px;font-size:15px;color:#3c4043;line-height:1.6;">
                                Xin chào <strong style="color:#1a1a2e;">{{ $notifiable->name }}</strong>,
                            </p>

                            <p style="margin:0 0 28px;font-size:15px;color:#3c4043;line-height:1.6;">
                                Đây là mã xác thực của bạn để đăng nhập vào <strong style="color:#1a1a2e;">RecoDB</strong>:
                            </p>

                            <!-- OTP Code -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                                <tr>
                                    <td align="center" style="background:#f8f9fa;border-radius:8px;padding:24px 16px;">
                                        <p style="margin:0 0 4px;font-size:11px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:#80868b;">
                                            Mã xác thực
                                        </p>
                                        <p style="margin:0;font-size:38px;font-weight:700;letter-spacing:10px;color:#032541;font-family:'Courier New',Courier,monospace;line-height:1.2;">
                                            {{ $notifiable->two_factor_code }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Notes -->
                            <p style="margin:0 0 10px;font-size:14px;color:#5f6368;line-height:1.6;">
                                Mã này <strong style="color:#3c4043;">có hiệu lực trong 10 phút</strong> và chỉ sử dụng được một lần. Vui lòng không chia sẻ mã này với bất kỳ ai.
                            </p>

                            <p style="margin:0 0 32px;font-size:14px;color:#5f6368;line-height:1.6;">
                                Nếu bạn không thực hiện đăng nhập này, hãy đổi mật khẩu ngay và liên hệ hỗ trợ.
                            </p>

                            <!-- Sign off -->
                            <p style="margin:0;font-size:15px;color:#3c4043;line-height:1.6;">
                                Trân trọng,<br>
                                <strong style="color:#1a1a2e;">Đội ngũ RecoDB</strong>
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding:20px 40px;border-top:1px solid #e8eaed;border-radius:0 0 12px 12px;">
                            <p style="margin:0;font-size:12px;color:#80868b;line-height:1.6;text-align:center;">
                                Bạn nhận được email này vì tài khoản đã được đăng ký trên RecoDB.<br>
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
