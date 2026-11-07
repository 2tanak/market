class MyUploadAdapter {
    constructor(loader) {
        this.loader = loader;
    }
    upload() {
        return this.loader.file.then(
            (file) =>
                new Promise((resolve, reject) => {
                    const data = new FormData();
                    data.append("upload", file);
                    fetch("/admin/editor-image-temp-upload", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN":
                                document
                                    .querySelector('meta[name="csrf-token"]')
                                    ?.getAttribute("content") ||
                                "{{ csrf_token() }}", // Если csrf_token() тоже внутри JS файла, лучше брать его из мета-тега!
                        },
                        body: data,
                    })
                        .then((response) => response.json())
                        .then((res) =>
                            res.url
                                ? resolve({ default: res.url })
                                : reject(res.error || "Ошибка загрузки"),
                        )
                        .catch((err) => reject("Ошибка сети: " + err.message));
                }),
        );
    }
    abort() {}
}

function MyCustomUploadAdapterPlugin(editor) {
    editor.plugins.get("FileRepository").createUploadAdapter = (loader) => {
        return new MyUploadAdapter(loader);
    };
}

// 2. Инициализация под Superbuild с жестким отключением ВСЕХ коммерческих плагинов
document.addEventListener("DOMContentLoaded", () => {
    if (typeof CKEDITOR !== "undefined") {
        CKEDITOR.ClassicEditor.create(document.querySelector("#editor"), {
            // Панель инструментов (только бесплатные базовые кнопки)
            toolbar: [
                "undo",
                "redo",
                "|",
                "bold",
                "italic",
                "|",
                "uploadImage",
                "link",
                "bulletedList",
                "numberedList",
            ],

            // Подключаем ваш AJAX-загрузчик файлов
            extraPlugins: [MyCustomUploadAdapterPlugin],

            // Отключаем коммерческие плагины, включая многоуровневые списки
            removePlugins: [
                "MultiLevelList", // <-- ПРЯМОЙ ФИКС ТЕКУЩЕЙ ОШИБКИ
                "Mention",
                "Emoji",
                "ExportPdf",
                "ExportWord",
                "AIAssistant",
                "CKBox",
                "CKFinder",
                "EasyImage",
                "Base64UploadAdapter",
                "RealTimeCollaborativeComments",
                "RealTimeCollaborativeTrackChanges",
                "RealTimeCollaborativeRevisionHistory",
                "PresenceList",
                "Comments",
                "TrackChanges",
                "TrackChangesData",
                "RevisionHistory",
                "Pagination",
                "WProofreader",
                "MathType",
                "SlashCommand",
                "Template",
                "DocumentOutline",
                "FormatPainter",
                "TableOfContents",
                "PasteFromOfficeEnhanced",
                "CaseChange",
            ],

            // Настройка поведения картинок
            image: {
                toolbar: [
                    "imageStyle:inline",
                    "imageStyle:block",
                    "imageStyle:side",
                    "|",
                    "toggleImageCaption",
                    "imageTextAlternative",
                ],
            },
        }).catch((error) => {
            console.error("Ошибка инициализации CKEditor:", error);
        });
    } else {
        console.error(
            "Критическая ошибка: Глобальный объект CKEDITOR не найден!",
        );
    }
});
