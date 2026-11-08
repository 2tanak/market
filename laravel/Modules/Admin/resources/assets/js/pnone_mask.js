document.addEventListener('DOMContentLoaded', function () {
    // Выбираем только инпуты с классом phone
    const phoneInputs = document.querySelectorAll('input.phone');

    // Если ни одного инпута не найдено, выходим и не выполняем код дальше
    if (phoneInputs.length === 0) return;

    phoneInputs.forEach(function(input) {
        // Принудительно ограничиваем длину под маску
        input.setAttribute('maxlength', '18');

        function setCursorPosition(pos, elem) {
            if (elem.setSelectionRange) {
                elem.setSelectionRange(pos, pos);
            }
        }

        function mask(event) {
            let matrix = "+7 (___) ___-__-__",
                i = 0,
                def = matrix.replace(/\D/g, ""),
                val = this.value.replace(/\D/g, "");
            
            if (def.length >= val.length) val = def;
            
            this.value = matrix.replace(/./g, function(a) {
                return /[_\d]/.test(a) && i < val.length ? val.charAt(i++) : i < val.length ? a : "";
            });
            
            if (event.type === "blur") {
                if (this.value.length == 2) this.value = "";
            } else {
                let nextChar = this.value.indexOf("_");
                if (nextChar !== -1) {
                    setCursorPosition(nextChar, this);
                } else {
                    setCursorPosition(this.value.length, this);
                }
            }
        }

        input.addEventListener("input", mask, false);
        
        input.addEventListener("focus", function() {
            if (!this.value || this.value === "") {
                this.value = "+7 (";
            }
            // Прыгаем сразу внутрь скобок
            setTimeout(() => setCursorPosition(4, this), 10);
        }, false);
        
        input.addEventListener("click", function() {
            let nextChar = this.value.indexOf("_");
            if (this.value === "+7 (") {
                setCursorPosition(4, this);
            } else if (nextChar !== -1) {
                setCursorPosition(nextChar, this);
            }
        }, false);
    });
});
