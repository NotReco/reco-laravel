<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>RecoDB</title>
</head>
<body style="margin:0;padding:0;background-color:#f1f5f9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;">

    <!-- Outer wrapper -->
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9;padding:40px 16px;">
        <tr>
            <td align="center">

                <!-- Card -->
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:580px;margin:0 auto;">

                    <!-- Header -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#032541 0%,#0d3b6e 100%);border-radius:16px 16px 0 0;padding:32px 40px;text-align:center;">
                            <img src="https://i.ibb.co/ynjxvNhx/logo-dark.jpg" alt="RecoDB"
                                style="height:38px;display:inline-block;margin:0 auto;">
                            <div style="margin-top:14px;width:40px;height:3px;background:#01b4e4;border-radius:2px;margin-left:auto;margin-right:auto;"></div>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="background:#ffffff;padding:40px 40px 32px;">
                            {{ $slot }}
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f8fafc;border-top:1px solid #e2e8f0;border-radius:0 0 16px 16px;padding:24px 40px;text-align:center;">
                            <p style="margin:0 0 6px;font-size:12px;color:#94a3b8;">
                                Bạn nhận được email này vì tài khoản của bạn đã được đăng ký trên <strong style="color:#64748b;">RecoDB</strong>.
                            </p>
                            <p style="margin:0;font-size:11px;color:#cbd5e1;">
                                © {{ date('Y') }} RecoDB · Đã đăng ký bản quyền
                            </p>
                        </td>
                    </tr>

                </table>
                <!-- End Card -->

            </td>
        </tr>
    </table>

</body>
</html>
