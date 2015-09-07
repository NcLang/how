#!/usr/bin/env python3.4
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.
#

import re
import locale
import time
from datetime import datetime
import urllib.request
import urllib.error
import xml.etree.ElementTree as ET
import sys
from termcolor import colored
import os
import os.path as path
import configparser

## Basic parameters
api="http://how.nl5.de/xml/"
local="~/.how/"
database="howdb.xml"
config="how.cfg"

local=os.path.expanduser(local)
DEFAULT_CONFIG = "[DEFAULT]\n# Update interval in hours\nUpdateInterval = 24\n# Look at how.nl5.de for available lists\nList = QA-default\n"

def pline():
    print("--------------------------------------------------")

def checkURL(url):
    try:
        response=urllib.request.urlopen(url,timeout=1)
        return True
    except urllib.error.URLError as err: pass
    return False

def howOld(file):
    ctime=path.getmtime(file)
    return time.time()-ctime

def downloadFile(url,fname):
    urllib.request.urlretrieve(url,fname)
                
def getRaw(url):
    response = urllib.request.urlopen(url)
    data = response.read()
    text = data.decode('utf-8')
    return text
    
def getDB(fname):
    tree = ET.parse(fname);
    root = tree.getroot()
    regexdb=[]

    for entry in root.findall("entry"):
        q=entry.find("question").text
        a=entry.find("answer").text
        regexdb.append({'q':q,'a':a})

    return regexdb

def tstamp():
    return datetime.now().strftime('%Y-%m-%d')

def formatAnswer(answer):
    lines=answer.splitlines()
    ret=""
    for line in lines:
        if line[0:2] == "- ":
            ret += colored(line[2:],"yellow")+"\n"
        else:
            ret += "\n"+line+"\n"
    return ret

def answer(question,db):
    ret = ""
    cnt=1
    for entry in db:
        try:
            test = re.match(entry['q'],question,re.I)
        except:
            test = False
        if test:
            #ret += "\n"+"A"+str(cnt)+": \n"
            ret += ""+formatAnswer(entry['a'])
            cnt += 1
    return ret

def usage():
    print("Usage: > how to ask a question?")

def main():
    
    ## Check, create, load config
    if not os.path.exists(local):
        os.makedirs(local)
    if not os.path.exists(local+config):
        with open(local+config, "w") as cfile:
            cfile.write(DEFAULT_CONFIG)
    cfg = configparser.SafeConfigParser()
    cfg.read(local+config)

    ## Download, update DB if necessary
    if os.path.isfile(local+database):
        if howOld(local+database) > 3600*int(cfg['DEFAULT']['UpdateInterval']) and int(cfg['DEFAULT']['UpdateInterval']) != 0:
            if checkURL(api):
                print("Updating local database ...");
                downloadFile(api+cfg['DEFAULT']['List'],local+database)
            else:
                print("Database is old but API is down. Try update later ...")
    else:
        if checkURL(api):
            print("Downloading database ...");
            downloadFile(api+cfg['DEFAULT']['List'],local+database)
        else:
            print("No database present and API is down. That's bad.")
            sys.exit(1)
    
    ## Forced DB update
    if len(sys.argv) > 1:
        if sys.argv[1] == "--update" or sys.argv[1] == "-u":
            print("Updating local database ...");
            downloadFile(api+cfg['DEFAULT']['List'],local+database)
            sys.exit(0)

    ## Parse database
    db = getDB(local+database)

    ## Get question
    question = " ".join(sys.argv[1:])
    question = question.strip()

    if question == "":
        print(colored("Yes?","red"))
        sys.exit(0)

    ## Find answer
    ans=answer(question,db)
    if ans != "":
        pline()
        print("Q: "+colored("How "+question+"?","green"))
        pline()
        print(ans)
        pline()
    else:
        print(colored("Didn't catch that ...","red"))


        
if __name__ == "__main__":
    main()
        
