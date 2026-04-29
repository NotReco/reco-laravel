<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Khôi phục mật khẩu — RecoDB</title>
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
                                Khôi phục mật khẩu
                            </h2>

                            <p style="margin:0 0 12px;font-size:15px;color:#3c4043;line-height:1.6;">
                                Xin chào <strong style="color:#1a1a2e;">{{ $user->name }}</strong>,
                            </p>

                            <p style="margin:0 0 28px;font-size:15px;color:#3c4043;line-height:1.6;">
                                Chúng tôi nhận được yêu cầu khôi phục mật khẩu cho tài khoản
                                <strong style="color:#1a1a2e;">{{ $user->email }}</strong>.
                                Nhấn vào nút bên dưới để đặt lại mật khẩu của bạn.
                            </p>

                            <!-- CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                                <tr>
                                    <td>
                                        <a href="{{ $url }}"
                                            style="display:inline-block;background:#01b4e4;color:#ffffff;text-decoration:none;padding:13px 28px;border-radius:8px;font-weight:600;font-size:15px;">
                                            Đặt lại mật khẩu
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Link fallback -->
                            <p style="margin:0 0 8px;font-size:13px;color:#80868b;line-height:1.5;">
                                Nếu nút không hoạt động, sao chép liên kết này vào trình duyệt:
                            </p>
                            <p style="margin:0 0 28px;font-size:13px;color:#01b4e4;word-break:break-all;line-height:1.5;">
                                {{ $url }}
                            </p>

                            <!-- Notes -->
                            <p style="margin:0 0 10px;font-size:14px;color:#5f6368;line-height:1.6;">
                                Liên kết này <strong style="color:#3c4043;">có hiệu lực trong 60 phút</strong>. Sau đó bạn cần gửi lại yêu cầu.
                            </p>

                            <p style="margin:0 0 32px;font-size:14px;color:#5f6368;line-height:1.6;">
                                Nếu bạn không yêu cầu khôi phục mật khẩu, hãy bỏ qua email này. Tài khoản của bạn vẫn an toàn.
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
