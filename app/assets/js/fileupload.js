const form = document.getElementById('form');
const fileInputJson = document.getElementById('file_json');
const fileInputJpegFr = document.getElementById('file_jpeg_fr');
const fileInputJpegBk = document.getElementById('file_jpeg_bk');
const inputSerie = document.getElementById('serie');
const inputFrom = document.getElementById('from_cart');
const inputTo = document.getElementById('to_cart');
const butCsvToJson = document.getElementById('csvtojson');
const butEnviar = document.getElementById('enviar');
const butGerarA5 = document.getElementById('gerar1');
const butGerarA4 = document.getElementById('gerar2');

butEnviar.addEventListener('click', async function (e) {
    e.preventDefault();
    const formDataJson = new FormData();
    var selectedFiles = [...fileInputJson.files];
    if (selectedFiles.length !== 0) {
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
    }

    const formDataJpegFr = new FormData();
    selectedFiles = [...fileInputJpegFr.files];
    if (selectedFiles.length !== 0) {
        selectedFiles.forEach((file) => {
            formDataJpegFr.append('jpegFile[]', file);
        });

        const jpegResult = await sendJpeg(formDataJpegFr);
    }
    const formDataJpegBk = new FormData();
    selectedFiles = [...fileInputJpegBk.files];
    if (selectedFiles.length !== 0) {
        selectedFiles.forEach((file) => {
            formDataJpegBk.append('jpegFile[]', file);
        });

        const jpegResult = await sendJpeg(formDataJpegBk);
    }
});

butGerarA5.addEventListener('click', async function (e) {
    e.preventDefault();
    if (inputSerie.value === '') {
        alert("Input field SERIE don't be empty!");
        return;
    }
    if (inputFrom.value === '' || parseInt(inputFrom.value) < 0) {
        inputFrom.value = 0;
    }
    if (inputTo.value === '' || parseInt(inputTo.value) < 0) {
        inputTo.value = 0;
    }
    if (typeof parseInt(inputFrom.value) !== 'number') {
        alert('Input field FROM must be a number!');
        return;
    }
    if (typeof parseInt(inputTo.value) !== 'number') {
        alert('Input field TO must be a number!');
        return;
    }
    if (parseInt(inputTo.value) < parseInt(inputFrom.value)) {
        alert("Input field TO don't be less them input field FROM!");
        return;
    }
    const result = await generatePdfA5(
        inputSerie.value,
        inputFrom.value,
        inputTo.value
    );
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

butGerarA4.addEventListener('click', async function (e) {
    e.preventDefault();
    if (inputSerie.value === '') {
        alert("Input field SERIE don't be empty!");
        return;
    }
    if (inputFrom.value === '' || parseInt(inputFrom.value) < 0) {
        inputFrom.value = 0;
    }
    if (inputTo.value === '' || parseInt(inputTo.value) < 0) {
        inputTo.value = 0;
    }
    if (typeof parseInt(inputFrom.value) !== 'number') {
        alert('Input field FROM must be a number!');
        return;
    }
    if (typeof parseInt(inputTo.value) !== 'number') {
        alert('Input field TO must be a number!');
        return;
    }
    if (parseInt(inputTo.value) < parseInt(inputFrom.value)) {
        alert("Input field TO don't be less them input field FROM!");
        return;
    }
    const result = await generatePdfA4(
        inputSerie.value,
        inputFrom.value,
        inputTo.value
    );
    console.log(result);
    if ('error' in result) {
        alert('Generate PDF fail: ' + result.msg);
    }

    if ('base64Pdf' in result) {
        var blobData = b64toBlob(result['base64Pdf'], 'application/pdf');
        var data = URL.createObjectURL(blobData);
        window.open(data);
    }
});

async function sendCsv(formData) {
    return await fetch('http://localhost/api/bingo/upload/csv', {
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

async function generatePdfA5(serie, fromCart, toCart) {
    const token =
        'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE3NDU0NjU2OTMsIm5iZiI6MTc0NTQ2NTY5MywiZXhwIjoxNzQ1NTUyMDkzLCJpc3MiOiJodHRwczpcL1wvb2F1dGguc2FvZnJhbmNpc2Nvc2NzLmNvbS5iciIsImF1ZCI6IkFQUC5ERVZFTlYuV1dQIiwic2NvcGUiOlsiYXBpXC9iaW5nb1wvY3JlYXRlXC9wb3N0IiwiYXBpXC9iaW5nb1wvdXBsb2FkXC9qc29uXC9wb3N0IiwiYXBpXC9iaW5nb1wvdXBsb2FkXC9iYWNrZ3JvdW5kXC9wb3N0IiwiYXBpXC9iaW5nb1wvcGRmXC9nZW5lcmF0ZVwvb25lX3NlcmllXC90d29fY2FydHNcLyNcLyNcLyNcL2dldCJdfQ.X8UBU19F1QnQL1euCCbMT0uMipbvdsvqnNTmBiUFyzxSiJOd4qV1HKSS9ErEVnMREstTNTpOumDRNlCXyASfH5LXJJUYf-4O-s1N1pc_ZN12XlxX6M3bgv3QNFcSzfAv9uSrtxMmaE22GX1ljmpfxSTmcYGfMG-WPImo920jEMoF5xCz_DdFwzaCJSR0hmgHY6vKrOMGqL8squs0aBc_DVf16REJtFvDoNnjRQ5o-xBwEjn6w7e8GKnIF4L5FV30EUojYAd1UUZGMbLviys057nZoTvISvDf2-84QoWCMMsTuyhcjtEtpYFC8LeZAXTVe2ujQZn1smA-sp0mc60x6g';
    const response = await fetch(
        'http://localhost/api/bingo/pdf/generate/one_serie/two_carts/one_side/A5/' +
            serie +
            '/' +
            fromCart +
            '/' +
            toCart,
        {
            method: 'GET',
            headers: {
                Authorization: `Bearer ${token}`,
            },
        }
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

async function generatePdfA4(serie, fromCart, toCart) {
    const token =
        'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE3NDU0NjU2OTMsIm5iZiI6MTc0NTQ2NTY5MywiZXhwIjoxNzQ1NTUyMDkzLCJpc3MiOiJodHRwczpcL1wvb2F1dGguc2FvZnJhbmNpc2Nvc2NzLmNvbS5iciIsImF1ZCI6IkFQUC5ERVZFTlYuV1dQIiwic2NvcGUiOlsiYXBpXC9iaW5nb1wvY3JlYXRlXC9wb3N0IiwiYXBpXC9iaW5nb1wvdXBsb2FkXC9qc29uXC9wb3N0IiwiYXBpXC9iaW5nb1wvdXBsb2FkXC9iYWNrZ3JvdW5kXC9wb3N0IiwiYXBpXC9iaW5nb1wvcGRmXC9nZW5lcmF0ZVwvb25lX3NlcmllXC90d29fY2FydHNcLyNcLyNcLyNcL2dldCJdfQ.X8UBU19F1QnQL1euCCbMT0uMipbvdsvqnNTmBiUFyzxSiJOd4qV1HKSS9ErEVnMREstTNTpOumDRNlCXyASfH5LXJJUYf-4O-s1N1pc_ZN12XlxX6M3bgv3QNFcSzfAv9uSrtxMmaE22GX1ljmpfxSTmcYGfMG-WPImo920jEMoF5xCz_DdFwzaCJSR0hmgHY6vKrOMGqL8squs0aBc_DVf16REJtFvDoNnjRQ5o-xBwEjn6w7e8GKnIF4L5FV30EUojYAd1UUZGMbLviys057nZoTvISvDf2-84QoWCMMsTuyhcjtEtpYFC8LeZAXTVe2ujQZn1smA-sp0mc60x6g';
    const response = await fetch(
        'http://localhost/api/bingo/pdf/generate/one_serie/two_carts/two_side/A4/' +
            serie +
            '/' +
            fromCart +
            '/' +
            toCart,
        {
            method: 'GET',
            headers: {
                Authorization: `Bearer ${token}`,
            },
        }
    );

    const contentType = response.headers.get('Content-Type');
    console.log(contentType);

    if (contentType.includes('application/json')) {
        return await response.json();
    }

    if (contentType.includes('application/pdf')) {
        const pdfBinary = await response.blob();
        const fileURL = URL.createObjectURL(pdfBinary);
        window.open(fileURL);
        return { pdf: 'ok' };
    }

    function desktopPdfPreview(pdfBase64, divPdf) {
        var objPdf = document.createElement('object');
        objPdf.style.width = '100%';
        objPdf.style.height = '99%';
        objPdf.type = 'application/pdf';
        objPdf.data = 'data:application/pdf;base64,' + pdfBase64;
        // $(divPdf).html('');
        // $(divPdf).append(objPdf);
        divPdf.innerHTML = '';
        divPdf.appendChild(objPdf);
    }
}
