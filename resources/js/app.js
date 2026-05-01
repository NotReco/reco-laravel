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
            language: "vi",
            plugins:
                "code lists link image media wordcount fullscreen table searchreplace",
            toolbar: [
                "undo redo | bold italic underline strikethrough | styles",
                "alignleft aligncenter alignright alignjustify | bullist numlist | indent outdent",
                "link image media table | searchreplace | removeformat | fullscreen code",
            ].join(" | "),
            skin: "oxide-dark",
            content_css: "dark",

            // Tắt tính năng tự động chuyển đổi markdown (như gõ * biến thành bullet list)
            text_patterns: false,

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
                            title: "Chữ nhấn mạnh (Xanh)",
                            inline: "span",
                            styles: { color: "#0284c7" },
                        },
                    ],
                },
            ],
            image_caption: true,
            media_live_embeds: true,
            relative_urls: true,
            convert_urls: false,
            remove_script_host: false,
            paste_data_images: true,
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

                // Debug image insertion
                editor.on("NodeChange", (e) => {
                    if (e.element && e.element.tagName === "IMG") {
                        console.log("Image inserted:", e.element.src);
                    }
                });
            },
        };

        if (uploadUrl) {
            options.automatic_uploads = true;
            options.images_upload_handler = async (blobInfo, progress) => {
                try {
                    console.log("Starting upload for:", blobInfo.filename());
                    const file = blobInfo.blob();
                    const name = blobInfo.filename();
                    const wrapped =
                        file instanceof File
                            ? file
                            : new File([file], name, { type: file.type });
                    const url = await uploadEditorFileToServer(
                        wrapped,
                        uploadUrl,
                    );
                    console.log("Upload successful, URL:", url);
                    return url;
                } catch (err) {
                    console.error("TinyMCE upload error:", err);
                    throw err;
                }
            };
            options.file_picker_types = "image media";
            options.file_picker_callback = (callback, value, meta) => {
                console.log("File picker opened for:", meta.filetype);

                if (meta.filetype === "image") {
                    // Create a simple file input
                    const input = document.createElement("input");
                    input.setAttribute("type", "file");
                    input.setAttribute("accept", "image/*");

                    input.onchange = async () => {
                        const file = input.files[0];
                        if (!file) return;

                        try {
                            console.log("File selected:", file.name);
                            const url = await uploadEditorFileToServer(
                                file,
                                uploadUrl,
                            );
                            console.log("Upload successful, URL:", url);
                            console.log(
                                "Full image URL for verification:",
                                window.location.origin + url,
                            ); // Log full URL

                            // For image dialog, callback with the URL
                            // TinyMCE will only insert when user clicks "Save" in the dialog
                            callback(url, {
                                alt: file.name,
                                title: file.name,
                            });
                        } catch (err) {
                            console.error("File picker error:", err);
                            alert(err?.message || "Upload thất bại");
                        }
                    };

                    input.click();
                } else {
                    // Handle other file types (video, etc.)
                    const input = document.createElement("input");
                    input.type = "file";
                    input.accept =
                        "video/mp4,video/webm,video/quicktime,.mp4,.webm,.mov";
                    input.onchange = async () => {
                        const file = input.files?.[0];
                        if (!file) return;
                        try {
                            console.log("File selected:", file.name);
                            const url = await uploadEditorFileToServer(
                                file,
                                uploadUrl,
                            );
                            console.log("Upload successful, URL:", url);
                            callback(url);
                        } catch (err) {
                            console.error("File picker error:", err);
                            alert(err?.message || "Upload thất bại");
                        }
                    };
                    input.click();
                }
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
