{{--
    Markdown Simple Editor + @Mention (Tribute.js)
    Sử dụng: @include('partials.markdown-editor')
    Textarea cần có class "js-markdown-editor"
--}}
<link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
<link rel="stylesheet" href="https://unpkg.com/tributejs@5.1.3/dist/tribute.css">
<style>
    .EasyMDEContainer .CodeMirror {
        border-radius: 0 0 12px 12px;
        border-color: #e5e7eb;
        font-family: inherit;
        font-size: 15px;
        color: #374151;
        background-color: #ffffff;
        min-height: 120px !important;
        height: 120px;
    }
    .editor-toolbar {
        border-radius: 12px 12px 0 0;
        border-color: #e5e7eb;
        background-color: #f9fafb;
        display: flex;
        align-items: center;
        padding: 4px 8px;
    }
    .editor-toolbar button {
        color: #4b5563 !important;
        border-radius: 6px;
    }
    .editor-toolbar button.active, .editor-toolbar button:hover {
        background: #e5e7eb;
        border-color: transparent;
    }
    .editor-toolbar .char-counter {
        margin-left: auto;
        font-size: 12px;
        color: #9ca3af;
        pointer-events: none;
        padding: 0 6px;
        white-space: nowrap;
    }
    .EasyMDEContainer .editor-statusbar {
        display: none !important;
    }
    .editor-preview {
        background: #ffffff;
        border-radius: 0 0 12px 12px;
        font-family: inherit;
    }
    .EasyMDEContainer {
        margin-bottom: 0;
    }
    .md-editor-footer {
        display: flex;
        justify-content: flex-end;
        padding-top: 8px;
    }

    /* ── Tribute.js Mention Dropdown ── */
    .tribute-container {
        z-index: 9999;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,.1), 0 8px 10px -6px rgba(0,0,0,.1);
        overflow: hidden;
        max-height: 260px;
        overflow-y: auto;
    }
    .tribute-container ul {
        list-style: none;
        margin: 0;
        padding: 4px;
    }
    .tribute-container li {
        padding: 8px 12px;
        cursor: pointer;
        border-radius: 8px;
        transition: background 150ms;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        color: #374151;
    }
    .tribute-container li.highlight,
    .tribute-container li:hover {
        background: #f0f9ff;
        color: #0369a1;
    }
    .tribute-container .mention-avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        object-fit: cover;
        background: linear-gradient(135deg, #94a3b8, #64748b);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 700;
        font-size: 12px;
        flex-shrink: 0;
    }
    .tribute-container .mention-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }
    .tribute-container .mention-name {
        font-weight: 600;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function loadScript(src) {
            return new Promise((resolve, reject) => {
                if (document.querySelector('script[src="' + src + '"]')) {
                    // Already loading or loaded
                    const check = setInterval(() => {
                        if (src.includes('easymde') && window.EasyMDE) { clearInterval(check); resolve(); }
                        if (src.includes('tribute') && window.Tribute) { clearInterval(check); resolve(); }
                    }, 50);
                    return;
                }
                const s = document.createElement('script');
                s.src = src;
                s.async = true;
                s.addEventListener('load', resolve, { once: true });
                s.addEventListener('error', reject, { once: true });
                document.head.appendChild(s);
            });
        }

        async function initMarkdownEditors() {
            const textareas = document.querySelectorAll('textarea.js-markdown-editor');
            if (textareas.length === 0) return;

            await loadScript('https://unpkg.com/easymde/dist/easymde.min.js');
            await loadScript('https://unpkg.com/tributejs@5.1.3/dist/tribute.js');

            const EasyMDEClass = window.EasyMDE;
            const TributeClass = window.Tribute;
            if (!EasyMDEClass || !TributeClass) return;

            for (const el of textareas) {
                if (el.dataset.markdownInitialized === '1') continue;
                el.dataset.markdownInitialized = '1';

                const mde = new EasyMDEClass({
                    element: el,
                    autoDownloadFontAwesome: true,
                    spellChecker: false,
                    styleSelectedText: false,
                    status: false,
                    toolbar: [
                        {
                            name: "bold",
                            action: EasyMDEClass.toggleBold,
                            className: "fa fa-bold",
                            title: "In đậm",
                        },
                        {
                            name: "italic",
                            action: EasyMDEClass.toggleItalic,
                            className: "fa fa-italic",
                            title: "In nghiêng",
                        },
                        {
                            name: "strikethrough",
                            action: EasyMDEClass.toggleStrikethrough,
                            className: "fa fa-strikethrough",
                            title: "Gạch giữa",
                        },
                        {
                            name: "underline",
                            action: function customUnderline(editor){
                                var cm = editor.codemirror;
                                var selectedText = cm.getSelection();
                                var text = selectedText || "văn bản";
                                cm.replaceSelection("<u>" + text + "</u>");
                            },
                            className: "fa fa-underline",
                            title: "Gạch dưới",
                        }
                    ],
                    placeholder: el.getAttribute('placeholder') || "Nhập nội dung ở đây..."
                });

                // Inject char counter vào toolbar
                const toolbar = mde.gui.toolbar;
                const charSpan = document.createElement('span');
                charSpan.className = 'char-counter';
                charSpan.textContent = '0 ký tự';
                toolbar.appendChild(charSpan);

                // Cập nhật giá trị textarea + char counter mỗi khi nội dung thay đổi
                mde.codemirror.on("change", () => {
                    el.value = mde.value();
                    charSpan.textContent = mde.value().length + ' ký tự';
                });

                // ── Tribute.js @Mention trên CodeMirror ──
                let debounceTimer = null;
                const tribute = new TributeClass({
                    trigger: '@',
                    values: function(text, cb) {
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(() => {
                            if (!text || text.length < 1) { cb([]); return; }
                            fetch('/api/users/search?q=' + encodeURIComponent(text))
                                .then(r => r.json())
                                .then(users => cb(users))
                                .catch(() => cb([]));
                        }, 250);
                    },
                    lookup: 'key',
                    fillAttr: 'value',
                    selectTemplate: function(item) {
                        return '@' + item.original.value;
                    },
                    menuItemTemplate: function(item) {
                        const d = item.original;
                        const avatarHtml = d.avatar
                            ? '<div class="mention-avatar"><img src="' + d.avatar + '" alt=""></div>'
                            : '<div class="mention-avatar">' + d.initial + '</div>';
                        return avatarHtml + '<span class="mention-name">' + d.value + '</span>';
                    },
                    noMatchTemplate: function() {
                        return '<li style="padding:8px 12px;color:#9ca3af;font-size:13px;">Không tìm thấy thành viên</li>';
                    },
                    allowSpaces: true,
                    menuShowMinLength: 1,
                });

                // Attach Tribute to CodeMirror's input element
                const cmInputEl = mde.codemirror.getInputField();
                tribute.attach(cmInputEl);
            }
        }

        initMarkdownEditors();
    });
</script>
