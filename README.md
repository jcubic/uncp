# UNsplash Cache Proxy

```
 _   _ _ __   ___ _ __  
| | | | '_ \ / __| '_ \ 
| |_| | | | | (__| |_) |
 \__,_|_| |_|\___| .__/ 
                 |_|
```

PHP file that act as proxy for Unsplash API. It uses SQLite database as cache.
You can use this file to use unsplash API with and not worry about rate limitig.
50 per hour for demo apps.

This project was created as helper for technical interviews Live Coding sessions.

## Usage

To use the script rename `config.json.sample` to `config.json` and add your Access
key. To get unsplash API key use [this link](https://unsplash.com/developers)
and register as developer. CACHE_TIME is number of minutes for cache (default 2 hours).

## License

Released under GPL v3 or later<br/>
Copyright (C) 2022 [Jakub T. Jankiewicz](https://jakub.jankiewicz.org)

