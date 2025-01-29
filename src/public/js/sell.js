document.addEventListener('DOMContentLoaded', function () {
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');

    imageInput.addEventListener('change', function (event) {
        imagePreview.innerHTML = '';

        const file = event.target.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();

            reader.onload = function (e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = '選択された画像';
                img.style.maxWidth = '100%';
                img.style.border = '1px solid #ddd';
                img.style.borderRadius = '5px';
                imagePreview.appendChild(img);
            };

            reader.readAsDataURL(file);
        }
    });
});