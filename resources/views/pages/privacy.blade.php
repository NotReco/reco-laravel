<x-app-layout>
    <x-slot:title>Chính sách bảo mật</x-slot:title>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
        <div class="mb-10 lg:mb-12 border-b border-gray-200 pb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Chính Sách Bảo Mật</h1>
            <p class="text-[13px] text-gray-500">Ngày có hiệu lực: 05/03/2026</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8 lg:gap-16">
            {{-- TOC Sidebar --}}
            <div class="lg:w-1/4 shrink-0">
                <div class="sticky top-24 bg-gray-50 p-5 rounded border border-gray-200">
                    <h3
                        class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4 border-b border-gray-200 pb-3">
                        Mục lục nội dung</h3>
                    <nav class="flex flex-col space-y-3 text-[14px]">
                        <a href="#thu-thap-thong-tin" class="text-gray-600 hover:text-black transition-colors">1. Thông
                            tin thu thập</a>
                        <a href="#su-dung-thong-tin" class="text-gray-600 hover:text-black transition-colors">2. Cách sử
                            dụng</a>
                        <a href="#chia-se-thong-tin" class="text-gray-600 hover:text-black transition-colors">3. Chia sẻ
                            thông tin</a>
                        <a href="#quyen-kiem-soat" class="text-gray-600 hover:text-black transition-colors">4. Quyền
                            kiểm soát</a>
                        <a href="#su-dung-cookie" class="text-gray-600 hover:text-black transition-colors">5. Sử dụng
                            Cookie</a>
                        <a href="#lien-he" class="text-gray-600 hover:text-black transition-colors">6. Liên hệ</a>
                    </nav>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="lg:w-3/4 pb-20">
                <div class="max-w-[750px] text-[15px] leading-[1.7] text-gray-800 space-y-12 text-justify">

                    <p>Chào mừng bạn đến với RecoDB! Việc bảo vệ quyền riêng tư của bạn là ưu tiên hàng đầu của chúng
                        tôi. Chính sách này giải thích cách chúng tôi thu thập, sử dụng và bảo vệ thông tin cá nhân của
                        bạn khi bạn sử dụng trang web đánh giá phim RecoDB.</p>

                    <section id="thu-thap-thong-tin" class="scroll-mt-24 space-y-4">
                        <h2 class="font-bold text-[16px] text-gray-900">1. Thông tin chúng tôi thu thập</h2>
                        <p>Khi bạn sử dụng RecoDB, chúng tôi có thể thu thập các loại thông tin sau:</p>
                        <ul class="list-disc pl-6 space-y-3">
                            <li class="pl-2">
                                <span class="font-semibold text-gray-900">Thông tin tài khoản:</span>
                                Khi bạn đăng ký, chúng tôi thu thập tên hiển thị, địa chỉ email và mật khẩu của bạn.
                            </li>
                            <li class="pl-2">
                                <span class="font-semibold text-gray-900">Dữ liệu tương tác:</span>
                                Nội dung các bài đánh giá (review), xếp hạng (rating) phim, bình luận và danh sách phim
                                yêu thích mà bạn tạo ra trên trang web.
                            </li>
                            <li class="pl-2">
                                <span class="font-semibold text-gray-900">Dữ liệu hệ thống tự động:</span>
                                Giống như nhiều trang web khác, chúng tôi tự động thu thập một số thông tin kỹ thuật cơ
                                bản như địa chỉ IP, loại trình duyệt và thời gian truy cập để duy trì hoạt động của hệ
                                thống.
                            </li>
                        </ul>
                    </section>

                    <section id="su-dung-thong-tin" class="scroll-mt-24 space-y-4">
                        <h2 class="font-bold text-[16px] text-gray-900">2. Cách chúng tôi sử dụng thông tin</h2>
                        <p>Chúng tôi sử dụng thông tin thu thập được nhằm mục đích:</p>
                        <ul class="list-disc pl-6 space-y-3">
                            <li class="pl-2">Tạo và quản lý tài khoản người dùng của bạn.</li>
                            <li class="pl-2">Hiển thị các bài đánh giá và xếp hạng phim của bạn cho cộng đồng.</li>
                            <li class="pl-2">Duy trì và cải thiện các tính năng kỹ thuật của trang web.</li>
                            <li class="pl-2">Ngăn chặn các hành vi spam hoặc vi phạm tiêu chuẩn cộng đồng.</li>
                        </ul>
                    </section>

                    <section id="chia-se-thong-tin" class="scroll-mt-24 space-y-4">
                        <h2 class="font-bold text-[16px] text-gray-900">3. Chia sẻ thông tin của bạn</h2>
                        <p>Chúng tôi cam kết không bán thông tin cá nhân của bạn cho bất kỳ bên thứ ba nào. Thông tin
                            của bạn chỉ được hiển thị công khai trên trang web (như tên người dùng và bài review) dựa
                            trên các tương tác bạn chủ động thực hiện.</p>
                        <p>Chúng tôi chỉ cung cấp thông tin cho cơ quan chức năng khi có yêu cầu bắt buộc về mặt pháp
                            lý.</p>
                    </section>

                    <section id="quyen-kiem-soat" class="scroll-mt-24 space-y-4">
                        <h2 class="font-bold text-[16px] text-gray-900">4. Quyền kiểm soát dữ liệu của bạn</h2>
                        <p>Bạn có toàn quyền đối với dữ liệu cá nhân của mình trên RecoDB. Cụ thể như sau:</p>
                        <ul class="list-disc pl-6 space-y-3">
                            <li class="pl-2">Bạn có thể xem, chỉnh sửa hoặc cập nhật thông tin cá nhân trong phần Cài
                                đặt tài khoản.</li>
                            <li class="pl-2">Bạn có quyền tự xóa các bài review, xếp hạng của mình bất kỳ lúc nào.
                            </li>
                            <li class="pl-2">Nếu muốn xóa hoàn toàn tài khoản và toàn bộ dữ liệu liên quan, bạn có thể
                                thực hiện thông qua trang Cài đặt hoặc liên hệ trực tiếp với ban quản trị.</li>
                        </ul>
                    </section>

                    <section id="su-dung-cookie" class="scroll-mt-24 space-y-4">
                        <h2 class="font-bold text-[16px] text-gray-900">5. Sử dụng Cookie</h2>
                        <p>RecoDB sử dụng Cookie (các tệp văn bản nhỏ lưu trên trình duyệt của bạn) chủ yếu để giữ cho
                            bạn luôn ở trạng thái đăng nhập khi chuyển đổi qua lại giữa các trang và ghi nhớ các tùy
                            chọn giao diện cơ bản.</p>
                        <p>Bạn có thể tắt Cookie trong trình duyệt, nhưng xin lưu ý điều này có thể làm ảnh hưởng đến
                            tính năng đăng nhập tự động của trang web.</p>
                    </section>

                    <section id="lien-he" class="scroll-mt-24 space-y-4">
                        <h2 class="font-bold text-[16px] text-gray-900">6. Liên hệ với chúng tôi</h2>
                        <p>Nếu bạn có bất kỳ câu hỏi nào về Chính sách bảo mật này hoặc về dữ liệu của mình, vui lòng
                            liên hệ với ban quản trị RecoDB qua email: <a href="mailto:ad.recodb@gmail.com"
                                class="text-blue-600 hover:text-blue-800 underline">ad.recodb@gmail.com</a>.</p>
                    </section>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
