class YouTube {
  constructor(apiKey) {
    this.apiKey = apiKey;
    this.loaded = false;
  }

  load() {
    return new Promise((resolve) => {
      gapi.load('client', () => {
        gapi.client.init({
          apiKey:        this.apiKey,
          discoveryDocs: ['https://www.googleapis.com/discovery/v1/apis/youtube/v3/rest']
        }).then(() => {
          this.loaded = true;
          resolve();
        });
      });
    });
  }

  /**
   *
   * @param q
   * @param limit
   * @param {string} order date, rating, relevance, title, videoCount, viewCount
   * @returns {*}
   */
  search(q, limit = 24, order = 'relevance') {
    if (!this.loaded) {
      return this.load().then(() => {
        return this.search(q, limit);
      });
    }

    return new Promise((resolve, reject) => {
      gapi.client.youtube.search.list({
        q,
        order,
        part:            'snippet',
        type:            'video',
        maxResults:      limit,
        videoEmbeddable: true
      }).then((resp) => {
        resolve(resp.result.items);
      }, (error) => {
        reject(new Error(error));
      });
    });
  }
}

export default new YouTube('AIzaSyAQObmMYolUyImDomBRDzn76vu85wsWjIw');
