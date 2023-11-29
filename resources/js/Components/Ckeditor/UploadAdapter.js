class MyUploadAdapter {
    constructor(loader, url) {
        // The file loader instance to use during the upload.
        this.loader = loader;
        this.url = url;
    }

    // Starts the upload process.
    upload() {
        return this.loader.file
            .then(file => {
                return new Promise((resolve, reject) => {

                    const data = new FormData();
                    data.append('upload', file);

                    axios.post(this.url, data, {})
                        .then(response => {
                            if (response.data.status == 'success') {
                                resolve(response.data.urls);
                            } else {
                                reject(response.message);
                            }
                        }).catch(error => {
                        reject(error.response.data.message);
                    });
                })
            });
    }

    // Aborts the upload process.
    abort() {
        if (this.xhr) {
            this.xhr.abort();
        }
    }
}

function UploadAdapterPlugin(editor) {
    editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
        // Configure the URL to the upload script in your back-end here!
        return new MyUploadAdapter(loader, '/files/image/upload');
    };
}

export default UploadAdapterPlugin;
