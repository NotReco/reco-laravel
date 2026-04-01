<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>

<body
    style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; background-color: #f3f4f6; margin: 0; padding: 0; color: #333333;">
    <div style="background-color: #f3f4f6; width: 100%; padding: 40px 0;">
        <table align="center" width="100%" cellpadding="0" cellspacing="0"
            style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <!-- Header -->
            <tr>
                <td style="background-color: #032541; padding: 30px; text-align: center;">
                    <img src="https://i.ibb.co/spHVCw36/logo-dark.png" alt="RecoDB"
                        style="height: 40px; display: block; margin: 0 auto;">
                </td>
            </tr>

            <!-- Body -->
            <tr>
                <td style="padding: 40px 30px;">
                    <h2 style="margin-top: 0; color: #111827; font-size: 20px;">Chào {{ $user->name }},</h2>

                    <p style="font-size: 15px; line-height: 1.6; color: #4b5563; margin-top: 20px;">
                        Bạn nhận được email này vì chúng tôi đã nhận được yêu cầu khôi phục lại mật khẩu cho tài khoản
                        của bạn.
                        Nếu bạn không thực hiện yêu cầu này, bạn có thể bỏ qua email này.
                    </p>

                    <div style="text-align: center; margin: 35px 0;">
                        <a href="{{ $url }}"
                            style="display: inline-block; background-color: #01b4e4; color: #ffffff; text-decoration: none; padding: 14px 28px; border-radius: 6px; font-weight: bold; font-size: 15px;">
                            KHÔI PHỤC MẬT KHẨU
                        </a>
                    </div>

                    <p style="font-size: 15px; line-height: 1.6; color: #4b5563;">
                        <strong>Thông tin tài khoản của bạn:</strong>
                    </p>
                    <ul
                        style="font-size: 15px; line-height: 1.6; color: #4b5563; padding-left: 20px; border-left: 2px solid #e5e7eb; margin-left: 0; list-style-type: none;">
                        <li style="margin-bottom: 5px;"><strong>Tên đăng nhập:</strong> {{ $user->name }}</li>
                        <li><strong>Email:</strong> {{ $user->email }}</li>
                    </ul>

                    <p style="font-size: 15px; line-height: 1.6; color: #4b5563; margin-top: 35px;">
                        Trân trọng,<br>
                        <strong>Đội ngũ RecoDB</strong>
                    </p>
                </td>
            </tr>

            <!-- Footer -->
            <tr>
                <td
                    style="background-color: #f9fafb; padding: 25px 30px; text-align: center; border-top: 1px solid #e5e7eb;">
                    <p style="margin: 0; font-size: 13px; color: #9ca3af;">
                        Bạn nhận được email này vì bạn là thành viên đã đăng ký trên hệ thống của RecoDB.
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
