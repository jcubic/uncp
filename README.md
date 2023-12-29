# [UNsplash API Cache Proxy](https://github.com/jcubic/uncp)

```
 _   _ _ __   ___ _ __  
| | | | '_ \ / __| '_ \ 
| |_| | | | | (__| |_) |
 \__,_|_| |_|\___| .__/ 
                 |_|
```

PHP file that acts as a proxy for Unsplash API. It uses the SQLite database as a cache.
You can use this file to use Unsplash API and not worry about rate limiting.
50 per hour for demo apps.

This project was created as a helper for technical interviews and Live Coding sessions.

* [Check online demo](https://codepen.io/jcubic/pen/PoOYwER)
* [API example](https://unsplash.just.net.pl/?q=kitten&s=12)


## Usage

To use the script rename `config.json.sample` to `config.json` and add your Access
key. To get the Unsplash API key use [this link](https://unsplash.com/developers)
and register as a developer. CACHE_TIME is the number of minutes for cache (default 2 hours).

## License

Released under GPL v3 or later<br/>
Copyright (C) 2022 [Jakub T. Jankiewicz](https://jakub.jankiewicz.org)

