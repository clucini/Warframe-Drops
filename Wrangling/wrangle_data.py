from bs4 import BeautifulSoup
import pandas as pd

raw_html = open("Wrangling/raw_html.html")
html = BeautifulSoup(raw_html, 'html.parser')

def getMissionData():
    df  = pd.DataFrame(columns=['Planet','Mission','Mission Type','Rotation','Item','Droprate','Droprate Category'])
    mission_table = html.find("h3",{"id":"missionRewards"}).findNext()
    curPlanet = ""
    curMission = ""
    curMissionType = ""
    curRot = ""
    for row in mission_table.findChildren(recursive=False):
        rw = row.text
        if rw != "" and rw.split()[0] != "Event:":
            if '/' in rw:
                curPlanet = rw.split("/")[0]
                curMission = rw.split("/")[1].split("(")[0]
                curMissionType = "(" + rw.split("/")[1].split("(")[1]
                curRot = "" 
            elif rw.split()[0] == "Rotation":
                curRot = rw.split()[1]
            else:
                r = row.select('td')
                item = r[0].text
                category = ' '.join(r[1].text.split()[:-1])
                percent = ''.join(filter(lambda x: x in '.0123456789', r[1].text.split()[-1:][0]))
                df = df.append(pd.DataFrame(columns=df.columns, data=[[curPlanet,curMission,curMissionType,curRot,item,percent,category]]))
    df = df.drop_duplicates()
    df.to_csv('Mission_data.csv',index=False)

def getRelicData():
    df  = pd.DataFrame(columns=['LongName', 'RelicTier',"Relic",'Tier','Item','Droprate','Droprate Category'])
    mission_table = html.find("h3",{"id":"relicRewards"}).findNext()
    curRelicTier = ""
    curRelic = ""
    curTier = ""
    curLong = ""
    for row in mission_table.findChildren(recursive=False):
        rw = row.text
        if rw != "":
            if '(' in rw and not '%' in rw:
                curLong = rw
                if curLong.split()[3] == "(Intact)":
                    curLong = ' '.join(curLong.split()[:-1])
                curRelicTier = rw.split()[0]
                curRelic = rw.split()[1]
                curTier = rw.split()[3][1:-1]
            else:
                r = row.select('td')
                item = r[0].text
                category = r[1].text.split()[0]
                percent = ''.join(filter(lambda x: x in '.0123456789', r[1].text.split()[1]))
                df = df.append(pd.DataFrame(columns=df.columns, data=[[curLong, curRelicTier,curRelic,curTier,item,percent,category]]))
    df.to_csv('Relic_data.csv',index=False)

getMissionData()