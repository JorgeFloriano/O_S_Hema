document.getElementById('files').addEventListener('change', function(event) {
    const container = document.getElementById('preview-container');
    container.innerHTML = ''; // Limpa as prévias anteriores

    const files = event.target.files;

    if (files.length > 9) {
        alert("Você pode selecionar no máximo 9 arquivos.");
        this.value = ""; // Limpa o input
        return;
    }

    Array.from(files).forEach(file => {
        const reader = new FileReader();

        reader.onload = function(e) {
            const div = document.createElement('div');
            div.style.position = 'relative';
            
            // Cria a miniatura
            if (file.type.includes('image')) {
                div.innerHTML = `
                    <img src="${e.target.result}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                `;
            } else {
                // Ícone para PDF
                div.innerHTML = `
                    <div class="img-thumbnail d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background: #eee;">
                        <i class="fa fa-file-pdf-o" style="font-size: 24px; color: red;"></i>
                    </div>
                `;
            }
            container.appendChild(div);
        }

        reader.readAsDataURL(file);
    });
});