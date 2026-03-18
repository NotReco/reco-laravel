<x-app-layout>
    <x-slot:title>Điều khoản dịch vụ</x-slot:title>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
        <div class="mb-10 lg:mb-12 border-b border-gray-200 pb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Điều Khoản Dịch Vụ</h1>
            <p class="text-[13px] text-gray-500">Ngày cập nhật gần nhất: {{ date('d/m/Y') }}</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8 lg:gap-16">
            {{-- TOC Sidebar --}}
            <div class="lg:w-1/4 shrink-0">
                <div class="sticky top-24 bg-gray-50 p-5 rounded border border-gray-200">
                    <h3
                        class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4 border-b border-gray-200 pb-3">
                        Mục lục nội dung</h3>
                    <nav class="flex flex-col space-y-3 text-[14px]">
                        <a href="#tai-khoan" class="text-gray-600 hover:text-black transition-colors">1. Tài khoản thành
                            viên</a>
                        <a href="#quy-tac-cong-dong" class="text-gray-600 hover:text-black transition-colors">2. Quy tắc
                            cộng đồng</a>
                        <a href="#ban-quyen" class="text-gray-600 hover:text-black transition-colors">3. Bản quyền nội
                            dung</a>
                        <a href="#quyen-quan-tri" class="text-gray-600 hover:text-black transition-colors">4. Quyền quản
                            trị</a>
                        <a href="#gioi-han-trach-nhiem" class="text-gray-600 hover:text-black transition-colors">5. Giới
                            hạn trách nhiệm</a>
                        <a href="#cap-nhat-dieu-khoan" class="text-gray-600 hover:text-black transition-colors">6. Cập
                            nhật</a>
                    </nav>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="lg:w-3/4 pb-20">
                <div class="max-w-[750px] text-[15px] leading-[1.7] text-gray-800 space-y-12 text-justify">

                    <p>Chào mừng bạn đến với RecoDB! Khi truy cập và sử dụng trang web này (bao gồm việc tạo tài khoản,
                        đọc và viết đánh giá phim), bạn đồng ý tuân thủ các Điều khoản dịch vụ dưới đây. Nếu bạn không
                        đồng ý với bất kỳ điều khoản nào, vui lòng không sử dụng RecoDB.</p>

                    <section id="tai-khoan" class="scroll-mt-24 space-y-4">
                        <h2 class="font-bold text-[16px] text-gray-900">1. Tài khoản thành viên</h2>
                        <ul class="list-disc pl-6 space-y-3">
                            <li class="pl-2">Để sử dụng các tính năng tương tác như viết review hay chấm điểm phim,
                                bạn cần tạo một tài khoản trên hệ thống của chúng tôi.</li>
                            <li class="pl-2">Bạn cam kết cung cấp thông tin chính xác khi đăng ký và tự chịu trách
                                nhiệm bảo mật mật khẩu của mình khỏi mọi truy cập trái phép.</li>
                            <li class="pl-2">Mọi hoạt động được thực hiện dưới tài khoản của bạn sẽ do bạn hoàn toàn
                                chịu trách nhiệm trước quy định của trang web.</li>
                        </ul>
                    </section>

                    <section id="quy-tac-cong-dong" class="scroll-mt-24 space-y-4">
                        <h2 class="font-bold text-[16px] text-gray-900">2. Quy tắc cộng đồng và Nội dung người dùng</h2>
                        <p>RecoDB là một không gian mở để chia sẻ đam mê điện ảnh. Khi đăng tải bài đánh giá (review),
                            bình luận hoặc bất kỳ nội dung nào khác, bạn cam kết tuân thủ các nguyên tắc sau:</p>

                        <ul class="list-disc pl-6 space-y-3">
                            <li class="pl-2">
                                <span class="font-semibold text-gray-900">Tôn trọng người khác:</span>
                                Không đăng tải các nội dung lăng mạ, bôi nhọ, phân biệt chủng tộc, thù ghét hoặc quấy
                                rối thành viên khác trong cộng đồng.
                            </li>
                            <li class="pl-2">
                                <span class="font-semibold text-gray-900">Nội dung lành mạnh:</span>
                                Không chứa các từ ngữ tục tĩu, hình ảnh đồi trụy, bạo lực quá mức hoặc bất kỳ nội dung
                                nào vi phạm pháp luật hiện hành.
                            </li>
                            <li class="pl-2">
                                <span class="font-semibold text-gray-900">Không spam và phá hoại:</span>
                                Không đăng quảng cáo rác, không chèn các đường link độc hại, virus hoặc mã độc nhằm phá
                                hoại hệ thống trang web và trải nghiệm của người dùng khác.
                            </li>
                        </ul>
                    </section>

                    <section id="ban-quyen" class="scroll-mt-24 space-y-4">
                        <h2 class="font-bold text-[16px] text-gray-900">3. Bản quyền và Sở hữu nội dung</h2>
                        <ul class="list-disc pl-6 space-y-3">
                            <li class="pl-2">
                                <span class="font-semibold text-gray-900">Nội dung của bạn:</span>
                                Bạn giữ bản quyền đối với các bài review và bình luận do chính mình viết. Tuy nhiên, khi
                                đăng tải lên RecoDB, bạn cấp cho chúng tôi quyền hiển thị, sao chép và phân phối nội
                                dung đó trên nền tảng của chúng tôi một cách hoàn toàn miễn phí.
                            </li>
                            <li class="pl-2">
                                <span class="font-semibold text-gray-900">Tài sản của RecoDB:</span>
                                Giao diện, logo, mã nguồn và các tính năng của trang web thuộc quyền sở hữu trí tuệ của
                                đội ngũ RecoDB. Bạn không được sao chép, chỉnh sửa hoặc sử dụng chúng cho mục đích
                                thương mại khi chưa có sự cho phép bằng văn bản.
                            </li>
                        </ul>
                    </section>

                    <section id="quyen-quan-tri" class="scroll-mt-24 space-y-4">
                        <h2 class="font-bold text-[16px] text-gray-900">4. Quyền quản trị của RecoDB</h2>
                        <p>Nhằm duy trì một cộng đồng lành mạnh và sạch sẽ, ban quản trị RecoDB có toàn quyền (nhưng
                            không có nghĩa vụ bắt buộc) thực hiện các hành động sau mà không cần báo trước:</p>
                        <ul class="list-disc pl-6 space-y-3">
                            <li class="pl-2">Xóa, ẩn hoặc chỉnh sửa các bài review/bình luận vi phạm "Quy tắc cộng
                                đồng".</li>
                            <li class="pl-2">Khóa tạm thời hoặc xóa vĩnh viễn tài khoản của những người dùng cố tình
                                vi phạm Điều khoản dịch vụ nhiều lần.</li>
                        </ul>
                    </section>

                    <section id="gioi-han-trach-nhiem" class="scroll-mt-24 space-y-4">
                        <h2 class="font-bold text-[16px] text-gray-900">5. Giới hạn trách nhiệm</h2>
                        <ul class="list-disc pl-6 space-y-3">
                            <li class="pl-2">RecoDB cung cấp nền tảng chia sẻ đánh giá phim dưới dạng "có sẵn". Chúng
                                tôi không đảm bảo rằng trang web sẽ không bao giờ bị lỗi, gián đoạn hay mất mát dữ liệu
                                do sự cố ngoài ý muốn.</li>
                            <li class="pl-2">Chúng tôi không chịu trách nhiệm về tính chính xác trong các bài đánh giá
                                của người dùng. Ý kiến trong bài review là của cá nhân người viết, đại diện cho góc nhìn
                                chủ quan và không đại diện cho quan điểm chính thức của RecoDB.</li>
                            <li class="pl-2">RecoDB sẽ không chịu trách nhiệm pháp lý cho bất kỳ thiệt hại nào phát
                                sinh từ việc bạn sử dụng hoặc không thể sử dụng trang web.</li>
                        </ul>
                    </section>

                    <section id="cap-nhat-dieu-khoan" class="scroll-mt-24 space-y-4">
                        <h2 class="font-bold text-[16px] text-gray-900">6. Cập nhật điều khoản</h2>
                        <p>Chúng tôi có thể thay đổi Điều khoản dịch vụ này vào bất kỳ lúc nào để phù hợp với sự phát
                            triển của hệ thống cũng như quy định của pháp luật. Việc bạn tiếp tục sử dụng RecoDB sau khi
                            có cập nhật đồng nghĩa với việc bạn hiển nhiên chấp nhận các thay đổi đó từ thời điểm công
                            bố.</p>
                    </section>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
