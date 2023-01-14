export function getBlobFromFile(file) {
    return new Promise((resolve, reject) => {
        let reader = new FileReader();
        reader.readAsArrayBuffer(file);
        reader.onload = (e) => {
            let blob = new Blob([e.target.result], {
                type: "image/png",
            });
            resolve(URL.createObjectURL(blob));
        };
        reader.onerror = (e) => {
            reject(null);
        };
    });
}
