import "./bootstrap";

import Alpine from "alpinejs";
import collapse from "@alpinejs/collapse";

window.Alpine = Alpine;
Alpine.plugin(collapse);

Alpine.start();

function getCsrfToken() {
    return (
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") ?? ""
    );
}

function getArticleEditorUploadUrl() {
    return (
        document
            .querySelector('meta[name="article-editor-upload-url"]')
            ?.getAttribute("content")
            ?.trim() ?? ""
    );
}

async function uploadEditorFileToServer(file, uploadUrl) {
    const formData = new FormData();
    formData.append("file", file);
    const res = await fetch(uploadUrl, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": getCsrfToken(),
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
        },
        body: formData,
        credentials: "same-origin",
    });
    let data = {};
    try {
        data = await res.json();
    } catch {
        /* ignore */
    }
    if (!res.ok) {
        const msg =
            data.error ||
            data.message ||
            (data.errors && Object.values(data.errors)[0]?.[0]) ||
            "Upload thất bại";
        throw new Error(msg);
    }
    if (!data.location) {
        throw new Error("Máy chủ không trả về địa chỉ file.");
    }
    return data.location;
}

function loadTinyMce() {
    if (window.tinymce) return Promise.resolve(window.tinymce);

    if (window.__tinymceLoadingPromise) return window.__tinymceLoadingPromise;

    window.__tinymceLoadingPromise = new Promise((resolve, reject) => {
        const existing = document.querySelector("script[data-tinymce-cdn]");
        if (existing) {
            existing.addEventListener("load", () => resolve(window.tinymce), {
                once: true,
            });
            existing.addEventListener("error", reject, { once: true });
            return;
        }

        const s = document.createElement("script");
        s.src = "/js/tinymce/tinymce.min.js";
        s.async = true;
        s.dataset.tinymceCdn = "1";
        s.addEventListener("load", () => resolve(window.tinymce), {
            once: true,
        });
        s.addEventListener("error", reject, { once: true });
        document.head.appendChild(s);
    });

    return window.__tinymceLoadingPromise;
}

async function initRichTextEditors() {
    const textareas = Array.from(
        document.querySelectorAll("textarea.js-richtext"),
    );
    if (textareas.length === 0) return;

    const tinymce = await loadTinyMce();
    if (!tinymce) return;

    const uploadUrl = getArticleEditorUploadUrl();

    for (const el of textareas) {
        if (el.dataset.richtextInitialized === "1") continue;
        el.dataset.richtextInitialized = "1";

        const height = Number(el.dataset.richtextHeight || 520);

        /** @type {Record<string, unknown>} */
        const options = {
            target: el,
            base_url: "/js/tinymce",
            suffix: ".min",
            license_key: "gpl",
            menubar: false,
            height: Number.isFinite(height) ? height : 520,
            plugins: "lists link image media wordcount",
            toolbar:
                "undo redo | bold italic underline | styles | bullist numlist | link | image media | removeformat",
            skin: "oxide-dark",
            content_css: "dark",

            // Paste Cleanup Configuration
            paste_as_text: false, // Set to true to force plain text by default, but let's try just cleaning it first
            paste_data_images: true, // Allow pasting images directly
            paste_webkit_styles: "none", // Strip webkit styles
            paste_retain_style_properties: "", // Strip all inline styles on paste
            paste_remove_styles_if_webkit: true,
            paste_remove_styles: true,
            paste_strip_class_attributes: "all", // Strip class attributes
            invalid_elements:
                "script,applet,form,input,textarea,button,select,style,meta,link", // Block dangerous elements (DO NOT block iframe/embed because of video)

            style_formats_merge: false,
            style_formats: [
                {
                    title: "Định dạng khối",
                    items: [
                        { title: "Văn bản thường", format: "p" },
                        { title: "Tiêu đề chính (H2)", format: "h2" },
                        { title: "Tiêu đề phụ (H3)", format: "h3" },
                        { title: "Tiêu đề nhỏ (H4)", format: "h4" },
                        { title: "Trích dẫn", format: "blockquote" },
                    ],
                },
                {
                    title: "Kiểu chữ nội tuyến",
                    items: [
                        {
                            title: "Chú thích nhỏ",
                            inline: "small",
                            styles: { "font-size": "85%", color: "#888" },
                        },
                        {
                            title: "Chữ nhấn mạnh (Đỏ)",
                            inline: "span",
                            styles: { color: "#e11d48" },
                        },
                    ],
                },
            ],
            image_caption: true,
            media_live_embeds: true,
            relative_urls: false,
            convert_urls: true,
            setup: (editor) => {
                editor.on("init", () => {
                    const textarea = editor.getElement();
                    const form = textarea?.closest?.("form");
                    if (!form) return;
                    form.addEventListener(
                        "submit",
                        () => {
                            editor.save();
                        },
                        { capture: true },
                    );
                });
            },
        };

        if (uploadUrl) {
            options.automatic_uploads = true;
            options.images_upload_handler = async (blobInfo) => {
                const file = blobInfo.blob();
                const name = blobInfo.filename();
                const wrapped =
                    file instanceof File
                        ? file
                        : new File([file], name, { type: file.type });
                return uploadEditorFileToServer(wrapped, uploadUrl);
            };
            options.file_picker_types = "image media";
            options.file_picker_callback = (callback, _value, meta) => {
                const input = document.createElement("input");
                input.type = "file";
                input.accept =
                    meta.filetype === "image"
                        ? "image/jpeg,image/png,image/webp,image/gif"
                        : "video/mp4,video/webm,video/quicktime,.mp4,.webm,.mov";
                input.onchange = async () => {
                    const file = input.files?.[0];
                    if (!file) return;
                    try {
                        const url = await uploadEditorFileToServer(
                            file,
                            uploadUrl,
                        );
                        if (meta.filetype === "image") {
                            callback(url, { alt: file.name });
                        } else {
                            callback(url);
                        }
                    } catch (err) {
                        alert(err?.message || "Upload thất bại");
                    }
                };
                input.click();
            };
        }

        tinymce.init(options);
    }
}

function bindTinyMceFormSync() {
    document.querySelectorAll("form").forEach((form) => {
        if (!form.querySelector("textarea.js-richtext")) return;
        form.addEventListener(
            "submit",
            () => {
                if (window.tinymce?.triggerSave) {
                    window.tinymce.triggerSave();
                }
            },
            { capture: true },
        );
    });
}

document.addEventListener("DOMContentLoaded", () => {
    initRichTextEditors();
    bindTinyMceFormSync();
});
