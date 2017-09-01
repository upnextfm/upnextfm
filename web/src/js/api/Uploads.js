class Uploads {

  upload(file) {
    const data = new FormData();
    data.append('file', file);
    const config = {
      method:      'POST',
      credentials: 'same-origin',
      body:        data
    };

    return fetch('/room/upload', config)
      .then((resp) => {
        if (!resp.ok) {
          throw new Error('Upload failed.');
        }
        return resp.text();
      });
  }
}

export default new Uploads();
