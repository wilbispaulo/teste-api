const form = document.getElementById('form');
const fileInput = document.getElementById('file');
const butEnviar = document.getElementById('enviar');

butEnviar.addEventListener('click', function (e) {
    e.preventDefault();
    const formData = new FormData();
    const selectedFiles = [...fileInput.files];
    selectedFiles.forEach((file) => {
        formData.append('jsonFile[]', file);
    });
    sendForm(formData);
});

async function sendForm(formData) {
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
