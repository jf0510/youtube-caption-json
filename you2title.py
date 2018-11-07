import requests
import re
from sys import argv
try:
    from urllib.parse import urlparse, urlencode, parse_qsl
except ImportError:
    from urlparse import urlparse, parse_qsl

URL_BASE = "https://www.youtube.com/watch?v="
headers = {"Accept-Language": "en-US,en;q=0.5"}

def main():
    if argv[1].startswith('http'):
        queries = dict(parse_qsl(urlparse(argv[1]).query))
        video_id = queries.get('v')
        res = requests.get(URL_BASE + video_id, headers=headers)
    else:
        video_id = argv[1]
        res = requests.get(URL_BASE + video_id, headers=headers)

    html = res.text
    keyword1 = '<span id="eow-title" class="watch-title" dir="ltr" title="'
    keyword2 = '">'
    start = html.find(keyword1)
    end = html.find(keyword2, start)
    result = html[start:end]
    result = result.replace(keyword1, '')
    print(result)
    return

if __name__ == '__main__':
    main()