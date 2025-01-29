document.addEventListener('DOMContentLoaded', function () {
    const imageInput = document.getElementById('profile_image');
    const profilePreview = document.getElementById('profile-preview');

    imageInput.addEventListener('change', function (event) {
        const file = event.target.files[0];

        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                profilePreview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            alert('画像ファイルを選択してください。（JPEG, PNG, GIFのみ対応）');
        }
    });
});