export function extractQueryParam(query, param) {
  const params = {};
  query.split('&').forEach((kv) => {
    kv = kv.split('=');
    params[kv[0]] = kv[1];
  });

  return params[param];
}

export function parseProviderLink(url) {
  if (typeof url !== 'string') {
    return {
      codename: null,
      provider: null
    };
  }

  url = url.trim();
  url = url.replace('feature=player_embedded&', '');

  if (url.indexOf('rtmp://') === 0) {
    return {
      codename: url,
      provider: 'rtmp'
    };
  }

  let m;
  if ((m = url.match(/youtube\.com\/watch\?([^#]+)/))) {
    return {
      codename: extractQueryParam(m[1], 'v'),
      type:     'youtube'
    };
  }

  if ((m = url.match(/youtu\.be\/([^\?&#]+)/))) {
    return {
      codename: m[1],
      provider: 'youtube'
    };
  }

  if ((m = url.match(/youtube\.com\/playlist\?([^#]+)/))) {
    return {
      codename: extractQueryParam(m[1], 'list'),
      provider: 'youtube_playlist'
    };
  }

  if ((m = url.match(/twitch\.tv\/([^\?&#]+)/))) {
    return {
      codename: m[1],
      provider: 'twitch'
    };
  }

  if ((m = url.match(/livestream\.com\/([^\?&#]+)/))) {
    return {
      codename: m[1],
      provider: 'livestream'
    };
  }

  if ((m = url.match(/ustream\.tv\/([^\?&#]+)/))) {
    return {
      codename: m[1],
      provider: 'ustream'
    };
  }

  if ((m = url.match(/hitbox\.tv\/([^\?&#]+)/))) {
    return {
      codename: m[1],
      provider: 'hitbox'
    };
  }

  if ((m = url.match(/vimeo\.com\/([^\?&#]+)/))) {
    return {
      codename: m[1],
      provider: 'vimeo'
    };
  }

  if ((m = url.match(/dailymotion\.com\/video\/([^\?&#_]+)/))) {
    return {
      codename: m[1],
      provider: 'dailymotion'
    };
  }

  if ((m = url.match(/imgur\.com\/a\/([^\?&#]+)/))) {
    return {
      codename: m[1],
      provider: 'imgur'
    };
  }

  if ((m = url.match(/soundcloud\.com\/([^\?&#]+)/))) {
    return {
      codename: url,
      provider: 'soundcloud'
    };
  }

  if ((m = url.match(/(?:docs|drive)\.google\.com\/file\/d\/([^\/]*)/)) ||
    (m = url.match(/drive\.google\.com\/open\?id=([^&]*)/))) {
    return {
      codename: m[1],
      provider: 'google'
    };
  }

  /* Raw file */
  const tmp = url.split('?')[0];
  if (tmp.match(/^https?:\/\//)) {
    if (tmp.match(/\.(mp4|flv|webm|og[gv]|mp3|mov)$/)) {
      return {
        codename: url,
        provider: 'fi'
      };
    }
    throw new Error('ERROR_QUEUE_UNSUPPORTED_EXTENSION');
  }

  return {
    codename: null,
    provider: null
  };
}

export function formatSeconds(seconds) {
  const date = new Date(1970, 0, 1);
  date.setSeconds(seconds);
  return date.toTimeString().replace(/.*(\d{2}:\d{2}:\d{2}).*/, '$1');
}
