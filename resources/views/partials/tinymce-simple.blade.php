{{--
    TinyMCE Simple Editor (dành cho Forum — giao diện người dùng)
    Sử dụng: @include('partials.tinymce-simple')
    Textarea cần có class "js-richtext-simple"
--}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function loadTinyMce() {
            if (window.tinymce) return Promise.resolve(window.tinymce);
            if (window.__tinymceLoadingPromise) return window.__tinymceLoadingPromise;
            window.__tinymceLoadingPromise = new Promise((resolve, reject) => {
                const existing = document.querySelector('script[data-tinymce-cdn]');
                if (existing) {
                    existing.addEventListener('load', () => resolve(window.tinymce), {
                        once: true
                    });
                    existing.addEventListener('error', reject, {
                        once: true
                    });
                    return;
                }
                const s = document.createElement('script');
                s.src = '/js/tinymce/tinymce.min.js';
                s.async = true;
                s.dataset.tinymceCdn = '1';
                s.addEventListener('load', () => resolve(window.tinymce), {
                    once: true
                });
                s.addEventListener('error', reject, {
                    once: true
                });
                document.head.appendChild(s);
            });
            return window.__tinymceLoadingPromise;
        }

        async function initSimpleEditors() {
            const textareas = document.querySelectorAll('textarea.js-richtext-simple');
            if (textareas.length === 0) return;

            const tinymce = await loadTinyMce();
            if (!tinymce) return;

            for (const el of textareas) {
                if (el.dataset.richtextInitialized === '1') continue;
                el.dataset.richtextInitialized = '1';

                const height = Number(el.dataset.richtextHeight || 300);

                tinymce.init({
                    target: el,
                    base_url: '/js/tinymce',
                    suffix: '.min',
                    license_key: 'gpl',
                    menubar: false,
                    statusbar: true,
                    height: Number.isFinite(height) ? height : 300,
                    language: 'vi',
                    plugins: 'lists link searchreplace',
                    toolbar: 'bold italic underline strikethrough | bullist numlist blockquote | link removeformat',
                    // Light skin for user-facing pages
                    skin: 'oxide',
                    content_css: 'default',
                    text_patterns: false,
                    paste_as_text: false,
                    paste_webkit_styles: 'none',
                    paste_retain_style_properties: '',
                    paste_remove_styles_if_webkit: true,
                    paste_remove_styles: true,
                    paste_strip_class_attributes: 'all',
                    invalid_elements: 'script,applet,form,input,textarea,button,select,style,meta,link,iframe,embed,object',
                    valid_elements: 'p,br,strong/b,em/i,u,s,strike,ul,ol,li,a[href|target|rel],blockquote,h2,h3,h4,span,div',
                    content_style: `
                    body {
                        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                        font-size: 15px;
                        line-height: 1.6;
                        color: #374151;
                        margin: 0;
                        padding: 10px 14px;
                    }
                    p { margin: 0; padding: 0; }
                    p + p { margin-top: 0.5em; }
                    blockquote {
                        border-left: 3px solid #0ea5e9;
                        margin: 8px 0;
                        padding: 4px 12px;
                        color: #6b7280;
                    }
                    .mce-content-body[data-mce-placeholder]:not(.mce-visualblocks)::before {
                        color: #9ca3af;
                        font-size: 15px;
                        line-height: 1.6;
                        margin: 0;
                        padding: 0;
                        padding-left: 15px;
                    }
                `,
                    elementpath: false,
                    branding: false,
                    promotion: false,
                    setup: (editor) => {
                        editor.on('init', () => {
                            const textarea = editor.getElement();
                            const form = textarea?.closest?.('form');

                            // Sync content to textarea on every change
                            // so browser validation sees the current content
                            editor.on('input keyup change SetContent', () => {
                                editor.save();
                            });

                            if (!form) return;
                            form.addEventListener('submit', () => {
                                editor.save();
                            }, {
                                capture: true
                            });

                            // Custom character counter in Vietnamese
                            const statusbar = editor.getContainer().querySelector('.tox-statusbar');
                            if (statusbar) {
                                statusbar.style.cssText = 'display:flex;align-items:center;justify-content:space-between;';
                                const counter = document.createElement('span');
                                counter.style.cssText = 'font-size:12px;color:#6b7280;padding:0 8px;order:-1;';
                                const updateCount = () => {
                                    const text = editor.getContent({ format: 'text' }).trim();
                                    counter.textContent = text.length + ' ký tự';
                                };
                                statusbar.insertBefore(counter, statusbar.firstChild);
                                updateCount();
                                editor.on('input keyup change SetContent', updateCount);
                            }
                        });
                    }
                });
            }
        }

        initSimpleEditors();
    });
</script>
