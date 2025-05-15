document.addEventListener("DOMContentLoaded", function () {
    // Initialize all copy buttons
    document.querySelectorAll(".copy-button").forEach((button) => {
        button.addEventListener("click", function () {
            const orderId = this.getAttribute("data-order-id");
            const orderDataElement =
                this.parentElement.querySelector(".order-data");

            if (orderDataElement) {
                // Get the text and replace <br> with newlines
                const textToCopy = orderDataElement.value.replace(
                    /<br>/g,
                    "\n"
                );

                // Use the Clipboard API
                navigator.clipboard
                
                .writeText(textToCopy)
                // .then(() => {
                //     // Show success feedback (you can use a toast or alert)
                //     showToast('Dados copiados para a área de transferência!');
                // })
                .catch((err) => {
                    console.error("Falha ao copiar texto: ", err);
                    // Fallback for browsers that don't support Clipboard API
                    fallbackCopyText(textToCopy);
                });
            }
        });
    });
});

// Fallback method for older browsers
function fallbackCopyText(text) {
    const textarea = document.createElement("textarea");
    textarea.value = text;
    textarea.style.position = "fixed"; // Prevent scrolling to bottom
    document.body.appendChild(textarea);
    textarea.select();

    try {
        const successful = document.execCommand("copy");
        if (successful) {
            showToast("Dados copiados para a área de transferência!");
        } else {
            console.error("Falha ao copiar texto");
        }
    } catch (err) {
        console.error("Erro ao copiar texto: ", err);
    }

    document.body.removeChild(textarea);
}

// Function to show toast notification
function showToast(message) {
    // You can implement a toast notification here
    // For simplicity, we'll use alert for now
    alert(message);

    // If you're using Bootstrap toasts, you would do something like:
    // const toast = new bootstrap.Toast(document.getElementById('copyToast'));
    // document.getElementById('toastMessage').innerText = message;
    // toast.show();
}
