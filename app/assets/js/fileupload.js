const form = document.getElementById('form');
const fileInputJson = document.getElementById('file_json');
const fileInputJpeg = document.getElementById('file_jpeg');
const butEnviar = document.getElementById('enviar');

butEnviar.addEventListener('click', function (e) {
    e.preventDefault();
    const formDataJson = new FormData();
    var selectedFiles = [...fileInputJson.files];
    selectedFiles.forEach((file) => {
        formDataJson.append('jsonFile[]', file);
    });
    sendJson(formDataJson);

    const formDataJpeg = new FormData();
    selectedFiles = [...fileInputJpeg.files];
    selectedFiles.forEach((file) => {
        formDataJpeg.append('jpegFile[]', file);
    });
    sendJpeg(formDataJpeg);
});

async function sendJson(formData) {
    return await fetch('http://localhost/api/bingo/upload/json', {
        method: 'POST',
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            return data;
        })
        .catch((e) => {
            alert('Não foi possível o envio:\nErro: ' + e);
        });
}

async function sendJpeg(formData) {
    return await fetch('http://localhost/api/bingo/upload/background', {
        method: 'POST',
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            return data;
        })
        .catch((e) => {
            alert('Não foi possível o envio:\nErro: ' + e);
        });
}
