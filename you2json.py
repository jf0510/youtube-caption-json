try:
    from urllib.parse import urlparse, urlencode, parse_qsl
    from urllib.request import urlopen
    from urllib.error import HTTPError, URLError
    from http.client import HTTPException
except ImportError:
    from urlparse import urlparse, parse_qsl
    from urllib import urlencode
    from urllib2 import urlopen, HTTPError, URLError
    from httplib import HTTPException

from sys import argv
import sys
import xml.etree.ElementTree as ET
from collections import namedtuple
import re
import json


TRACK_URL = 'http://video.google.com/timedtext?%s'
LIST_URL = 'http://www.youtube.com/api/timedtext?%s'
TRACK_KEYS = 'id name lang_original lang_translated lang_default'

Track = namedtuple('Track', TRACK_KEYS)
Line = namedtuple('Line', 'start duration text')


def retrieve_caption(video_id, languages):
    """
    Fetch the first available track in a language list, convert it to srt and
    return the list of lines for a given youtube video_id.
    """
    track = get_track(video_id, languages)
    caption = convert_caption(track)
    
    return caption


def get_track(video_id, languages):
    """Return the first available track in a language list for a video."""
    tracks = get_track_list(video_id)
    for lang in languages:
        if lang in tracks:
            break
    else:
        return

    track = tracks[lang]
    url = TRACK_URL % urlencode({'name': track.name, 'lang': lang,
                                        'v': video_id})
    track = urlopen(url)

    return parse_track(track)


def get_track_list(video_id):
    """Return the list of available captions for a given youtube video_id."""
    url = LIST_URL % urlencode({'type': 'list', 'v': video_id})
    captions = {}
    try:
        data = urlopen(url)
        tree = ET.parse(data)
        for element in tree.iter('track'):
            lang = element.get('lang_code')
            fields = map(element.get, TRACK_KEYS.split())
            captions[lang] = Track(*fields)
    except (URLError, HTTPError, HTTPException) as err:
        print("Network error: Unable to retrieve %s\n%s" % (url, err))
        sys.exit(6)
    return captions


def parse_track(track):
    """Parse a track returned by youtube and return a list of lines."""
    lines = []

    tree = ET.parse(track)
    for element in tree.iter('text'):
        if not element.text:
            continue
        start = float(element.get('start'))
        # duration is sometimes unspecified
        duration = float(element.get('dur') or 0)
        text = element.text
        lines.append(Line(start, duration, text))

    return lines


def convert_caption(caption):
    """Convert each line in a caption to srt format and return a list."""
    if not caption:
        return
    # lines = []
    srt_list = []
    for num, line in enumerate(caption, 1):
        start, duration = line.start, line.duration
        if duration:
            end = start + duration  # duration of the line is specified
        else:
            if caption[num]:
                end = caption[num].start  # we use the next start if available
            else:
                end = start + 5  # last resort

        srt_list.append( {
                'index': num, 
                'text': line.text,
                'start_time': int(start*1000),
                'end_time': int(end*1000)
            } )
        
    return srt_list


def main():
    if argv[1].startswith('http'):
        queries = dict(parse_qsl(urlparse(argv[1]).query))
        video_id = queries.get('v')
        caption = retrieve_caption(video_id, ['en'])

    else:
        video_id = argv[1]
        caption = retrieve_caption(video_id, ['en'])

    if caption:
        print(video_id + '.json')
        open('json/' + video_id + '.json', 'w').write(json.dumps(caption, indent=2, sort_keys=True))
        #open('../../webPrograming/json/' + video_id + '.json', 'w').write(json.dumps(caption, indent=2, sort_keys=True))
        return

if __name__ == '__main__':
    main()