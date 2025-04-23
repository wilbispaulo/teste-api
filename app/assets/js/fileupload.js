const form = document.getElementById('form');
const fileInputJson = document.getElementById('file_json');
const fileInputJpeg = document.getElementById('file_jpeg');
const inputSerie = document.getElementById('serie');
const butEnviar = document.getElementById('enviar');
const butGerar = document.getElementById('gerar');

butEnviar.addEventListener('click', async function (e) {
    e.preventDefault();
    const formDataJson = new FormData();
    var selectedFiles = [...fileInputJson.files];
    selectedFiles.forEach((file) => {
        formDataJson.append('jsonFile[]', file);
    });

    const jsonResult = await sendJson(formDataJson);

    var ok = true;
    for (const [key, values] of Object.entries(jsonResult.json)) {
        if (values === 'invalid_sign') {
            alert(`Invalid signature in ${key}...`);
            ok = false;
        }
    }
    if (ok) {
        alert('Json upload files success.');
    }

    const formDataJpeg = new FormData();
    selectedFiles = [...fileInputJpeg.files];
    selectedFiles.forEach((file) => {
        formDataJpeg.append('jpegFile[]', file);
    });

    const jpegResult = await sendJpeg(formDataJpeg);
});

butGerar.addEventListener('click', async function (e) {
    e.preventDefault();
    if (inputSerie.value === '') {
        alert("Input field serie don't be empty!");
        return;
    }
    const result = await generatePdf(inputSerie.value);
    // console.log(result);
    if ('error' in result) {
        alert('Generate PDF fail: ' + result.msg);
    }

    if ('base64Pdf' in result) {
        var blobData = b64toBlob(result['base64Pdf'], 'application/pdf');
        var data = URL.createObjectURL(blobData);
        window.open(data);
    }
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

async function generatePdf(serie) {
    const response = await fetch(
        'http://localhost/api/bingo/pdf/generate/one_serie/two_carts/' + serie
    );

    const contentType = response.headers.get('Content-Type');

    if (contentType.includes('application/json')) {
        return await response.json();
    }

    if (contentType.includes('application/pdf')) {
        const pdfBinary = await response.blob();
        const fileURL = URL.createObjectURL(pdfBinary);
        window.open(fileURL);
        return { pdf: 'ok' };
    }
}
