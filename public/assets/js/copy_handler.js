document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".copy-button").forEach((button) => {
        button.addEventListener("click", function () {
            const orderDataElement =
                this.parentElement.querySelector(".order-data");

            if (orderDataElement) {
                // Substitui <br> por quebras de linha reais
                const textToCopy = orderDataElement.value.replace(
                    /<br\s*\/?>/gi,
                    "\n"
                );

                // Verifica se a API de clipboard está disponível e se o contexto é seguro
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard
                        .writeText(textToCopy)
                        .then(() => {
                            showToast("Dados copiados!");
                        })
                        .catch((err) => {
                            console.error("Erro Clipboard API: ", err);
                            fallbackCopyText(textToCopy);
                        });
                } else {
                    // Se não houver HTTPS ou API, usa o fallback
                    fallbackCopyText(textToCopy);
                }
            }
        });
    });
});

function fallbackCopyText(text) {
    const textarea = document.createElement("textarea");
    textarea.value = text;
    // Esconde o textarea para não pular a tela
    textarea.style.position = "fixed";
    textarea.style.left = "-9999px";
    textarea.style.top = "0";
    document.body.appendChild(textarea);
    textarea.focus();
    textarea.select();

    try {
        const successful = document.execCommand("copy");
        if (successful) {
            showToast("Dados copiados (via fallback)!");
        }
    } catch (err) {
        console.error("Erro crítico ao copiar: ", err);
    }
    document.body.removeChild(textarea);
}
